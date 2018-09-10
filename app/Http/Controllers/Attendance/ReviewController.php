<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/27
 * Time: 9:59
 */

namespace App\Http\Controllers\Attendance;

use App\Components\Helper\DataHelper;
use App\Http\Components\ScopeAtt\DailyScope;
use App\Http\Components\ScopeAtt\LeaveScope;
use App\Http\Controllers\Controller;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRules;
use App\Models\UserHoliday;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReviewController extends AttController
{
    protected $scopeClass = DailyScope::class;
    public function index(Request $request)
    {
        $scope = $this->scope;
        $scope->block = 'attendance.leave.monthscope';
        $data = $this->dealAttendance($scope);
        $title = trans('att.考勤管理');
        return view('attendance.daily-detail.review', compact('title', 'data', 'scope'));
    }

    public function dealAttendance($scope){
        list($year, $month) = explode('-', $scope->startDate);

        //该月应到天数:关联查找类型为正常工作的该月日历
        $shouldCome = Calendar::getShouldComeDays($year, $month);

        //实际到的天数
        $actuallyCome = DailyDetail::getActuallyCome($year, $month);

        //迟到总分钟数
        $beLateNum = DailyDetail::getBeLateNum($year, $month);

        //合计扣分
        $deductNum = DailyDetail::getBeLateNum($year, $month);

        //计算带薪假,返回数组
        $hasSalary = $this->hasSalaryLeavesArray($year, $month);

        //计算无薪假,返回数组
        $diffTime = $this->NoSalaryLeavesArray($year, $month);

        //上下班打卡总次数
        $punch = DailyDetail::getSumPunch($year, $month);
        //上下班补打卡总次数
        $re = Leave::getReCheckSum($year, $month);

        //剩余年假
        $remainYear = $this->remain('年假');
        //剩余调休假
        $remainChange = $this->remain('调休假');
        //剩余探亲假
        $remainVisit = $this->remain('探亲假');

        $users = User::whereRaw($scope->getwhere())->get();
        $info = [];
        foreach ($users as $user){
            //判断全勤
            $isFullWork = $this->ifPresentAllDay($shouldCome, $actuallyCome, $user, $actuallyCome, $punch, $re);

            $info[] = [
                'date' => "$year-$month",
                'user_id' => $user->user_id,
                'user_alias' => $user->alias,
                'user_dept' => $user->dept->dept ?? '无',
                'should_come' => $shouldCome,
                'actually_come' => $actuallyCome[$user->user_id] ?? 0,
                'overtime' => $diffTime[$user->user_id][HolidayConfig::OVERTIME] ?? 0,
                'no_salary_leave' => $diffTime[$user->user_id][HolidayConfig::LEAVEID] ?? 0,
                'has_salary_leave' => $hasSalary[$user->user_id] ?? 0,
                'is_full_work' => $isFullWork,
                'late_num' => $beLateNum[$user->user_id] ?? 0,
                'other' => '--',
                'deduct_num' => $deductNum[$user->user_id] ?? 0,
                'remain_year_holiday' => $remainYear[$user->user_id] ?? 0,
                'remain_change' => $remainChange[$user->user_id] ?? 0,
                'remain_visit' => $remainVisit[$user->user_id] ?? 0,
                'detail' => '明细'
            ];
        }
        return $info;
    }

    //剩余带薪假天数
    public function remain($holidayName){
        $remain = UserHoliday::whereHas('holidayConfig', function ($query) use ($holidayName){
            $query->where('holiday', $holidayName);
        })
            ->get()->pluck('num', 'user_id')->toArray();
        return $remain;
    }

    public function timeDiff($object, $value){
        return $value+ DataHelper::diffTime(date('Y-m-d', strtotime($object->start_time)) . ' ' . Leave::$startId[$object->start_id],
            date('Y-m-d', strtotime($object->end_time)) . ' ' . Leave::$endId[$object->end_id]);
    }

    public function NoSalaryLeavesArray($year, $month){
        $leaveObjects = Leave::getNoSalaryLeaves($year, $month);
        $diffTime[][] = 0;
        //将不同用户不同类型的假期天数存在diffTime中
        foreach ($leaveObjects as $leaveObject){
            $diffTime[$leaveObject->user_id][$leaveObject->holidayConfig->apply_type_id] =
                $this->timeDiff($leaveObject, $diffTime[$leaveObject->user_id][$leaveObject->apply_type_id] ?? 0);
        }
        return $diffTime;
    }

    public function hasSalaryLeavesArray($year, $month){
        $hasSalaryLeaves = Leave::getSalaryLeaves($year, $month);
        $hasSalary = [];//以'用户id => 带薪天数'存放
        foreach ($hasSalaryLeaves as $hasSalaryLeaf){
            $hasSalary[$hasSalaryLeaf->user_id] = $this->timeDiff($hasSalaryLeaf, $hasSalary[$hasSalaryLeaf->user_id] ?? 0);
        }
        return $hasSalary;
    }

    //是否全勤:应到天数等于实到 无请假 迟到分钟数合计为0 上下班打卡与上下班补打卡的和等于应到天数*2
    public function ifPresentAllDay($shouldCome, $actuallyCome, $user, $beLateNum, $punch, $re){
        $isFullWork = ($shouldCome <= ($actuallyCome[$user->user_id] ?? '') &&
            !isset($diffTime[$user->user_id][HolidayConfig::LEAVEID]) &&
            ($beLateNum[$user->user_id] ?? '') === '0' &&
            ($punch[$user->user_id] ?? 0) + ($re[$user->user_id] ?? 0) >= $shouldCome * 2
        ) ? '是' : '否';
        return $isFullWork;
    }

}