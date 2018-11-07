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
            $key = sprintf("%d-%d-%d", $item->year, $item->month, $item->day);
            $newCalendarArr[$key] = $item->punchRules->config;
        }
        return $newCalendarArr;
    }

    public function getInfoByData($data)
    {

    }

    /**
     * 正常情况下 上下班时间与对应规则的匹配,进行扣除迟到或早退的时间
     * @param $punch_start
     * @param $punch_end
     * @param $punchRuleConfigs
     * @return array
     */
    public function getDeduct($punch_start, $punch_end, $punchRuleConfigs)
    {
        $punchRuleConfArr = PunchRulesConfig::getPunchRules($punchRuleConfigs->toArray());
        $deduct = 0;
        $isDanger = ['on_work' => false, 'off_work' => false];

        if (!empty($punch_start) || !empty($punch_end)) {
            foreach ($punchRuleConfArr['sort'] as $key => $value) {//时间段
                list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);
                $ps = $punch_start ?? $ps ?? (strtotime($readyTime) <= strtotime($punch_end) ? $readyTime : NULL);
                $pe = $punch_end ?? $pe ?? (strtotime($endWorkTime) >= strtotime($punch_start) ? $endWorkTime : NULL);
                $compare = DataHelper::timesToNum($ps, $pe, $endWorkTime, $readyTime);

                //上班时间对比各个时间段,若开始时间在该时间段之后或结束时间在该时间段之前都证明不在该段内,扣掉该段的时间差
                if (empty($ps) || empty($pe) || $compare[0] >= $compare[2] || $compare[1] <= $compare[3]) {
                    $deduct = $deduct + DataHelper::diffTime($readyTime, $endWorkTime);
                }

                //按照这个时间段的多个规则进行匹配扣除
                foreach ($punchRuleConfArr['cfg'][$key]['ded_num'] as $item) {
                    $countArr = DataHelper::timesToNum(
                        strtotime($readyTime) + $item['start_gap'], strtotime($readyTime) + $item['end_gap'],
                        strtotime($endWorkTime) - $item['end_gap'], strtotime($endWorkTime) - $item['start_gap']
                    );
                    //上班规则匹配
                    if ($item['late_type'] == 1) {
                        if (!empty($punch_start) && DataHelper::ifBetween($countArr[0], $countArr[1], (int)str_replace(':', '', $punch_start))) {
                            $deduct = $deduct + $item['ded_num'];
                            $isDanger['on_work'] = true;
                        }
                    }
                    //下班规则匹配
                    if ($item['late_type'] == 2) {
                        if (!empty($punch_end) && DataHelper::ifBetween($countArr[2], $countArr[3], (int)str_replace(':', '', $punch_end))) {
                            $deduct = $deduct + $item['ded_num'];
                            $isDanger['off_work'] = true;
                        }
                    }
                }
            }
        }else {
            $deduct = 1;
        }
        return ['deduct' => $deduct, 'danger' => $isDanger];
    }

    /**
     * 针对请假与正常情况下的天数扣除统计
     * @param string $punch_start 该天上班打卡时间
     * @param string $punch_end 该天下班打卡时间
     * @param array $punchRuleConfigs 该天对应的打卡规则对象数组
     * @param DailyDetail $dailyDetail 该天明细
     * @return float 返回一天中该扣的天数
     */
    public function countDeduct($punch_start, $punch_end, $punchRuleConfigs, $dailyDetail)
    {
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
                $deduct = 0;
            else {
                //小于1,限制打卡的计算范围,在此范围内先按正常扣天去扣, 再剔除里面包含请假天数的扣除
                $ps = $fromTo['start'] ?? $punch_start ?? NULL;
                $pe = $fromTo['end'] ?? $punch_end ?? NULL;
                $deduct = $this->getDeduct($ps, $pe, $punchRuleConfigs)['deduct'] - $day_l;
            }
        }else {
            //正常则按扣除规则
            $deduct = $this->getDeduct($punch_start, $punch_end, $punchRuleConfigs)['deduct'];
        }
        return $deduct > 1 ? 1 : $deduct;
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
}