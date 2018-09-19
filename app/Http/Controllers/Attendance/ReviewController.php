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
    public $yearName = '年假';
    public $visitName = '探亲假';

    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'attendance.leave.monthscope';

        $month = $this->dealAttendance($scope);
        if ($month[0] == 'error') {
            foreach ($month[1] as $message) {
                flash($message['message'], $message['sign']);
            }
            return redirect()->route('holiday-config');
        }
        $monthData = $month[1];

        $title = trans('att.考勤管理');
        return view('attendance.daily-detail.review', compact('title', 'monthData', 'scope'));
    }

    public function dealAttendance($scope, $cache = true)
    {
        //判断配置,返回错误信息
        list($message, $yearHolObj, $visitHolObj, $changeHolObj) = $this->ifConfig();
        if (!empty($message)) {
            return ['error', $message];
        }

        list($year, $month) = explode('-', $scope->startDate);

        /*//取缓存
        if (($info = $this->getFromRedis($year, $month, $cache, $scope))[0] == 'success') {
            return $info;
        }*/

        //该月应到天数:关联查找类型为正常工作的该月日历
        $shouldCome = Calendar::getShouldComeDays($year, $month);

        //实到天数
        $actuallyCome = DailyDetail::getNormalCome($year, $month);

        //迟到总分钟数
        $beLateNum = DailyDetail::getBeLateNum($year, $month);

        //合计扣分
        $deductNum = DailyDetail::getDeductNum($year, $month);

        //计算带薪假,返回数组
        $hasSalary = Leave::getSalaryLeaves($year, $month);

        //计算无薪假(请假),返回数组
        $hasNoSalary = Leave::getNoSalaryLeaves($year, $month);

        //申请的假期中影响全勤的假期数
        $affectFull = Leave::noFull($year, $month);

        //获取用户对通知信息的状态
        $confirmStates = ConfirmAttendance::getConfirmState($year, $month);

        //将'不设置','夜班加班调休','节假加班'纳入加班计算范围
        $overLeaIds = Leave::getLeavesIdByChangeTypes(
            [HolidayConfig::OVER_TIME, HolidayConfig::WEEK_WORK, HolidayConfig::NO_SETTING], $year, $month
        );

        //将'不设置','夜班加班','调休'纳入调休计算范围
        $changeLeaIds = Leave::getLeavesIdByChangeTypes(
            [HolidayConfig::WORK_CHANGE, HolidayConfig::OVER_TIME, HolidayConfig::NO_SETTING], $year, $month
        );

        $users = User::whereRaw($scope->getwhere())->get();
        $info = [];

        foreach ($users as $user) {
            //计算加班
            $overDays = AttendanceHelper::selectChangeInfo('', '', explode(',', $overLeaIds[$user->user_id] ?? ''));

            //计算调休
            $changeDays = AttendanceHelper::selectChangeInfo('', '', explode(',', $changeLeaIds[$user->user_id] ?? ''));

            //判断全勤
            $isFullWork = $this->ifPresentAllDay($shouldCome, $actuallyCome, $affectFull, $user, $beLateNum);

            //剩余年假
            $remainYear = AttendanceHelper::getUserYearHoliday($user->userExt->entry_time, $user->user_id,
                $yearHolObj);

            //剩余节日调休假
            $arr = AttendanceHelper::getUserChangeHoliday($user->user_id, $changeHolObj);
            $remainChange = $arr['change_work_day'] - $arr['change_use_day'];

            //剩余探亲假
            $remainVisit = AttendanceHelper::getUserYearHoliday($user->userExt->entry_time, $user->user_id,
                $visitHolObj);

            $info["$user->user_id"] = [
                'date'                => "$year-$month",
                'user_id'             => $user->user_id,
                'user_alias'          => $user->alias,
                'user_dept'           => $user->dept->dept ?? '无',
                'should_come'         => empty($shouldCome) ? '请配置日历' : $shouldCome,
                'actually_come'       => $actuallyCome[$user->user_id] ?? 0,
                'overtime'            => $overDays,
                'change_time'         => $changeDays,
                'no_salary_leave'     => $hasNoSalary[$user->user_id] ?? 0,
                'has_salary_leave'    => $hasSalary[$user->user_id] ?? 0,
                'is_full_work'        => $isFullWork,
                'late_num'            => $beLateNum[$user->user_id] ?? 0,
                'other'               => '--',
                'deduct_num'          => $deductNum[$user->user_id] ?? 0,
                'remain_year_holiday' => $remainYear,
                'remain_change'       => $remainChange,
                'remain_visit'        => $remainVisit,
                'send'                => $confirmStates[$user->user_id] ?? 0,
            ];
        }
        /*//将没有筛选的全部用户放入缓存
        if ($cache === true && $scope->getWhere() == '1 = 1') {
            Redis::setex("att-$year-$month", 100, serialize($info));
        }*/
        return ['success', $info];
    }

    //是否全勤:应到天数等于实到 无影响全勤 迟到分钟数合计为0
    public function ifPresentAllDay($shouldCome, $actuallyCome, $affectFull, $user, $beLateNum)
    {
        $isFullWork = ($shouldCome <= ($actuallyCome[$user->user_id] ?? '') &&
            !isset($affectFull[$user->user_id]) &&
            ($beLateNum[$user->user_id] ?? '') === '0') ? '是' : '否';
        return $isFullWork;
    }

    //点击发送时的处理
    public function send(Request $request)
    {
        $userId = ($request->user_id == 'all') ? 'all' : str_replace('send_', '', $request->user_id);
        //OperateLogHelper::sendWXMsg('sy0546', '上月考勤统计已出帐');
        if ($userId == 'all') {
            $userList = User::get(['user_id'])->pluck('user_id')->toArray();
        }else {
            $userList = [$userId];
        }

        Redis::del("att-$request->date");
        list($year, $month) = explode('-', $request->date);

        foreach ($userList as $u) {
            ConfirmAttendance::firstOrCreate([
                'user_id' => $u,
                'year'    => $year,
                'month'   => $month,
                'confirm' => 1,
            ]);
        }
        return $userId == 'all' ? redirect()->back() : 'success';
    }

    /**
     * @return array
     */
    public function ifConfig(): array
    {
        $message = [];
        if (!$yearHolObj = HolidayConfig::getObjByName($this->yearName)) {
            $message['年假'] = ['message' => "请添加或修改假期配置名称成: '$this->yearName'后再进行", 'sign' => 'danger'];
        }
        if (!$visitHolObj = HolidayConfig::getObjByName($this->visitName)) {
            $message['探亲'] = ['message' => "请添加或修改假期配置名称成: '$this->visitName'后再进行", 'sign' => 'danger'];
        }
        if (!HolidayConfig::where(['change_type' => HolidayConfig::WEEK_WORK])->first()) {
            $message['节假加班'] = ['message' => '请配置或修改"节假日加班",并勾选节假日加班选项', 'sign' => 'danger'];
        }
        if (!$changeHolObj = HolidayConfig::where(['change_type' => HolidayConfig::WORK_CHANGE])->first()) {
            $message['调休'] = ['message' => '请配置或修改"调休假",并勾选调休选项', 'sign' => 'danger'];
            return [$message, $yearHolObj, $visitHolObj, $changeHolObj];
        }
        return [$message, $yearHolObj, $visitHolObj, $changeHolObj];
    }

    /**
     * @param $year
     * @param $month
     * @param $cache //为true:取全部用户缓存;为id:取单个用户缓存
     * @param $scope //筛选缓存
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
}