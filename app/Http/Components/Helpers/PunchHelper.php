<?php
/**
 * Created by PhpStorm.
 * User: wangyingjie
 * Date: 2018/11/5
 * Time: 15:32
 */

namespace App\Http\Components\Helpers;


use App\Components\Helper\DataHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRules;
use App\Models\Sys\PunchRulesConfig;

class PunchHelper
{
    /**
     * 连表获取日历对应的上下班配置, 以['year-month-day' => 上下班规则]
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public static function getCalendarPunchRules($startDate, $endDate)
    {
        $calendarArr = Calendar::whereBetween(\DB::raw('UNIX_TIMESTAMP(CONCAT(year, "-", month, "-", day))'),
            [strtotime($startDate), strtotime($endDate) + 3600 * 12])
            ->with('punchRules')->get();
        $newCalendarArr = [];
        foreach ($calendarArr as $item) {
            $key = sprintf("%d-%02d-%02d", $item->year, $item->month, $item->day);
            $newCalendarArr[$key] = $item->punchRules->config;
        }
        return $newCalendarArr;
    }

    public function prPunchTime($punch_start, $punch_end, $formulaPunchRules)
    {
        $minPrEndPunch = '24:00';
        $maxPrStartPunch = '00:00';
        foreach ($formulaPunchRules['sort'] as $key => $value) {
            list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);
            $arrTimes = DataHelper::timesToNum($punch_start, $punch_end, $readyTime, $endWorkTime);

            if (empty($punch_start) && !empty($punch_end)) {
                if ($arrTimes[1] > $arrTimes[2] &&
                    (int)str_replace(':', '', $maxPrStartPunch) < $arrTimes[2]) {
                    $maxPrStartPunch = $readyTime;
                }
            }

            if (empty($punch_end) && !empty($punch_start)) {
                if ($arrTimes[0] < $arrTimes[3]) {
                    $minPrEndPunch = $endWorkTime;
                    break;
                }
            }
        }
        return empty($punch_start) && !empty($punch_end) ? [$maxPrStartPunch, $punch_end] : [$punch_start, $minPrEndPunch];
    }

    /**
     * 正常情况下 上下班时间与对应规则的匹配,进行扣除迟到或早退的时间
     * @param $punch_start
     * @param $punch_end
     * @param $punchRuleConfigs
     * @return array
     */
    public function getDeduct($punch_start, $punch_end, $punchRuleConfigs, $formula = NULL)
    {
        $formulaPunchRules = empty($form) ? PunchRulesConfig::getPunchRules($punchRuleConfigs->toArray()) : $formula;
        $deductDay = 0;
        $deductScore = ['minute' => 0, 'score' => 0];
        $isDanger = ['on_work' => false, 'off_work' => false];

        if (!empty($punch_start) || !empty($punch_end)) {
            foreach ($formulaPunchRules['sort'] as $key => $value) {//时间段
                list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);
                list($ps, $pe) = $this->prPunchTime($punch_start, $punch_end, $formulaPunchRules);
                $compare = DataHelper::timesToNum($ps, $pe, $endWorkTime, $readyTime);

                //上班时间对比各个时间段,若开始时间在该时间段之后或结束时间在该时间段之前都证明不在该段内,扣掉该段的时间差
                if (empty($ps) || empty($pe) || $compare[0] >= $compare[2] || $compare[1] <= $compare[3]) {
                    $deductDay = $deductDay + DataHelper::diffTime($readyTime, $endWorkTime);
                }

                //按照这个时间段的多个规则进行匹配扣除
                foreach ($formulaPunchRules['cfg'][$key]['ded_num'] as $item) {
                    $countArr = DataHelper::timesToNum(
                        strtotime($readyTime) + $item['start_gap'], strtotime($readyTime) + $item['end_gap'],
                        strtotime($endWorkTime) - $item['end_gap'], strtotime($endWorkTime) - $item['start_gap']
                    );
                    //上班规则匹配
                    if ($item['late_type'] == PunchRules::LATE_WORK) {
                        if (!empty($punch_start) && DataHelper::ifBetween($countArr[0], $countArr[1], (int)str_replace(':', '', $punch_start))) {
                            if ($item['ded_type'] == PunchRulesConfig::DEDUCT_SCORE) {
                                //扣的分数
                                $deductScore['score'] = $deductScore['score'] + $item['ded_num'];
                                //扣的分钟
                                $m = (strtotime($punch_start) - strtotime($readyTime)) / 60;
                                $deductScore['minute'] = $deductScore['minute'] + ($m > 0 ? $m : 0);
                            }else {
                                //或扣的天数
                                $deductDay = $deductDay + $item['ded_num'];
                            }
                            $isDanger['on_work'] = true;
                        }
                    }
                    //下班规则匹配
                    if ($item['late_type'] == PunchRules::LATE_OFF_WORK) {
                        if (!empty($punch_end) && DataHelper::ifBetween($countArr[2], $countArr[3], (int)str_replace(':', '', $punch_end))) {
                            if ($item['ded_type'] == PunchRulesConfig::DEDUCT_SCORE) {
                                $deductScore['score'] = $deductScore['score'] + $item['ded_num'];
                                $m = (strtotime($endWorkTime) - strtotime($punch_end)) / 60;
                                $deductScore['minute'] = $deductScore['minute'] + ($m > 0 ? $m : 0);
                            }else {
                                $deductDay = $deductDay + $item['ded_num'];
                            }
                            $isDanger['off_work'] = true;
                        }
                    }
                }
            }
        }else {
            $deductDay = 1;
        }
        return ['deduct_day' => $deductDay, 'deduct_score' => $deductScore, 'danger' => $isDanger];
    }

    /**
     * 针对请假与正常情况下的天数扣除统计
     * @param string $punch_start 该天上班打卡时间
     * @param string $punch_end 该天下班打卡时间
     * @param array $punchRuleConfigs 该天对应的打卡规则对象数组
     * @param DailyDetail $dailyDetail 该天明细
     * @return
     */
    public function countDeduct($punch_start, $punch_end, $punchRuleConfigs, $dailyDetail)
    {
        if (!empty($dailyDetail->leave_id)) {
            //请假情况的扣除规则
            $day_l = 0;
            $fromTo = $deducts = [];
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
                $deductDay = 0;
            else {
                //小于1,限制打卡的计算范围,在此范围内先按正常扣天去扣, 再剔除里面包含请假天数的扣除
                $ps = $fromTo['start'] ?? $punch_start ?? NULL;
                $pe = $fromTo['end'] ?? $punch_end ?? NULL;
                $deducts = $this->getDeduct($ps, $pe, $punchRuleConfigs);
                $deductDay = $deducts['deduct_day'] - $day_l;
            }
        }else {
            //正常则按扣除规则
            $deducts = $this->getDeduct($punch_start, $punch_end, $punchRuleConfigs);
            $deductDay = $deducts['deduct_day'];
        }
        return ['deduct_day' => $deductDay > 1 ? 1 : $deductDay, 'deduct_score' => $deducts['deduct_score'] ?? NULL];
    }

    public function storeDeductInLeave($deduct, $userId, $date)
    {
        $switch = HolidayConfig::where('cypher_type', HolidayConfig::CYPHER_SWITCH)->first();
        $switchLeaveId = NULL;
        if ($deduct > 0) {
            $data = [
                'user_id'     => $userId,
                'holiday_id' => $switch->holiday_id,
                'step_id'     => 0,
                'start_time'  => $date,
                'end_time'    => $date,
                'number_day'  => $deduct,
                'reason'      => '',
                'user_list'   => '',
                'status'      => 6,
                'remain_user' => '',
                'copy_user'   => '',
            ];
            $switchLeaveId = Leave::create($data)->leave_id;
        }
        return $switchLeaveId;
    }

    public function dealBuffer($buffer, $punchRuleConfigs, $startTime, $endTime)
    {
        $buf = $buffer;
        $ret = [];
        $formulaPunchRules = PunchRulesConfig::getPunchRules($punchRuleConfigs->toArray());
        foreach ($formulaPunchRules['sort'] as $key => $value) {
            list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);
            if (DataHelper::ifBetween(strtotime($readyTime), strtotime($endWorkTime), strtotime($startTime))) {
                $diff = (strtotime($startTime) - strtotime($readyTime)) / 60;
                if ($diff >= $buf) {
                    $ret = $this->getDeduct(DataHelper::dateTimeAdd($startTime, 'T'.$buf.'M', 'H:i', 'sub'), $endTime, $punchRuleConfigs, $formulaPunchRules);
                    $buf = 0;
                    break;
                }elseif ($diff > 0) {
                    $buf = $buf - $diff;
                    $ret = $this->getDeduct($readyTime, $endTime, $punchRuleConfigs, $formulaPunchRules);
                }
            }
        }
        return ['remain_buffer' => $buf, 'ret' => $ret];
    }

    public function updateDeductBuffer($startDate, $endDate, $userIds, $punchRuleConfigsArr)
    {
        $buffer = $pluckDetail = [];
        $details = DailyDetail::whereBetween('day', [$startDate, $endDate])
            ->whereIn('user_id', $userIds)->orderBy('day')->get();

        foreach ($details as $detail) {
            $pluckDetail[$detail->user_id][$detail->day] =  $detail;
            $buffer[$detail->user_id] = DailyDetail::LEAVE_BUFFER;
        }

        foreach ($pluckDetail as $userId => $values) {
            foreach ($values as $item) {
                if ($buffer[$userId] > 0) {
                    if ($item->heap_late_num > 0) {
                        $ret = $this->dealBuffer($buffer[$userId], $punchRuleConfigsArr[$item->day], $item->punch_start_time, $item->punch_end_time);
                        $item->heap_late_num = $ret['ret']['deduct_score']['minute'];
                        $item->lave_buffer_num = $ret['remain_buffer'];
                        $item->deduction_num = $ret['ret']['deduct_score']['score'];
                        $buffer[$userId] = $ret['remain_buffer'];
                    } else {
                        $item->lave_buffer_num = $buffer[$userId];
                    }
                    $item->save();
                }
            }
        }
    }
}