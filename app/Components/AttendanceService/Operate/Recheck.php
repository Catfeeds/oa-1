<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/3
 * Time: 19:52
 * 补打卡
 */

namespace App\Components\AttendanceService\Operate;

use App\Components\AttendanceService\AttendanceInterface;
use App\Models\Attendance\DailyDetail;
use App\Models\Sys\HolidayConfig;
use Illuminate\Foundation\Validation\ValidatesRequests;


class Recheck extends Operate implements AttendanceInterface
{
    use  ValidatesRequests;

    public function checkLeave($request) : array
    {
        $this->validate($request, $this->_validateRuleRe);
        $p = $request->all();

        //假期配置ID
        $holidayId = $p['holiday_id'] ?? '';
        //批量调休人员名单
        $copyUser = $p['copy_user'] ?? '';

        $startTime = $p['start_time'];
        $endTime = $p['end_time'];

        list($hId, $punchType) = explode('$$', $holidayId);

        $holidayId = $hId;
        //补上班打卡
        if((int)$punchType === HolidayConfig::GO_WORK && empty($startTime)) {
            $this->validate($request, array_merge($this->_validateRule, [
                'start_time' => 'required'
            ]));
        }
        //补下班打卡
        if((int)$punchType === HolidayConfig::OFF_WORK && empty($endTime)) {
            $this->validate($request, array_merge($this->_validateRule, [
                'end_time' => 'required'
            ]));
        }

        //补打卡的时间验证
        if((!empty($startTime) && !empty($endTime)) && strtotime($startTime) > strtotime($endTime)) {
            $this->validate($request, array_merge($this->_validateRule, [
                'start_time' => 'required',
                'end_time' => 'required|after:start_time'
            ]),['请选择有效的时间范围']);
        }

        $data = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'holiday_id' => $holidayId,
            'number_day' => 0,//补打卡默认天数未0
            'user_list' => '',
            'copy_user' => json_encode($copyUser),
            'start_id' => 0,
            'end_id'   => 0,
        ];

        return  $this->backLeaveData(true, [], $data);
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

    public function setDailyDetail($leave)
    {
        $startDay = strtotime($leave->start_time);
        $endDay = strtotime($leave->end_time);

        //上下班都要补打卡的情况
        if (date('Y-m-d', $startDay) == date('Y-m-d', $endDay)) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $startDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();
            $data = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $daily->leave_id),
                'punch_start_time' => date('H:i', $startDay),
                'punch_start_time_num' => $startDay,
                'punch_end_time' => date('H:i', $endDay),
                'punch_end_time_num' => $endDay,
            ];
            $daily->update($data);
            return ;
        }
        //上班补打卡
        if($leave->holidayConfig->punch_type === 1) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $startDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();
            $startData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $daily->leave_id),
                'punch_start_time' => date('H:i', $startDay),
                'punch_start_time_num' => $startDay,
            ];

            $daily->update($startData);
        }

        //下班补打卡
        if($leave->holidayConfig->punch_type === 2) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $endDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();
            $endData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $endDay),
                'leave_id' => self::addLeaveId($leave->leave_id, $daily->leave_id),
                'punch_end_time' => date('H:i', $endDay),
                'punch_end_time_num' => $endDay,
            ];

            $daily->update($endData);
        }

    }
}