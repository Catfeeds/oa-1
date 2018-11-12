<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/27
 * Time: 9:59
 */

namespace App\Http\Controllers\Attendance;

use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Http\Components\Helpers\PunchHelper;
use App\Http\Components\Helpers\ReviewHelper;
use App\Http\Components\ScopeAtt\DailyScope;
use App\Http\Components\ScopeAtt\LeaveScope;
use App\Http\Controllers\Controller;
use App\Models\Attendance\ConfirmAttendance;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRules;
use App\Models\UserHoliday;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class ReviewController extends AttController
{
    protected $scopeClass = DailyScope::class;
    public $reviewHelper;

    public function __construct(ReviewHelper $reviewHelper)
    {
        $this->reviewHelper = $reviewHelper;
    }

    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'attendance.leave.monthscope';

        $monthInfo = $this->dealAttendance($scope);
        if ($this->reviewHelper->errorRedirect($monthInfo)) return redirect()->route('holiday-config');

        $title = trans('att.考勤管理');
        return view('attendance.daily-detail.review', compact('title', 'monthInfo', 'scope', 'paidNames'));
    }

    /**
     * 返回查询后的信息
     * ['success', 数据的数组]或['error', '错误信息']
     * @param $scope
     * @param bool $cache
     * @return array
     */
    public function dealAttendance($scope, $cache = true)
    {
        list($message, $yearHolObj, $visitHolObj) = $this->reviewHelper->ifConfig();
        //判断配置,返回错误信息
        /*if (!empty($message)) {
            return ['error', $message];
        }*/

        list($year, $month) = explode('-', $scope->startDate);

        $holidayConfigArr = $this->reviewHelper->getHolidayConfigByCypherTypes(array_keys(HolidayConfig::$cypherTypeChar));

        $scopeArr = ['start_time' => $scope->startDate, 'end_time' => $scope->startDate];
        $startDate = date('Y-m-01', strtotime($scope->startDate));
        $endDate = date('Y-m-t', strtotime($scope->startDate));

        //该月应到天数:关联查找类型为正常工作的该月日历
        $shouldCome = Calendar::getShouldComeDays($year, $month);

        //迟到总分钟数
        $beLateNum = DailyDetail::getBeLateNum($year, $month);

        //合计扣分
        $deductNum = DailyDetail::getDeductNum($year, $month);

        //申请的假期中影响全勤的假期数
        $affectFull = Leave::noFull($year, $month);

        //获取用户对通知信息的状态
        $confirmStates = ConfirmAttendance::getConfirmState($year, $month);

        $users = User::whereRaw($scope->getwhere())->get();
        $info = [];

        foreach ($users as $user) {
            //计算带薪假,返回数组
            $hasSalary = empty($holidayConfigArr[HolidayConfig::CYPHER_PAID]) ? 0 : \AttendanceService::driver('paid', 'cypher')
                ->getDaysByScope($scopeArr, $user->user_id, $holidayConfigArr[HolidayConfig::CYPHER_PAID]);

            //计算无薪假,返回数组
            $hasNoSalary = empty($holidayConfigArr[HolidayConfig::CYPHER_UNPAID]) ? 0 : \AttendanceService::driver('unpaid', 'cypher')
                ->getDaysByScope($scopeArr, $user->user_id, $holidayConfigArr[HolidayConfig::CYPHER_UNPAID]);

            //返回[剩余调休, 已加班, 已调休]
            $leaveInfo = empty($holidayConfigArr[HolidayConfig::CYPHER_CHANGE][0]) ? 0 : \AttendanceService::driver('change', 'cypher')
                ->selectLeaveInfo($startDate, $endDate, $user->user_id, $holidayConfigArr[HolidayConfig::CYPHER_CHANGE][0]);

            //计算实到天数
            $actuallyCome = $this->reviewHelper->countActuallyDays($startDate, $endDate, $user);

            //判断全勤
            $isFullWork = $this->reviewHelper->ifPresentAllDay($shouldCome, $actuallyCome, $affectFull, $user, $beLateNum);

            $remainWelfare = $this->reviewHelper->countWelfare($user, [
                'year' => $yearHolObj, 'visit' => $visitHolObj,
            ]);

            $info["$user->user_id"] = [
                'date'                => "$year-$month",
                'user_name'           => $user->username,
                'user_id'             => $user->user_id,
                'user_alias'          => $user->alias,
                'user_dept'           => $user->dept->dept ?? '无',
                'should_come'         => empty($shouldCome) ? '请配置日历' : $shouldCome,
                'actually_come'       => $actuallyCome,
                'overtime'            => $leaveInfo['overTimeLeaveLog'],
                'change_time'         => $leaveInfo['changeLeaveLog'],
                'no_salary_leave'     => $hasNoSalary,
                'has_salary_leave'    => $hasSalary,
                'is_full_work'        => $isFullWork,
                'late_num'            => $beLateNum[$user->user_id] ?? 0,
                'other'               => '--',
                'deduct_num'          => $deductNum[$user->user_id] ?? 0,
                'remain_year_holiday' => $remainWelfare['year']['number_day'] ?? 0,
                'remain_visit'        => $remainWelfare['visit']['number_day'] ?? 0,
                'remain_change'       => $leaveInfo['userLeaveInfo'],
                'send'                => $confirmStates[$user->user_id] ?? 0,
            ];
        }
        return ['success', $info];
    }

    //点击发送时的处理
    public function send(Request $request)
    {
        $userId = ($request->user_id == 'all') ? 'all' : str_replace('send_', '', $request->user_id);
        //OperateLogHelper::sendWXMsg('sy0546', '上月考勤统计已出帐');
        if ($userId == 'all') {
            $userList = User::get(['user_id'])->pluck('user_id')->toArray();
        } else {
            $userList = [$userId];
        }

        Redis::del("att-$request->date");
        list($year, $month) = explode('-', $request->date);

        foreach ($userList as $u) {
            $obj = ConfirmAttendance::firstOrCreate([
                'user_id' => $u,
                'year'    => $year,
                'month'   => $month,
            ]);
            if ($obj->confirm == 0) {
                $obj->confirm = 1;
                $obj->save();
            }
        }
        return $userId == 'all' ? redirect()->back() : 'success';
    }

    /**
     * @param $year
     * @param $month
     * @param $cache `为true:取全部用户缓存;为id:取单个用户缓存
     * @param $scope `筛选缓存
     * @return array
     */
    public function getFromRedis($year, $month, $cache, $scope)
    {
        if (($info = unserialize(Redis::get("att-$year-$month")))) {
            if ($cache === true) {
                //对缓存进行条件筛选
                $infoAll = [];
                var_dump("使用所有用户缓存");
                foreach ($info as $k => $item) {
                    if ($item['user_id'] == ($scope->dailyUserId ?: true) &&
                        $item['user_alias'] == ($scope->dailyAlias ?: true) &&
                        $item['user_dept'] == (Dept::getDeptList()[$scope->dailyDept ?: 0] ?? true)
                    ) {
                        $infoAll[] = $item;
                    }
                }
                return ['success', $infoAll];
            } elseif (isset($info[$cache])) {
                var_dump("使用单个用户缓存");
                return ['success', [$info[$cache]]];
            }
        }
    }

    //明细
    public function reviewDetail($id)
    {
        $data = DailyDetail::where('user_id', $id)->orderBy('day', 'desc')->paginate(30);
        $userInfo['username'] = User::where('user_id', $id)->first()->username;
        $userInfo['alias'] = User::where('user_id', $id)->first()->alias;
        $title = "{$userInfo['username']}的考勤详情";
        return view('attendance.review.review-detail', compact('title', 'data', 'userInfo'));
    }
}