<?php
/**
 * Created by PhpStorm.
 * User: wangyingjie
 * Date: 2018/10/16
 * Time: 14:30
 */
namespace App\Http\Components\Helpers;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRulesConfig;

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
        $isFullWork = ($shouldCome <= $actuallyCome &&
            !isset($affectFull[$user->user_id]) &&
            ($beLateNum[$user->user_id] ?? '') === '0') ? '是' : '否';
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
            $driver = HolidayConfig::$cypherTypeChar[$v->cypher_type];
            $ret[$k] = \AttendanceService::driver($driver, 'cypher')->getUserHoliday($arr['et'], $arr['id'], $v);
        }

        //加了多少天班就剩余调休就多几天
        $over = HolidayConfig::where('cypher_type', HolidayConfig::CYPHER_OVERTIME)->first();
        $overArr = AttendanceHelper::selectLeaveInfo(date('Y').'-01-01', date('Y').'-12-31', $user->user_id, $over);
        $ret['change']['number_day'] = $ret['change']['number_day'] + $overArr['apply_days'];

        return $ret;
    }

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
    public function countActuallyDays($startDate, $endDate, $user, $calPunch)
    {
        $detailArr = DailyDetail::where([
            ['day', '>=', $startDate], ['day', '<=', $endDate], ['user_id', $user->user_id]
        ])->get();

        $days = 0;
        foreach ($detailArr as $item) {
            $days = $days + $this->countDay($item->punch_start_time, $item->punch_end_time, $calPunch[$item->day] ?? [], $item);
        }
        return $days;
    }

    /**
     * 计算一天的打卡时间在打卡规则中是多少天
     * @param string $punch_start 该天上班打卡时间
     * @param string $punch_end 该天下班打卡时间
     * @param array $punchRuleConfigs 该天对应的打卡规则对象数组
     * @param DailyDetail $dailyDetail 该天明细
     * @return float|int 返回当天被扣天数之后的剩余天数
     */
    public function countDay($punch_start, $punch_end, $punchRuleConfigs, $dailyDetail)
    {
        $day = 1;//默认为1天,扣到0为止
        //当天明细含请假情况
        if (!empty($dailyDetail->leave_id)) {
            $day_l = 0;
            $leaves = json_decode($dailyDetail->leave_id, true);
            $leaveObjects = Leave::whereIn('leave_id', $leaves)->get();
            foreach ($leaveObjects as $leaveObject) {
                $leaStartDate = date('Y-m-d', strtotime($leaveObject->start_time));
                $leaEndDate = date('Y-m-d', strtotime($leaveObject->end_time));
                $test= PunchRulesConfig::getTodayNormalWorkTime($punchRuleConfigs);
                //若这天是请半天的,累加请的天数
                if (($dailyDetail->day == $leaStartDate && strtotime($leaveObject->start_id) > strtotime('14:00')) ||
                    ($dailyDetail->day == $leaEndDate && strtotime($leaveObject->end_id) < strtotime('14:00'))) {
                    $day_l = 0.5 + $day_l;
                }else {
                    $day_l = 1; break;
                }
            }
            //大于等于1,证明一天都没来,不用经过上下班配置进行天数扣除
            if ($day_l >= 1)
                $day = 0;
            else {
                //小于1,剩余天数就要经过上下班配置进行天数的判断扣除
                $deduct = $this->getDeduct($punch_start, $punch_end, $punchRuleConfigs);
                $day = ((1 - $day_l) - $deduct) <= 0 ? 0 : (1 - $day_l) - $deduct;
            }

        }else {
            //正常则按扣除规则
            $deduct = $this->getDeduct($punch_start, $punch_end, $punchRuleConfigs);
            $day = $day - $deduct;
        }
        return $day;
    }

    public function getDeduct($punch_start, $punch_end, $punchRuleConfigs)
    {
        $punchRuleConfArr = PunchRulesConfig::getPunchRules($punchRuleConfigs->toArray())['cfg'];
        $deduct = 0;
        if (!empty($punch_start) || !empty($punch_end)) {
            foreach ($punchRuleConfArr as $key => $value) {//时间段
                list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);
                foreach ($value['ded_num'] as $item) {//时间段中的规则
                    if (strtotime($punch_start) >= strtotime($readyTime) + $item['start_gap']
                        && strtotime($punch_start) <= strtotime($readyTime) + $item['end_gap']
                    ) {
                        if ($item['late_type'] == 1) {
                            $deduct = $deduct + $item['ded_num'];
                        }
                    }
                    if (strtotime($punch_end) >= strtotime($endWorkTime) - $item['end_gap']
                        && strtotime($punch_end) <= strtotime($endWorkTime) - $item['start_gap']) {
                        if ($item['late_type'] == 1) {
                            $deduct = $deduct + $item['ded_num'];
                        }
                    }
                }
            }
        }
        if (empty($punch_start)) $deduct = $deduct + 0.5;
        if (empty($punch_end)) $deduct = $deduct + 0.5;
        return $deduct;
    }

    /**
     * 连表获取日历对应的上下班配置, 以['year-month-day' => 上下班规则]
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getCalendarPunchRules($startDate, $endDate)
    {
        $calendarArr = Calendar::whereBetween(\DB::raw('UNIX_TIMESTAMP(CONCAT(year, "-", month, "-", day))'),
            [strtotime($startDate), strtotime($endDate)])
            ->with('punchRules')->get();
        $newCalendarArr = [];
        foreach ($calendarArr as $item) {
            $key = sprintf("%d-%02d-%02d", $item->year, $item->month, $item->day);
            $newCalendarArr[$key] = $item->punchRules->config;
        }
        return $newCalendarArr;
    }

}