<?php
/**
 * Created by PhpStorm.
 * User: wangyingjie
 * Date: 2018/10/16
 * Time: 14:30
 */
namespace App\Http\Components\Helpers;
use App\Components\Helper\DataHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRulesConfig;
use App\Models\UserExt;

class ReviewHelper
{
    public $yearName = '年假';
    public $visitName = '探亲假';

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
        return [$message, $yearHolObj, $visitHolObj];
    }
    /**
     * 有权限则跳转到假期配置页,没有则在页面判断,显示联系管理员
     * @param $monthInfo
     * @return bool
     */
    public function errorRedirect($monthInfo)
    {
        if ($monthInfo[0] == 'error' && \Entrust::can(['holiday-config', 'holiday-config-all'])) {
            foreach ($monthInfo[1] as $message) {
                flash($message['message'], $message['sign']);
            }
            return true;
        }
        return false;
    }

    //是否全勤:应到天数等于实到 无影响全勤 迟到分钟数合计为0
    public function ifPresentAllDay($shouldCome, $actuallyCome, $affectFull, $user, $beLateNum)
    {
        $isFullWork = ( /*$shouldCome <= $actuallyCome&&*/
            empty($affectFull[$user->user_id]) &&
            (empty($beLateNum[$user->user_id]) || $beLateNum[$user->user_id] == 0)) ? '是' : '否';
        return $isFullWork;
    }

    /**
     * 计算各种带薪假的剩余天数
     * @param $user
     * @param array $obj
     * @return array
     */
    public function countWelfare($user, array $obj)
    {
        $ret = [];
        $arr = ['et' => $user->userExt->entry_time, 'id' => $user->user_id];
        foreach ($obj as $k => $v) {
            if(empty($v->cypher_type)) continue;
            $driver = HolidayConfig::$cypherTypeChar[$v->cypher_type];
            $ret[$k] = \AttendanceService::driver($driver, 'cypher')->getUserHoliday($arr['et'], $arr['id'], $v);
        }
        return $ret;
    }
    /*public function countWelfare($user, $obj)
    {
        $ret = [];
        $arr = ['et' => $user->userExt->entry_time, 'id' => $user->user_id];
        foreach ($obj as $k => $v) {
            if(empty($v->cypher_type)) continue;
            $driver = HolidayConfig::$cypherTypeChar[$v->cypher_type];
            $re = \AttendanceService::driver($driver, 'cypher')->getUserHoliday($arr['et'], $arr['id'], $v);
            $ret[$re['holiday_id']] = $re;
        }
        return $ret;
    }*/

    public function getHolidayConfigByCypherTypes(array $cypherTypes)
    {
        $holCon = [];
        foreach ($cypherTypes as $cypherType) {
            $holCon[$cypherType] = HolidayConfig::where('cypher_type', $cypherType)->get();
        }
        return $holCon;
    }

    /**
     * 获取实到天数
     * @param $startDate
     * @param $endDate
     * @param $user
     * @return int
     */
    public function countActuallyDays($startDate, $endDate, $user)
    {
        $userComeDay = UserExt::where('user_id', $user->user_id)->first(['entry_time'])->entry_time ?? NULL;
        $dailies = 0;
        if (!empty($userComeDay)) {
            $YmdUserComeDay = date('Y-m-d', strtotime($userComeDay));
            //月中入职的话,天数按实际出勤天数获取,否则默认出勤天数为24
            if (DataHelper::ifBetween(date('Y-m-01'), date('Y-m-t'), $YmdUserComeDay, 'r=')) {
                $dailies = DailyDetail::whereBetween('day', [$startDate, $endDate])->where('user_id', $user->user_id)->count();
            }elseif($YmdUserComeDay <= date('Y-m-01')) {
                $dailies = 24;
            }
        }
        /*$overDays = Leave::whereBetween('end_time', [$startDate, $endDate])->where('user_id', $user->user_id)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::SWITCH_REVIEW_ON])
            ->whereHas('holidayConfig', function ($query) {
                $query->where('cypher_type', HolidayConfig::CYPHER_OVERTIME);
            })->count();*/

        $leaves = Leave::whereBetween('end_time', [$startDate, $endDate])->where('user_id', $user->user_id)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::SWITCH_REVIEW_ON])
            ->whereHas('holidayConfig', function ($query) {
                $query->whereNotIn('cypher_type', [HolidayConfig::CYPHER_OVERTIME, HolidayConfig::CYPHER_NIGHT, HolidayConfig::CYPHER_HOUR]);
            })
            ->sum('number_day');
        return $dailies - $leaves < 0 ? 0 : $dailies - $leaves;
    }


    /**
     * 在前端显示迟到早退标红
     */
    public function getDanger($startDate, $endDate, $dailyDetailData)
    {
        $punchHelper = PunchHelper::getInstance($startDate, $endDate, true);
        $danger = [];

        foreach ($dailyDetailData as $daily) {
            $isDanger = ['on_work' => false, 'off_work' => false];
            if ($punchHelper->setFormulaCalPunRuleConf($daily->day)) {
                $leaveArr = json_decode($daily->leave_id, true);
                if (!empty($leaveArr)) {
                    $leaves = Leave::whereIn('leave_id', $leaveArr)->with('holidayConfig')->get();
                    $overtime = $leaves->map(function ($v) {
                        if ($v->holidayConfig->cypher_type == HolidayConfig::CYPHER_OVERTIME)
                            return $v->holidayConfig;
                        return NUll;
                    })->filter()->toArray();

                    if (isset($punchHelper->formulaCalPunRuleConf['if_rest']) && empty($overtime)) {
                        $danger[$daily->day] = $isDanger;continue;
                    }
                    if ($leaves->where('is_switch', '<>', Leave::NO_SWITCH)->count() != 0) {
                        foreach ($leaves as $leaf) {
                            if ($leaf->is_switch == Leave::LATE || $leaf->is_switch == Leave::LATE_ABSENTEEISM || $leaf->is_switch == Leave::ALLDAY_ABSENTEEISM) {
                                $isDanger['on_work'] = true;
                            }
                            if ($leaf->is_switch == Leave::EARLY || $leaf->is_switch == Leave::EARLY_ABSENTEEISM || $leaf->is_switch == Leave::ALLDAY_ABSENTEEISM) {
                                $isDanger['off_work'] = true;
                            }
                        }
                    }
                    if ($daily->deduction_num != 0) {
                        $isDanger['on_work'] = true;
                    }
                }
            }
            $danger[$daily->day] = $isDanger;
        }
        return ['danger' => $danger, 'event' => $punchHelper->events];
    }

    /**
     * @param $scopeArr
     * @return
     */
    public function getLeaves($scopeArr)
    {
        return Leave::where('start_time', '>=', date('Y-m-01', strtotime($scopeArr['start_time'])))
            ->where('end_time', '<=', date('Y-m-t', strtotime($scopeArr['end_time'])))
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::WAIT_REVIEW, Leave::ON_REVIEW, Leave::WAIT_EFFECTIVE, Leave::SWITCH_REVIEW_ON])->get();
    }

    public function filterLeaves($leaves, array $holidayIds, $user)
    {
        return $leaves->whereIn('holiday_id', $holidayIds)->where('user_id', $user->user_id)
            ->groupBy('holiday_id')->map(function ($value) {
                return $value->sum('number_day');
            })->toArray();
    }
}