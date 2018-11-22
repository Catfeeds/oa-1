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
        $calPunchRuleConfArr = $formulaPunRuleConfArr = [];
        foreach ($calendarArr as $item) {
            $key = sprintf("%d-%02d-%02d", $item->year, $item->month, $item->day);
            $calPunchRuleConfArr[$key] = $item->punchRules->config;
            $formulaPunRuleConfArr[$key] = PunchRulesConfig::getPunchRules($calPunchRuleConfArr[$key]->toArray());
        }
        return ['calPunRuleConf' => $calPunchRuleConfArr, 'formula' => $formulaPunRuleConfArr];
    }

    /**
     * 对上班打卡时间为空或下班打卡时间为空的明细填充一个对应该时间段的时间,供后面计算
     * @param $punch_start
     * @param $punch_end
     * @param $formulaCalPunRuleConf
     * @return array
     */
    public function prPunchTime($punch_start, $punch_end, $formulaCalPunRuleConf)
    {
        $minPrEndPunch = '24:00';
        $maxPrStartPunch = '00:00';
        foreach ($formulaCalPunRuleConf['sort'] as $key => $value) {
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
     * @param $formulaCalPunRuleConf
     * @return array
     */
    public function getDeduct($punch_start, $punch_end, $formulaCalPunRuleConf)
    {
        $deductDay = 0;
        $deductScore = ['minute' => 0, 'score' => 0, 'if_hour' => 0];

        if (!empty($punch_start) || !empty($punch_end)) {
            foreach ($formulaCalPunRuleConf['sort'] as $key => $value) {//时间段
                list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);
                list($ps, $pe) = $this->prPunchTime($punch_start, $punch_end, $formulaCalPunRuleConf);
                $compare = DataHelper::timesToNum($ps, $pe, $endWorkTime, $readyTime);

                //上班时间对比各个时间段,若开始时间在该时间段之后或结束时间在该时间段之前都证明不在该段内,扣掉该段的时间差
                if (empty($ps) || empty($pe) || $compare[0] >= $compare[2] || $compare[1] <= $compare[3]) {
                    $deductDay = $deductDay + DataHelper::leaveDayDiff('2018-01-01', $readyTime, '2018-01-01', $endWorkTime);
                }

                //按照这个时间段的多个规则进行匹配扣除
                foreach ($formulaCalPunRuleConf['cfg'][$key]['ded_num'] as $item) {
                    $countArr = DataHelper::timesToNum(
                        strtotime($readyTime) + $item['start_gap'], strtotime($readyTime) + $item['end_gap'],
                        strtotime($endWorkTime) - $item['end_gap'], strtotime($endWorkTime) - $item['start_gap']
                    );
                    //上班规则匹配
                    if ($item['late_type'] == PunchRules::LATE_WORK && $compare[2] <= 1800) {
                        if (!empty($punch_start) && DataHelper::ifBetween($countArr[0], $countArr[1], (int)str_replace(':', '', $punch_start), 'r=')) {
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
                        }
                    }
                    //下班规则匹配
                    if ($item['late_type'] == PunchRules::LATE_OFF_WORK) {
                        if (!empty($punch_end) && DataHelper::ifBetween($countArr[2], $countArr[3], (int)str_replace(':', '', $punch_end), 'r=')) {
                            if ($compare[2] <= 1800) {
                                if ($item['ded_type'] == PunchRulesConfig::DEDUCT_SCORE) {
                                    $deductScore['score'] = $deductScore['score'] + $item['ded_num'];
                                    $m = (strtotime($endWorkTime) - strtotime($punch_end)) / 60;
                                    $deductScore['minute'] = $deductScore['minute'] + ($m > 0 ? $m : 0);
                                }else {
                                    $deductDay = $deductDay + $item['ded_num'];
                                }
                            }else {
                                $deductScore['if_hour'] = 1;
                            }
                        }
                    }
                }
            }
        }else {
            $deductDay = 1;
        }
        return ['deduct_day' => $deductDay, 'deduct_score' => $deductScore];
    }

    /**
     * 针对请假与正常情况下的天数扣除统计
     * @param string $punch_start 该天上班打卡时间
     * @param string $punch_end 该天下班打卡时间
     * @param array $formulaCalPunRuleConf 该天对应的打卡规则对象数组
     * @param DailyDetail $dailyDetail 该天明细
     * @return array
     */
    public function countDeduct($punch_start, $punch_end, $formulaCalPunRuleConf, $dailyDetail, $buffer)
    {
        if (!empty($dailyDetail->leave_id)) {
            //请假情况的扣除规则
            $day_l = 0;
            $fromTo = $deducts = [];
            $leaves = json_decode($dailyDetail->leave_id, true);
            $leaveObjects = Leave::whereIn('leave_id', $leaves)->whereHas('holidayConfig', function ($query) {
                $query->whereNotIn('cypher_type', [HolidayConfig::CYPHER_RECHECK, HolidayConfig::OVERTIME]);
            })->get();
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
                $deducts = $this->dealBuffer($buffer, $formulaCalPunRuleConf, $ps, $pe);
                $deductDay = $deducts['ret']['deduct_day'] - $day_l;
            }
        }else {
            //正常则按扣除规则
            $deducts = $this->dealBuffer($buffer, $formulaCalPunRuleConf, $punch_start, $punch_end);
            $deductDay = $deducts['ret']['deduct_day'];
        }

        return [
            'deduct_day'    => $deductDay > 1 ? 1 : $deductDay,
            'deduct_score'  => $deducts['ret']['deduct_score'] ?? [],
            'remain_buffer' => $deducts['remain_buffer'] ?? 0,
        ];
    }

    public function storeDeductInLeave($deduct, $userId, $date)
    {
        $data = [
            'user_id'     => $userId,
            'holiday_id' => 0,
            'step_id'     => 0,
            'start_time'  => $date,
            'end_time'    => $date,
            'number_day'  => 0,
            'reason'      => '',
            'user_list'   => '',
            'status'      => Leave::SWITCH_REVIEW_ON,
            'remain_user' => '',
            'copy_user'   => '',
        ];
        $switchLeaveId = $hourLeaveId = NULL;
        $ret = [];
        if ($deduct['deduct_score']['if_hour'] == 1) {
            $hour = HolidayConfig::where('cypher_type', HolidayConfig::CYPHER_HOUR)->first();
            $hourData = $data;
            $hourData['holiday_id'] = $hour->holiday_id;
            $hourLeaveId = Leave::create($hourData)->leave_id;
        }
        if ($deduct['deduct_day'] > 0) {
            $switch = HolidayConfig::where('cypher_type', HolidayConfig::CYPHER_SWITCH)->first();
            $switchData = $data;
            $switchData['holiday_id'] = $switch->holiday_id;
            $switchData['number_day'] = $deduct['deduct_day'];
            $switchLeaveId = Leave::create($switchData)->leave_id;
        }
        if (!empty($switchLeaveId)) $ret[] = $switchLeaveId;
        if (!empty($hourLeaveId)) $ret[] = $hourLeaveId;
        return $ret;
    }

    /**
     * 计算一天的缓冲时间
     * @param $buffer
     * @param $formulaCalPunRuleConf
     * @param $startTime
     * @param $endTime
     * @return array
     */
    public function dealBuffer($buffer, $formulaCalPunRuleConf, $startTime, $endTime)
    {
        $buf = $buffer;
        $ret = [];
        $ifDeductBuf = 0;
        foreach ($formulaCalPunRuleConf['sort'] as $key => $value) {
            list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);
            if (DataHelper::ifBetween(strtotime($readyTime), strtotime($endWorkTime), strtotime($startTime), 'r=')) {
                $diff = (strtotime($startTime) - strtotime($readyTime)) / 60;
                if ($diff >= $buf) {
                    //迟到时间大于缓冲时间, 缓冲时间直接为0, 上班时间修改为减去缓冲时间的时间去计算
                    $ret = $this->getDeduct(DataHelper::dateTimeAdd($startTime, 'T'.$buf.'M', 'H:i', 'sub'), $endTime, $formulaCalPunRuleConf);
                    $buf = 0;
                }elseif ($diff > 0) {
                    //小于缓冲时间,以正常上班的时间去计算
                    $buf = $buf - $diff;
                    $ret = $this->getDeduct($readyTime, $endTime, $formulaCalPunRuleConf);
                }
                $ifDeductBuf = 1;
                break;
            }
        }
        if ($ifDeductBuf === 0) {
            $ret = $this->getDeduct($startTime, $endTime, $formulaCalPunRuleConf);
        }
        return ['remain_buffer' => $buf, 'ret' => $ret];
    }

    /**
     * 计算扣分的入口
     * @param array $bufferArr 保存剩余缓冲分钟数的数组
     * @param array $u 导入excel之后的数据
     * @param array $formulaCalPunRuleConf 格式化之后的打卡规则
     * @param DailyDetail $detail 这一天的打卡情况
     * @return array
     */
    public function fun_(&$bufferArr, $u, $formulaCalPunRuleConf, $detail)
    {
        $index = 'buffer_'.date('Y$$n', strtotime($u['ts'])).$u['alias'];
        if (isset($bufferArr[$index])) {
            $remain_buffer = $bufferArr[$index];
        }else {
            $remain_buffer = DailyDetail::LEAVE_BUFFER;
            $bufferArr[$index] = $remain_buffer;
        }
        $deducts = $this->countDeduct($u['start_time'], $u['end_time'],
            $formulaCalPunRuleConf[$u['ts']], $detail, $remain_buffer);
        $bufferArr[$index] = $deducts['remain_buffer'];
        return $deducts;
    }
}