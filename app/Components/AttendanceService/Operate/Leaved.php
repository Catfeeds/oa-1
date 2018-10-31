<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/3
 * Time: 19:52]
 * 申请请假配置类型
 */
namespace App\Components\AttendanceService\Operate;

use App\Components\AttendanceService\AttendanceInterface;
use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRules;


class Leaved extends Operate implements AttendanceInterface
{
    /**
     * 检验申请单验证
     * @param $request
     * @return array
     */
    public function checkLeave($request) : array
    {
        $p = $request->all();
        $this->validate($request, array_merge($this->_validateRule,[
            'start_id' => 'required',
            'end_id' => 'required',
        ]));
        //假期配置ID
        $holidayId = $p['holiday_id'];

        //批量调休人员名单
        $userList = $p['dept_users'] ?? '';
        $copyUser = $p['copy_user'] ?? '';
        //申请时间
        $startTime = (string)$p['start_time'];
        $endTime = (string)$p['end_time'];
        //拼接有效时间戳
        $startTimeS = trim($startTime .' '. $p['start_id']);
        $endTimeS = trim($endTime .' '. $p['end_id']);
        //时间判断
        if(strtotime($startTimeS) > strtotime($endTimeS)) {
            return $this->backLeaveData(false, ['end_time' => trans('请选择有效的时间范围')]);
        }
        //时间天数分配
        $numberDay = DataHelper::diffTime($startTimeS, $endTimeS);
        if(empty($numberDay)) {
            return $this->backLeaveData(false, ['end_time' => trans('申请失败,时间跨度异常，有疑问请联系人事')]);
        }

        //验证是否已经有再提交的请假单,排除已拒绝的请假单
        $isLeaves = Leave::whereRaw("
            status != 2 and 
            `start_time` BETWEEN '{$startTime}' and '{$endTime}'
                or 
            `end_time` BETWEEN '{$startTime}' and '{$endTime}'
        ")->get();

        foreach ($isLeaves as $lk => $lv) {
            if(empty($lv->user_id)) continue;
            $diffEndTime = strtotime(AttendanceHelper::getLeaveEndTime($lv->end_time, $lv->end_id));
            if($diffEndTime >= strtotime($startTimeS)) {
                return $this->backLeaveData(false, ['end_time' => trans('已经有该时间段申请单')]);
            }
        }

        //渠道配置计算类型配置判断
        $holidayConfig = HolidayConfig::where(['holiday_id' => $holidayId])->first();
        $driver = HolidayConfig::$cypherTypeChar[$holidayConfig->cypher_type];
        $userHoliday = $this->driver($driver)->check($holidayConfig, $numberDay);
        //验证是否要上次附件
        if($holidayConfig->is_annex === HolidayConfig::STATUS_ENABLE && empty($p['annex'])) {
            return $this->backLeaveData(false, ['annex' => trans('请上传附件')]);
        }
        //验证是否允许再节日前后申请
        if($holidayConfig->is_before_after === HolidayConfig::STATUS_ENABLE ) {
            $st =  strtotime(date('Y-m-d', strtotime('-1day', strtotime($startTimeS))));
            $et =  strtotime(date('Y-m-d', strtotime('+1day', strtotime($endTimeS))));
            $st = date('Y', $st) .'-'. (int)date('m', $st) .'-'. (int)date('d', $st);
            $et = date('Y', $et) .'-'. (int)date('m', $et) .'-'. (int)date('d', $et);

            $calendar= Calendar::with('punchRules')
                ->whereRaw('CONCAT(year,"-",month,"-",day)>='. $st .' and CONCAT(year,"-",month,"-",day) <= '. $et)
                ->get();

            foreach ($calendar as $ck => $cv) {
                if($cv->punchRules->punch_type_id === PunchRules::HOLIDAY) {
                    return $this->backLeaveData(false, ['end_time' => trans($cv->year.'-'.$cv->month.'-'.$cv->day.'有节假日，不允许连休!')]);
                }
            }
        }

        //员工剩余假期判断和假期使用完是否可在提交请假单
        if(!$userHoliday['success']) {
            return $this->backLeaveData(false, $userHoliday['message']);
        }

        //返回数据
        $userList = json_encode($userList);
        $data = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'holiday_id' => $holidayId,
            'number_day' => $numberDay,
            'user_list' => $userList,
            'copy_user' => json_encode($copyUser),
            'start_id' => $p['start_id'],
            'end_id'   => $p['end_id'],
        ];

        return  $this->backLeaveData(true, [], $data);
    }

    public function getLeaveStep($holidayId, $numberDay): array
    {
        return parent::getLeaveStep($holidayId, $numberDay);
    }

    /**
     * 创建申请单
     * @param array $leave
     * @return array
     */
    public function createLeave(array $leave): array
    {
        return parent::createLeave($leave);
    }

    /**
     * @param object $leave
     */
    public function leaveReviewPass($leave)
    {
        if(empty($leave->remain_user)) {
            $leave->update(['status' => 3, 'review_user_id' => 0]);
            self::setDailyDetail($leave);

        } else {
            $remainUser = json_decode($leave->remain_user, true);

            $reviewUserId = reset($remainUser);
            array_shift($remainUser);

            if(empty($remainUser)) {
                $remainUser = '';
            } else {
                $remainUser = json_encode($remainUser);
            }

            $leave->update(['status' => 1, 'review_user_id' => $reviewUserId, 'remain_user' => $remainUser]);
        }
    }

    /**
     * @param $leave
     */
    public function setDailyDetail($leave)
    {
        $startDay = strtotime($leave->start_time);
        $endDay = strtotime($leave->end_time);

        $day = DataHelper::prDates($startDay, $endDay);

        if(!empty($day)) {
            foreach ($day as $k => $d) {
                $daily = DailyDetail::whereIn('day', [date('Y-m-d', $d)])->where(['user_id' => $leave->user_id])->first();
                if(!empty($daily->day)) continue;
                $data = [
                    'user_id' => $leave->user_id,
                    'day' => date('Y-m-d', $d),
                    'leave_id' => self::addLeaveId($leave->leave_id),
                    'punch_start_time' => Leave::$startId[1],
                    'punch_start_time_num' => strtotime(date('Y-m-d', $d) . ' ' . Leave::$startId[1]),
                    'punch_end_time' => Leave::$endId[3],
                    'punch_end_time_num' => strtotime(date('Y-m-d', $d) . ' ' . Leave::$endId[3]),
                ];

                DailyDetail::create($data);
            }
        }

        $startDaily = DailyDetail::where(['day' => date('Y-m-d', $startDay), 'user_id' => $leave->user_id])->first();
        $endDaily = DailyDetail::where(['day' => date('Y-m-d', $endDay), 'user_id' => $leave->user_id])->first();

        $punch = self::getPunch($leave, $startDay, $endDay);

        if($startDay == $endDay) {
            //插入或更新所需数据, 更新新的请假打卡也就是为空的字段,将新的请假id存入数组转为json存入数据表
            $startData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $startDaily->leave_id ?? NULL),
                'punch_start_time' => $startDaily->punch_start_time ?? $punch['punch_start_time'],
                'punch_start_time_num' => $startDaily->punch_start_time_num ?? $punch['punch_start_time_num'],
                'punch_end_time' => $startDaily->punch_end_time ?? $punch['punch_end_time'],
                'punch_end_time_num' => $startDaily->punch_end_time_num ?? $punch['punch_end_time_num'],
            ];
            empty($startDaily->day) ? DailyDetail::create($startData) : $startDaily->update($startData);
        }

        if($startDay < $endDay) {
            $startData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $startDaily->leave_id ?? NULL),
                'punch_start_time' => $startDaily->punch_start_time ?? $punch['punch_start_time'],
                'punch_start_time_num' => $startDaily->punch_start_time_num ?? $punch['punch_start_time_num'],
                'punch_end_time' => $startDaily->punch_end_time ?? Leave::$endId[3],
                'punch_end_time_num' => $startDaily->punch_end_time_num ?? strtotime(date('Y-m-d', $startDay) . ' ' . Leave::$endId[3]),
            ];
            empty($startDaily->day) ? DailyDetail::create($startData) : $startDaily->update($startData);

            $endData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $endDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $endDaily->leave_id ?? NULL),
                'punch_start_time' => $endDaily->punch_start_time ?? Leave::$startId[1],
                'punch_start_time_num' => $endDaily->punch_start_time_num ??
                    strtotime(date('Y-m-d', $endDay) . ' ' . Leave::$startId[1]),
                'punch_end_time' => $endDaily->punch_end_time ?? $punch['punch_end_time'],
                'punch_end_time_num' => $endDaily->punch_end_time_num ?? $punch['punch_end_time_num'],
            ];
            empty($endDaily->day) ? DailyDetail::create($endData) : $endDaily->update($endData);
        }
    }

}
