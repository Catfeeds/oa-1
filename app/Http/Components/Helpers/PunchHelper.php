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
use Illuminate\Database\Eloquent\Collection;

class PunchHelper
{
    protected $cypherHolidays;
    protected $formulaPunRuleConfArr;

    public function setCypherHolidays(array $objects)
    {
        foreach ($objects as $object) {
            $this->cypherHolidays[HolidayConfig::$cypherTypeChar[$object->cypher_type]] = $object;
        }
    }

    public function getCypherHolidaysByType($cypherType)
    {
        $cypherChar = HolidayConfig::$cypherTypeChar[$cypherType];
        if (empty($this->cypherHolidays[$cypherChar])) {
            $holidayConf = HolidayConfig::where('cypher_type', $cypherType)->first();
            if (!empty($holidayConf)) {
                $this->setCypherHolidays([$holidayConf]);
                return $holidayConf;
            }else {
                return NULL;
            }
        }else {
            return $this->cypherHolidays[$cypherChar];
        }
    }

    /**
     * 连表获取日历对应的上下班配置
     * @param $minDate
     * @param $maxDate
     * @return array
     */
    public static function getCalendarPunchRules($minDate, $maxDate, $calendar = false, $calendarArr = NULL)
    {
        $calPunchRuleConfArr = $formulaPunRuleConfArr = $eventArr = $cal = [];
        if (empty($calendarArr))
            $calendarArr = Calendar::getCalendarArrWithPunchRules($minDate, $maxDate);
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
     * 针对只有一个打卡时间情况的处理,填充相应时间点,供后面计算
     * 如:打卡时间为8:45, 则下班时间填充为12:00
     * 打卡时间为20:10, 则上班时间填充为14:00 下班时间为20:10
     * 15:30 ---> 上:15:30 下:18:00
     * @param $punch_start
     * @param $punch_end
     * @param $formulaCalPunRuleConf
     * @return array [$punchStart, $punchEnd]
     */
    public function prPunchTime($punch_start, $punch_end, $formulaCalPunRuleConf)
    {
        if (!empty($punch_start) && !empty($punch_end)) return [$punch_start, $punch_end];

        $punchStart = $punch_start;
        $punchEnd = $punch_end;

        if (empty($punch_end) && !empty($punch_start)) {
            //不含小时假的配置时间段中最大的开始时间点
            $maxStartNoHour = '';
            foreach ($formulaCalPunRuleConf['sort'] as $key => $value) {
                list($start, $end) = explode('$$', $key);
                $arrTimes = DataHelper::timesToNum($punch_start, $start, $end);
                $hourDuration = collect($formulaCalPunRuleConf['cfg'][$key]['ded_num'])
                    ->where('holiday_id', $this->getCypherHolidaysByType(HolidayConfig::CYPHER_HOUR)->holiday_id ?? '0')->first();
                if (empty($hourDuration)) {
                    if ($arrTimes[0] <= $arrTimes[2]) {
                        $punchEnd = $end;break;
                    }
                    $maxStartNoHour = empty($maxStartNoHour) || strtotime($maxStartNoHour) < strtotime($start) ? $start : $maxStartNoHour;
                }else {
                    if ($arrTimes[0] <= $arrTimes[2]) {
                        $punchStart = $maxStartNoHour;
                        $punchEnd = $punch_start;break;
                    }else {
                        $punchEnd = $punch_start;
                        $punchStart = $maxStartNoHour;break;
                    }
                }
            }
        }
        dd([$punchStart, $punchEnd]);
        return [$punchStart, $punchEnd];
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
                //待测
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
                    //continue;
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
            $overObject = Leave::whereIn('leave_id', $leaves)->whereHas('holidayConfig', function ($query) {
                $query->whereIn('cypher_type', [HolidayConfig::CYPHER_OVERTIME]);
            })->first();

            //节假日,直接返回默认
            if (isset($formulaCalPunRuleConf['if_rest']) && empty($overObject)) return $default;
            //节假日加班, 修改打卡规则
            if (isset($formulaCalPunRuleConf['if_rest']) && !empty($overObject)) {
                $formulaCalPunRuleConf = $this->getFormulaOverTimeConf($overObject);
                $deducts = $this->dealHolidayWork($buffer, $formulaCalPunRuleConf, $punch_start, $punch_end, $overObject);
            }
            else {
                //获取这一天因假期不在的时间段
                $leaveTime = $this->getApplyTimes($formulaCalPunRuleConf, $dailyDetail, $leaveObjects, $punch_start);
                //这一整天请假,直接返回默认
                if (isset($leaveTime['leave_time']['unnecessary'])) return $default;
                //延迟假或夜班加班, 修改打卡规则
                $formulaCalPunRuleConf = self::getFormulaCombineConf(
                    $this->combine(collect($leaveTime)->flatten(1)->toArray()), $formulaCalPunRuleConf);
                if (empty($formulaCalPunRuleConf)) return $default;
                $deducts = $this->dealBuffer($buffer, $formulaCalPunRuleConf, $punch_start, $punch_end);
            }
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
     * 获取这天请假的时间段(待作为驱动进行优化)
     * @param $formulaCalPunRuleConf
     * @param $dailyDetail
     * @param $leaveObjects
     * @return array
     */
    public function getApplyTimes($formulaCalPunRuleConf, $dailyDetail, $leaveObjects, $punchStart): array
    {
        $leaveTime = $nightTime = $delayTime = $hourTime = [];
        $begin = explode('$$', array_first(array_keys($formulaCalPunRuleConf['sort'])))[0];
        $end = explode('$$', array_last(array_keys($formulaCalPunRuleConf['sort'])))[1];
        foreach ($leaveObjects as $leaveObject) {
            if ($leaveObject->holidayConfig->cypher_type == HolidayConfig::CYPHER_NIGHT) {
                $nightTime[] = $this->getNightTime($leaveObject, $begin, $end, $formulaCalPunRuleConf);
            }elseif ($leaveObject->holidayConfig->cypher_type == HolidayConfig::CYPHER_DELAY) {
                $delayTime = $this->getDelayTime($leaveObject, $begin, $end, $punchStart);
            }elseif ($leaveObject->holidayConfig->cypher_type == HolidayConfig::CYPHER_HOUR) {
                $hourTime[] = $this->getHourTime($leaveObject);
            }else {
                $leaveTime[] = $this->getLeaveTime($dailyDetail, $leaveObject, $end, $begin);
            }
        }
        return ['leave_time' => $leaveTime, 'night_time' => $nightTime, 'delay_time' => $delayTime, 'hour_time' => $hourTime];
    }

    /**
     * 获取因夜班加班,当天不在的时间段
     * @param $begin
     * @param $leaveObject
     * @return array
     */
    public function getNightTime($leaveObject, $begin, $end, $formulaCalPunRuleConf): array
    {
        $lastDaily = DailyDetail::where('day', date('Y-m-d', strtotime($leaveObject->start_time)))->first();
        if (empty($lastDaily)) return NULL;
        $lpe = explode(':', $lastDaily->punch_end_time);
        if ($lpe[0] > 24) {
            $m = ($lpe[0] - 24).':'.$lpe[1];
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

    public function getHourTime($leaveObject)
    {
        return ['start' => $leaveObject->start_id, 'end' => $leaveObject->end_id];
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
     * 获取因带薪假/无薪假,当天不在的时间段
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
        $punchRulesId = PunchRules::where('punch_type_id', PunchRules::HOLIDAY_WORK)->first()->id;
        $new = [];
        $formulaOverTimeConf = PunchRulesConfig::getPunchRulesCfgToId($punchRulesId);
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
     * 对时间的的界限问题进行处理
     * @param $v
     * @param $boundary
     * @return array
     */
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
            $endTime = end($v);
        }else {
            list($h, $m) = explode(':', $lastDayEndTime);
            $lastDayEndTime = ($h + 24) . ':' . $m;//重新获得上一天的下班打卡时间
            $startTime = $v[$j + 1] ?? NULL;
            $endTime = empty($startTime) ? NULL : end($v);
        }
        if ($startTime == $endTime) {
            $endTime = NULL;
        }
        return [$lastDayEndTime, $startTime, $endTime];
    }

    public function dealLastDayEnd($ts, $v, $users, Collection $nights)
    {
        $lastDay = DataHelper::dateTimeAdd($ts, '1D', 'Y-m-d 00:00:00', 'sub');
        if (!empty($users[$v[3]])) {
            $nights->where('user_id', $users[$v[3]]->user_id)->where('start_time', $lastDay)->first();
        }

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

    public function dealHolidayWork($buffer, $formulaCalPunRuleConf, $punch_start, $punch_end, Leave $overTimeObj)
    {
        $deducts = $this->dealBuffer($buffer, $formulaCalPunRuleConf, $punch_start, $punch_end);
        $deducts['ret']['deduct_day'] = 0;
        $workDuration = collect($formulaCalPunRuleConf['cfg'])->keys()->map(function ($value) {
            list($start, $end) = explode('$$', $value);
            return strtotime($end) - strtotime($start);
        })->sum();

        $d = $this->getNormalWorkTime($formulaCalPunRuleConf);
        $allDuration = strtotime($d['end']) - strtotime($d['start']);

        $restDuration = $allDuration - $workDuration;
        $rate = (strtotime($punch_end) - strtotime($punch_start) - $restDuration) / $workDuration;
        if ($rate >= 0.5) {
            $overTimeObj->status = Leave::PASS_REVIEW;
            $overTimeObj->save();
        }else {
            $overTimeObj->status = Leave::CANCEL_REVIEW;
            $overTimeObj->save();
        }
        return $deducts;

    }

    public function getNormalWorkTime($formulaCalPunRuleConf)
    {
        return [
            'start' => explode('$$', collect($formulaCalPunRuleConf['sort'])->keys()->first())[0],
            'end' => explode('$$', collect($formulaCalPunRuleConf['sort'])->keys()->last())[1]
        ];
    }
}