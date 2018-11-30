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
    public static function getCalendarPunchRules($startDate, $endDate, $calendar = false)
    {
        $calendarArr = Calendar::whereBetween(\DB::raw('UNIX_TIMESTAMP(CONCAT(year, "-", month, "-", day))'),
            [strtotime($startDate), strtotime($endDate) + 3600 * 12])
            ->with('punchRules')->get();
        $calPunchRuleConfArr = $formulaPunRuleConfArr = $eventArr = $cal = [];
        foreach ($calendarArr as $item) {
            $key = sprintf("%d-%02d-%02d", $item->year, $item->month, $item->day);
            $calPunchRuleConfArr[$key] = $item->punchRules->config;
            $formulaPunRuleConfArr[$key] = PunchRulesConfig::getPunchRules($calPunchRuleConfArr[$key]->toArray());
            if ($item->punchRules->punch_type_id != PunchRules::NORMALWORK) {
                $formulaPunRuleConfArr[$key]['if_rest'] = true;
            }

            if ($calendar === true) {
                $eventArr[$key] = $item->punchRules;
                $cal[$key] = $item;
            }
        }
        return [/*'calPunRuleConf' => $calPunchRuleConfArr, */
            'formula' => $formulaPunRuleConfArr, 'event' => $eventArr, 'calendar' => $cal];
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
        if (!empty($punch_start) && !empty($punch_end)) return [$punch_start, $punch_end];

        $minPrEndPunch = '24:00';
        $maxPrStartPunch = '00:00';
        foreach ($formulaCalPunRuleConf['sort'] as $key => $value) {
            list($startWorkTime, $endWorkTime, $readyTime) = explode('$$', $key);
            $arrTimes = DataHelper::timesToNum($punch_start, $punch_end, $readyTime, $endWorkTime);

            if (empty($punch_start) && !empty($punch_end)) {
                if ($arrTimes[1] > $arrTimes[2] &&
                    (int)str_replace(':', '', $maxPrStartPunch) < $arrTimes[2]
                ) {
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
     * 上下班时间与对应规则的匹配,进行扣除迟到或早退的时间
     * @param $punch_start
     * @param $punch_end
     * @param $formulaCalPunRuleConf
     * @return array ['deduct_day' => $deductDay, 'deduct_score' => $deductScore];
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
                    $d = DataHelper::leaveDayDiff('2018-01-01', $readyTime, '2018-01-01', $endWorkTime);
                    //标记小时假,不扣天数
                    if ($compare[2] > 1800 && $d > 0) {
                        $deductScore['if_hour'] = 1;
                        $d = 0;
                    }
                    $deductDay = $deductDay + $d;
                }

                //按照这个时间段的多个规则进行匹配扣除
                foreach ($formulaCalPunRuleConf['cfg'][$key]['ded_num'] as $item) {
                    $countArr = DataHelper::timesToNum(
                        strtotime($readyTime) + $item['start_gap'], strtotime($readyTime) + $item['end_gap'],
                        strtotime($endWorkTime) - $item['end_gap'], strtotime($endWorkTime) - $item['start_gap']
                    );
                    //上班规则匹配
                    if ($item['late_type'] == PunchRules::LATE_WORK && $compare[2] <= 1800) {
                        if (!empty($punch_start) && DataHelper::ifBetween($countArr[0], $countArr[1],
                                (int)str_replace(':', '', $punch_start), 'r=')) {
                            if ($item['ded_type'] == PunchRulesConfig::DEDUCT_SCORE) {
                                //扣的分数
                                $deductScore['score'] = $deductScore['score'] + $item['ded_num'];
                                //扣的分钟
                                $m = (strtotime($punch_start) - strtotime($readyTime)) / 60;
                                $deductScore['minute'] = $deductScore['minute'] + ($m > 0 ? $m : 0);
                            } else {
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
                                } else {
                                    $deductDay = $deductDay + $item['ded_num'];
                                }
                            } else {
                                $deductScore['if_hour'] = 1;
                            }
                        }
                    }
                }
            }
        } else {
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
        $default = ['deduct_day' => 0, 'deduct_score' => [], 'remain_buffer' => $buffer];

        if (!empty($dailyDetail->leave_id)) {
            //请假情况的扣除规则
            $leaves = json_decode($dailyDetail->leave_id, true);
            $leaveObjects = Leave::whereIn('leave_id', $leaves)->whereHas('holidayConfig', function ($query) {
                $query->whereIn('apply_type_id', [HolidayConfig::LEAVEID, HolidayConfig::CHANGE]);
            })->with('holidayConfig')->get();
            $overObjects = Leave::whereIn('leave_id', $leaves)->whereHas('holidayConfig', function ($query) {
                $query->whereIn('cypher_type', [HolidayConfig::CYPHER_OVERTIME]);
            })->first();

            //节假日,直接返回默认
            if (isset($formulaCalPunRuleConf['if_rest']) && empty($overObjects)) return $default;
            //节假日加班, 修改打卡规则
            if (isset($formulaCalPunRuleConf['if_rest']) && !empty($overObjects))
                $formulaCalPunRuleConf = $this->getFormulaOverTimeConf($overObjects);
            else {
                //获取这一天因假期不在的时间段
                $leaveTime = $this->getApplyTimes($formulaCalPunRuleConf, $dailyDetail, $leaveObjects, $punch_start);
                //这一整天请假,直接返回默认
                if (isset($leaveTime['leave_time']['unnecessary'])) return $default;
                //延迟假或夜班加班, 修改打卡规则
                $formulaCalPunRuleConf = self::getFormulaCombineConf(
                    $this->combine(collect($leaveTime)->flatten(1)->toArray()), $formulaCalPunRuleConf);
                if (empty($formulaCalPunRuleConf)) return $default;
            }
            $deducts = $this->dealBuffer($buffer, $formulaCalPunRuleConf, $punch_start, $punch_end);
        } else {
            if (isset($formulaCalPunRuleConf['if_rest'])) return $default;
            //正常则按扣除规则
            $deducts = $this->dealBuffer($buffer, $formulaCalPunRuleConf, $punch_start, $punch_end);
        }
        $deductDay = $deducts['ret']['deduct_day'];

        return [
            'deduct_day'    => $deductDay > 1 ? 1 : $deductDay,
            'deduct_score'  => $deducts['ret']['deduct_score'] ?? [],
            'remain_buffer' => $deducts['remain_buffer'] ?? 0,
        ];
    }

    /**
     * 存入转换假与小时假
     * @param $deduct
     * @param $userId
     * @param $date
     * @return array
     */
    public function storeDeductInLeave($deduct, $userId, $date)
    {
        $data = [
            'user_id'     => $userId,
            'holiday_id'  => 0,
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
        if (isset($deduct['deduct_score']['if_hour'])) {
            if ($deduct['deduct_score']['if_hour'] == 1) {
                $hour = HolidayConfig::where('cypher_type', HolidayConfig::CYPHER_HOUR)->first();
                $hourData = $data;
                $hourData['holiday_id'] = $hour->holiday_id;
                $hourLeaveId = Leave::create($hourData)->leave_id;
            }
        }

        if (isset($deduct['deduct_day'])) {
            if ($deduct['deduct_day'] > 0) {
                $switch = HolidayConfig::where('cypher_type', HolidayConfig::CYPHER_SWITCH)->first();
                $switchData = $data;
                $switchData['holiday_id'] = $switch->holiday_id;
                $switchData['number_day'] = $deduct['deduct_day'];
                $switchLeaveId = Leave::create($switchData)->leave_id;
            }
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
    public function dealBuffer($buffer, $formulaCalPunRuleConf, $startTime, $endTime/*, $leaveTime = []*/)
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
                    $ret = $this->getDeduct(DataHelper::dateTimeAdd($startTime, 'T' . $buf . 'M', 'H:i', 'sub'), $endTime, $formulaCalPunRuleConf);
                    $buf = 0;
                } elseif ($diff > 0) {
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
     * @param array $formulaCalPunRuleConfArr 格式化之后的打卡规则数组
     * @param DailyDetail $detail 这一天的打卡情况
     * @return array
     */
    public function fun_($bufferArr, $u, $formulaCalPunRuleConfArr, $detail)
    {
        $index = 'buffer_' . date('Y$$n', strtotime($u['ts'])) . $u['alias'];
        if (isset($bufferArr[$index])) {
            $remain_buffer = $bufferArr[$index];
        } else {
            $remain_buffer = DailyDetail::LEAVE_BUFFER;
            $bufferArr[$index] = $remain_buffer;
        }
        $deducts = $this->countDeduct($u['start_time'], $u['end_time'],
            $formulaCalPunRuleConfArr[$u['ts']], $detail, $remain_buffer);
        $bufferArr[$index] = $deducts['remain_buffer'];
        return ['deducts' => $deducts, 'bufferArr' => $bufferArr];
    }

    /**
     * 获取这天请假的时间段
     * @param $formulaCalPunRuleConf
     * @param $dailyDetail
     * @param $leaveObjects
     * @return array
     */
    public function getApplyTimes($formulaCalPunRuleConf, $dailyDetail, $leaveObjects, $punchStart): array
    {
        $leaveTime = $nightTime = $delayTime = [];
        $begin = explode('$$', array_first(array_keys($formulaCalPunRuleConf['sort'])))[0];
        $end = explode('$$', array_last(array_keys($formulaCalPunRuleConf['sort'])))[1];
        foreach ($leaveObjects as $leaveObject) {
            if ($leaveObject->holidayConfig->cypher_type == HolidayConfig::CYPHER_NIGHT) {
                $nightTime[] = $this->getNightTime($leaveObject, $begin, $end, $formulaCalPunRuleConf);
            }elseif ($leaveObject->holidayConfig->cypher_type == HolidayConfig::CYPHER_DELAY) {
                $delayTime = $this->getDelayTime($leaveObject, $begin, $end, $punchStart);
            } else {
                $leaveTime[] = $this->getLeaveTime($dailyDetail, $leaveObject, $end, $begin);
            }
        }
        return ['leave_time' => $leaveTime, 'night_time' => $nightTime, 'delay_time' => $delayTime];
    }

    /**
     * 获取因夜班加班,当天不在的时间段
     * @param $begin
     * @param $leaveObject
     * @return array
     */
    public function getNightTime($leaveObject, $begin, $end, $formulaCalPunRuleConf): array
    {
//        $numberDay = $leaveObject->number_day * 3600;
        $lastDaily = DailyDetail::where('day', strtotime('-1 day '.$leaveObject->start_time))->first();
        dd($lastDaily, $leaveObject->number_day);
        if (empty($lastDaily)) return NULL;
        $lpe = explode(':', $lastDaily->punch_end_time);
        if ($lpe > 2400) {
            $m = substr_replace($lpe - 2400, ':', strlen($lpe - 2400) - 2, 0);
            $numberDay = strtotime('+1 day '.$lastDaily->day.' '.$m) - strtotime($lastDaily->day.' '.$leaveObject->start_id);
        }else {
            $numberDay = strtotime($lastDaily->day.' '.$lastDaily->punch_end_time) - strtotime($lastDaily->day.' '.$leaveObject->start_id);
        }

        $duration = 0;
        foreach ($formulaCalPunRuleConf['cfg'] as $key => $cfg) {
            list($v1, $v2) = explode('$$', $key);
            $duration = strtotime($v2) - strtotime($v1) + $duration;
        }
        //加班的工作时长小于正常一天的工作时长,正常上班偏移加班的时长为不在时间
        if ($numberDay < $duration) {
            return [
                'start' => $begin,
                'end'   => $this->findDiffToCreateNewEnd(date('Y-m-d ', strtotime($leaveObject->start_time)).$leaveObject->start_id,
                    date('Y-m-d ', strtotime($leaveObject->end_time)).$leaveObject->end_id, $begin, 'H:i'),
            ];
        }
        //大于正常一天的工作时长,这一整天设为不在时间
        return ['start' => $begin, 'end' => $end];
    }

    /**
     * 获取因延迟假,当天不在的时间段
     * @param $leaveObject
     * @param $begin
     * @param $end
     * @param $punch_start
     * @return array
     */
    public function getDelayTime($leaveObject, $begin, $end, $punch_start)
    {
        $delayTime = [];
        $timeGap = PunchRulesConfig::resolveGapFormula($leaveObject->holidayConfig->work_relief_formula);
        $interval = new \DateInterval('PT' . $timeGap . 'S');
        $dateBegin = new \DateTime($begin);
        $dateEnd = new \DateTime($end);
        switch ($leaveObject->holidayConfig->work_relief_type) {
            case HolidayConfig::NO_SETTING:
                $datePs = new \DateTime($punch_start);
                if (strtotime($punch_start) - strtotime($begin) >= $timeGap) {
                    $delayTime['go'] = [
                        'start' => $begin,
                        'end'   => $dateBegin->add($interval)->format('H:i'),
                    ];
                    break;
                }
                $delayTime[] = [
                    'start' => $begin,
                    'end'   => $punch_start,
                ];
                $delayTime[] = [
                    'start' => $dateEnd->add($dateBegin->diff($datePs))->sub($interval)->format('H:i'),
                    'end'   => $end,
                ];
                break;
            case HolidayConfig::GO_WORK:
                $delayTime[] = [
                    'start' => $begin,
                    'end'   => $dateBegin->add($interval)->format('H:i'),
                ];
                break;
            case HolidayConfig::OFF_WORK:
                $delayTime[] = [
                    'start' => $dateEnd->sub($interval)->format('H:i'),
                    'end'   => $end,
                ];
                break;
        }
        return $this->combine($delayTime);
    }

    /**
     * 对多个重叠的时间段进行合并, 形成这一整天因多种假期导致不在的时间段的时间合并
     * @param $leaveTimes
     * @return array
     */
    public static function combine($leaveTimes)
    {
        $edition = $new = [];
        foreach ($leaveTimes as $key => $row)
        {
            $volume[$key]  = strtotime($row['start']);
            $edition[$key] = strtotime($row['end']);
        }
        array_multisort($volume, SORT_ASC, $edition, SORT_ASC, $leaveTimes);
        for ($i = 0; $i < count($leaveTimes) - 1; $i ++) {
            $j = $i + 1;
            if (strtotime($leaveTimes[$j]['start']) <= strtotime($leaveTimes[$i]['end'])) {
                if (strtotime($leaveTimes[$j]['end']) <= strtotime($leaveTimes[$i]['end'])) {
                    $leaveTimes[$j] = $leaveTimes[$i];
                }else {
                    $leaveTimes[$j]['start'] = $leaveTimes[$i]['start'];
                }
                $leaveTimes[$i] = NULL;
            }
        }
        return array_values(array_filter($leaveTimes));
    }

    /**
     * 针对节假日加班,按档位重新设置新的规则
     * @param $overtime
     * @return array
     */
    public function getFormulaOverTimeConf($overtime)
    {
        $punchTypeId = '//待添加类型';
        $new = [];
        $formulaOverTimeConf = PunchRulesConfig::getPunchRulesCfgToId($punchTypeId);
        foreach ($formulaOverTimeConf['cfg'] as $key => $value) {
            list($start, $end) = explode('$$', $key);
            if (strtotime($overtime->start_id) <= strtotime($start) && strtotime($overtime->end_id) >= strtotime($end)) {
                $new['start_time'][] = $start;
                $new['end_time'][] = $end;
                $new['cfg'][$key] = $value;
                $new['sort'][$key] = $formulaOverTimeConf['sort'][$key];
            }
        }
        return $new;
    }

    /**
     * 针对夜班加班,重新设置新的规则
     * @param $nighttime
     * @param $formulaCalPunRuleConf
     * @return array
     */
    /*public function getFormulaNightConf($nighttime, $formulaCalPunRuleConf)
    {
        $new = [];
        foreach($formulaCalPunRuleConf['cfg'] as $key => $value) {
            list($start, $end) = explode('$$', $key);
            if (DataHelper::ifBetween(strtotime($start), strtotime($end), strtotime($nighttime['end']))) {
                $new['start_time'][] = $nighttime['end'];
                $new['end_time'][] = $end;
                $k = $nighttime['end'].'$$'.$end.'$$'.$nighttime['end'];
                $new['cfg'][$k] = $value;
                $new['sort'][$k] = strtotime($nighttime['end']);
            }elseif (strtotime($start) >= strtotime($nighttime['end'])) {
                $new['start_time'][] = $start;
                $new['end_time'][] = $end;
                $new['cfg'][$key] = $value;
                $new['sort'][$key] = $formulaCalPunRuleConf['sort'][$key];
            }
        }
        return $new;
    }*///to be

    /**
     * 针对延迟假,重新设置新的规则
     * @param $delayTimes
     * @param $formulaCalPunRuleConf
     * @return array
     */
    /*public static function getFormulaDelayConf($delayTimes, $formulaCalPunRuleConf)
    {
        $new = [];$continue = 0;
        foreach($formulaCalPunRuleConf['cfg'] as $key => $value) {
            list($start, $end, $ready) = explode('$$', $key);
            if (!empty($delayTimes['go']) && DataHelper::ifBetween(strtotime($start), strtotime($end), strtotime($delayTimes['go']['end']))) {
                $new['start_time'][] = $delayTimes['go']['end'];
                $new['end_time'][] = $end;
                $k1 = $delayTimes['go']['end'].'$$'.$end.'$$'.$delayTimes['go']['end'];
                $new['cfg'][$k1] = $value;
                $new['sort'][$k1] = strtotime($delayTimes['go']['end']);
                $continue = 1;
            }
            if (!empty($delayTimes['off']) &&
                DataHelper::ifBetween(strtotime($start), strtotime($end), strtotime($delayTimes['off']['start']))
            ) {
                $new['start_time'][] = $start;
                $new['end_time'][] = $delayTimes['off']['start'];
                $k2 = $start.'$$'.$delayTimes['off']['start'].'$$'.$ready;
                $new['cfg'][$k2] = $value;
                $new['sort'][$k2] = $formulaCalPunRuleConf['sort'][$key];
                $continue = 1;
            }
            if ($continue == 1) {
                $continue = 0; continue;
            }
            else {
                $new['start_time'][] = $start;
                $new['end_time'][] = $end;
                $new['cfg'][$key] = $value;
                $new['sort'][$key] = $formulaCalPunRuleConf['sort'][$key];
                $continue = 0;
            }
        }
        return $new;
    }*///to be

    public static function getFormulaCombineConf($combineTimes, $formulaCalPunRuleConf)
    {
        if (empty($combineTimes)) return $formulaCalPunRuleConf;

        $new = [];
        foreach($formulaCalPunRuleConf['cfg'] as $key => $value) {
            $k = explode('$$', $key);
            foreach ($combineTimes as $combineTime) {
                if (DataHelper::ifBetween(strtotime($combineTime['start']), strtotime($combineTime['end']), strtotime($k[1]), '=')) {
                    $k = [$k[0], $combineTime['start'], $k[2]];
                }elseif (DataHelper::ifBetween(strtotime($combineTime['start']), strtotime($combineTime['end']), strtotime($k[0]), '=')) {
                    $k = [$combineTime['end'], $k[1], $combineTime['end']];
                }
            }
            if (strtotime($k[0]) < strtotime($k[1])) {
                $new['cfg'][join('$$', $k)] = $value;
                $new['sort'][join('$$', $k)] = strtotime($k[0]);
            }
        }
        return $new;
    }

    /**
     * 原时间段的差值加上新时间段的开始值等于新时间段的结束值
     * @param $start
     * @param $end
     * @param $newStart
     * @param $format
     * @return string
     */
    public function findDiffToCreateNewEnd($start, $end, $newStart, $format) {
        $dateStart = new \DateTime($start);
        $dateEnd = new \DateTime($end);
        $dateNewStart = new \DateTime($newStart);
        return $dateNewStart->add($dateStart->diff($dateEnd))->format($format);
    }

    /**
     * @param $dailyDetail
     * @param $leaveObject
     * @param $end
     * @param $begin
     * @return array
     */
    public function getLeaveTime($dailyDetail, $leaveObject, $end, $begin): array
    {
        $leaveTime = [];
        $leaStartDate = strtotime($leaveObject->start_time);
        $leaEndDate = strtotime($leaveObject->end_time);
        if ($leaEndDate == $leaStartDate) {
            $leaveTime = ['start' => $leaveObject->start_id, 'end' => $leaveObject->end_id];
        } elseif ($leaEndDate > $leaStartDate && strtotime($dailyDetail->day) == $leaStartDate) {
            $leaveTime = ['start' => $leaveObject->start_id, 'end' => $end];
        } elseif ($leaEndDate > $leaStartDate && strtotime($dailyDetail->day) == $leaEndDate) {
            $leaveTime = ['start' => $begin, 'end' => $leaveObject->end_id];
        } elseif (DataHelper::ifBetween($leaStartDate, $leaEndDate, strtotime($dailyDetail->day))) {
            $leaveTime = ['start' => $begin, 'end' => $end, 'unnecessary' => 1];
        }
        return $leaveTime;
    }

    public function getLastDayEnd($v, $boundary)
    {
        $lastDayEndTime = '00:00';$j = 0;
        for ($i = 5; $i < count($v); $i ++) {
            if (strtotime($boundary) >= strtotime($v[$i]) && strtotime($v[$i]) > strtotime($lastDayEndTime)) {
                $lastDayEndTime = $v[$i];$j = $i;
            }
        }
        if ($j == 0) {
            $lastDayEndTime = NULL;
            $startTime = $v[5];
        }else {
            list($h, $m) = explode(':', $lastDayEndTime);
            $lastDayEndTime = ($h + 24) . ':' . $m;//重新获得上一天的下班打卡时间
            $startTime = $v[$j + 1] ?? NULL;
        }
        return [$lastDayEndTime, $startTime];
    }

    public function dealLastDayEnd($ts, $v, $userIds)
    {
        $lastDay = DataHelper::dateTimeAdd($ts, '1D', 'Y-m-d 00:00:00', 'sub');
        $night = Leave::where(['user_id' => $userIds[$v[1]], 'start_time' => $lastDay, 'status' => Leave::WAIT_EFFECTIVE])
            ->whereHas('holidayConfig', function ($query) {
                $query->where('cypher_type', HolidayConfig::CYPHER_NIGHT);
            })->first();

        if (!empty($night)) {
            $nightDate = explode(' ', $night->end_time);
            $boundary = DataHelper::dateTimeAdd(PunchRules::BEGIN_TIME, 'T1H', 'H:i', 'sub');
            //夜班加班申请时间大于六点, 以申请的下班时间加1小时为界线
            if ($nightDate[0] == $ts && strtotime($nightDate[1]) >= strtotime($boundary)) {
                return $this->getLastDayEnd($v, DataHelper::dateTimeAdd($nightDate[1], 'T1H', 'H:i'));
            }
        }
        return $this->getLastDayEnd($v, PunchRules::BEGIN_TIME);
    }
}