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
            if(empty($v->cypher_type)) continue;
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

        $countDays = $countDeducts = 0;
        foreach ($detailArr as $item) {
            $dayInfo = $this->countDay($item->punch_start_time, $item->punch_end_time, $calPunch[$item->day] ?? [], $item);
            $countDays = $countDays + $dayInfo['day'];
            $countDeducts = $countDeducts + $dayInfo['deduct'];
        }
//        return ['days' => $countDays, 'deducts' => $countDeducts];
        return $countDays;
    }

    /**
     * 计算一天的打卡时间在打卡规则中是多少天
     * @param string $punch_start 该天上班打卡时间
     * @param string $punch_end 该天下班打卡时间
     * @param array $punchRuleConfigs 该天对应的打卡规则对象数组
     * @param DailyDetail $dailyDetail 该天明细
     * @return array 返回当天被扣天数之后的剩余天数
     */
    public function countDay($punch_start, $punch_end, $punchRuleConfigs, $dailyDetail)
    {
        $deduct = 0;
        if (!empty($dailyDetail->leave_id)) {
            //请假情况的扣除规则
            $day_l = 0;
            $fromTo = [];
            $leaves = json_decode($dailyDetail->leave_id, true);
            $leaveObjects = Leave::whereIn('leave_id', $leaves)->get();
            foreach ($leaveObjects as $leaveObject) {
                $leaStartDate = date('Y-m-d', strtotime($leaveObject->start_time));
                $leaEndDate = date('Y-m-d', strtotime($leaveObject->end_time));
                //若这天是请半天的,累加请的天数
                if ($dailyDetail->day == $leaStartDate && strtotime($leaveObject->start_id) > strtotime('14:00')) {
                    $day_l = 0.5 + $day_l;
                    $fromTo = ['start' => NULL, 'end' => '14:00'];//这天可以打卡的范围缩小
                }elseif (($dailyDetail->day == $leaEndDate && strtotime($leaveObject->end_id) < strtotime('14:00'))) {
                    $day_l = 0.5 + $day_l;
                    $fromTo = ['start' => '14:00', 'end' => NULL];
                }else {
                    $day_l = 1; break;
                }
            }
            //大于等于1,证明一天都没来,不用经过上下班配置进行天数扣除
            if ($day_l >= 1)
                $day = 0;
            else {
                //小于1,限制打卡的计算范围,在此范围内先按正常扣天去扣, 再剔除里面包含请假天数的扣除
                $ps = $fromTo['start'] ?? $punch_start ?? NULL;
                $pe = $fromTo['end'] ?? $punch_end ?? NULL;
                $deduct = $this->getDeduct($ps, $pe, $punchRuleConfigs)[0] - $day_l;
                $day = (1 - $deduct) >= 0 ? 1 - $deduct : 0;
            }
        }else {
            //正常则按扣除规则
            $deduct = $this->getDeduct($punch_start, $punch_end, $punchRuleConfigs)[0];
            $day = (1 - $deduct) >= 0 ? 1 - $deduct : 0;
        }
        return ['day' => $day, 'deduct' => $deduct];
    }

    /**
     * 正常情况下 上下班时间与对应规则的匹配,进行扣除迟到或早退的时间
     * @param $punch_start
     * @param $punch_end
     * @param $punchRuleConfigs
     * @return array [扣除分数, 扣除的标记留给前端显示红色]
     */
    public function getDeduct($punch_start, $punch_end, $punchRuleConfigs)
    {
        $punchRuleConfArr = PunchRulesConfig::getPunchRules($punchRuleConfigs->toArray());
        $deduct = 0;
        $isDanger = ['on_work' => false, 'off_work' => false];
        if (!empty($punch_start) || !empty($punch_end)) {
            foreach ($punchRuleConfArr['sort'] as $key => $value) {//时间段
                list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);

                //上班时间对比各个时间段,若开始时间在该时间段之后或结束时间在该时间段之前都证明不在该段内,扣掉该段的时间差
                $ps = $punch_start ?? $ps ?? (strtotime($readyTime) <= strtotime($punch_end) ? $readyTime : NULL);
                $pe = $punch_end ?? $pe ?? (strtotime($endWorkTime) >= strtotime($punch_start) ? $endWorkTime : NULL);
                if (empty($ps) || empty($pe) || strtotime($ps) >= strtotime($endWorkTime) || strtotime($pe) <= strtotime($readyTime)) {
                    $deduct = $deduct + DataHelper::diffTime($readyTime, $endWorkTime);
                }

                foreach ($punchRuleConfArr['cfg'][$key]['ded_num'] as $item) {
                    //按照这个时间段的多个规则进行匹配扣除
                    if (!empty($punch_start) && strtotime($punch_start) >= strtotime($readyTime) + $item['start_gap']
                        && strtotime($punch_start) <= strtotime($readyTime) + $item['end_gap']
                    ) {
                        if ($item['late_type'] == 1) {
                            $deduct = $deduct + $item['ded_num'];
                            $isDanger['on_work'] = true;
                        }
                    }
                    if (!empty($punch_end) && strtotime($punch_end) >= strtotime($endWorkTime) - $item['end_gap']
                        && strtotime($punch_end) <= strtotime($endWorkTime) - $item['start_gap']) {
                        if ($item['late_type'] == 1) {
                            $deduct = $deduct + $item['ded_num'];
                            $isDanger['off_work'] = true;
                        }
                    }
                }
            }
        }else {
            $deduct = 1;
        }
        return [$deduct, $isDanger];
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

    /**
     * 获取上下班时间早退或迟到时的标记,用于在前端显示红色标明
     * @param $startDate
     * @param $endDate
     * @param array $dailyDetailData 打卡明细数组
     * @return array e.g: $danger['2018-10-10']['on_work' => true, 'off_work' => false]
     */
    public function getDanger($startDate, $endDate, $dailyDetailData)
    {
        $danger = [];
        $calPunch = $this->getCalendarPunchRules($startDate, $endDate);
        foreach ($dailyDetailData as $datum) {
            $danger[$datum->day] = $this->getDeduct($datum->punch_start_time, $datum->punch_end_time, $calPunch[$datum->day])[1];

            $leaveArr = json_decode($datum->leave_id, true);
            if (!empty($leaveArr)) {
                //这天若打卡在请假区间,false不显示红色
                $leaves = Leave::whereIn('leave_id', $leaveArr)->get();
                foreach ($leaves as $leaf) {
                    $leafStart = strtotime(date('Y-m-d', strtotime($leaf->start_time)).' '.$leaf->start_id);
                    $leafEnd = strtotime(date('Y-m-d', strtotime($leaf->end_time)).' '.$leaf->end_id);
                    $datumStart = strtotime($datum->day.' '.$datum->punch_start_time);
                    $datumEnd = strtotime($datum->day.' '.$datum->punch_end_time);
                    if ($datumStart >= $leafStart && $datumStart <= $leafEnd) {
                        $danger[$datum->day]['on_work'] = false; break;
                    }
                    if ($datumEnd >= $leafStart && $datumEnd <= $leafEnd) {
                        $danger[$datum->day]['off_work'] = false; break;
                    }
                }
            }
        }
        return $danger;
    }

}