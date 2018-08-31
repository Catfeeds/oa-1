<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/14
 * Time: 9:56
 */

namespace App\Http\Components\Helpers;


use App\Components\Helper\DataHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Role;
use App\Models\Sys\ApprovalStep;
use App\Models\Sys\HolidayConfig;
use App\Models\UserHoliday;
use App\User;

class AttendanceHelper
{
    /**
     * 显示审核步骤流程
     * @param $stepId
     */
    public static function showApprovalStep($stepId)
    {
        $step = ApprovalStep::findOrFail($stepId);

        $roleId = $roleName = [];
        if(!empty($step->step)) {
            $roleId = json_decode($step->step, true);
        }
        foreach ($roleId as $k => $v) {
            $roleName[] = Role::getRoleText($v);
        }

        $roleName = implode('->', $roleName);
        return $roleName;
    }

    /**
     * @param $startTime
     * @param $startId
     * @return string
     */
    public static function getLeaveStartTime($startTime, $startId)
    {
        return date('Y-m-d', strtotime($startTime)) . ' ' . Leave::$startId[$startId];
    }

    /**
     * @param $endTime
     * @param $endId
     * @return string
     */
    public static function getLeaveEndTime($endTime, $endId)
    {
        return date('Y-m-d', strtotime($endTime)) . ' ' . Leave::$endId[$endId];
    }

    /**
     * 获取调休部门人员和假期ID
     * @param int $deptId
     * @return array
     */
    public static function getMyChangeLeaveId($deptId)
    {
        $leaveIds = $users = [];
        $user = User::where(['dept_id' => $deptId, 'is_leader' => 1])->first();

        if(empty($user->user_id)) return $leaveIds;

        $leave = Leave::where(['user_id' => $user->user_id])->where('user_list', '!=', '')->get();

        foreach ($leave as $k => $v) {
            $userList = json_decode($v->user_list);
            if(!empty($userList) && in_array(\Auth::user()->user_id, $userList)) {
                $leaveIds[] = $v->leave_id;
                $users[] = $userList;
            }
        }

        return ['leave_ids' => $leaveIds, 'user_ids' => $users];
    }


    /**
     * 申请单 拒绝/取消 福利天数回退
     * @param object $leave 申请单信息
     */
    public static function leaveNumBack($leave)
    {
        //拒绝之后，是福利假的话假期天数回退
        $holidayConfig = HolidayConfig::where(['holiday_id' => $leave->holiday_id])->first();
        $userConfig = UserHoliday::where(['user_id' => $leave->user_id, 'holiday_id' => $holidayConfig->holiday_id])->first();
        if(!empty($userConfig->num) && $holidayConfig->num >= $userConfig->num) {
            $startTime = date('Y-m-d', strtotime($leave->start_time)) .' '. Leave::$startId[$leave->start_id];
            $endTime = date('Y-m-d', strtotime($leave->end_time)) .' '. Leave::$endId[$leave->end_id];
            //时间天数分配
            $day = DataHelper::diffTime($startTime, $endTime);
            $num = $userConfig->num + $day;
            if($num > $holidayConfig->num) $num = $holidayConfig->num;

            $userConfig->update(['num' => $num]);
        }
    }

    /**
     * 申请单 通过 操作
     * @param object $leave 申请单信息
     */
    public static function leaveReviewPass($leave)
    {
        if(empty($leave->remain_user)) {
            $leave->update(['status' => 3, 'review_user_id' => 0]);
        } else {
            $remain_user = json_decode($leave->remain_user, true);

            $review_user_id = reset($remain_user);
            unset($remain_user[$review_user_id]);
            if(empty($remain_user)) {
                $remain_user = '';
            } else {
                $remain_user = json_encode($remain_user);
            }

            $leave->update(['status' => 1, 'review_user_id' => $review_user_id, 'remain_user' => $remain_user]);
        }
    }

    /**
     * @param bool $isLeader
     * @param $deptId
     * @return array
     */
    public static function setChangeList($deptId = NULL, $isLeader = true)
    {
        if(empty($deptId)) $deptId = \Auth::user()->dept_id;

        $where = '';
        $userIds = [];
        if($isLeader) {
            $changeLeaveIds = AttendanceHelper::getMyChangeLeaveId($deptId);

            $userIds = $changeLeaveIds['user_ids'];
            if(!empty($changeLeaveIds['leave_ids'])) {
                $leaveIds = implode(',', $changeLeaveIds['leave_ids']);
                $where = " or Leave_id in ($leaveIds)";
            }
        }
        if(!empty($userIds)) {
            $users = [];
            foreach ($userIds as $k => $v) {
                if(empty($v)) continue;
                foreach ($v as $vv) {
                    $users[] = $vv;
                }
            }
            $userIds = array_filter($users);
        }

        return ['where' => $where, 'user_ids' => $userIds];
    }

    /**
     * 请假调休 申请单通过记录到每日详情里
     * @param $leave
     */
    public static function setDailyDetail($leave)
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
                    'leave_id' => $leave->leave_id,
                    'punch_start_time' => Leave::$startId[1],
                    'punch_start_time_num' => strtotime(date('Y-m-d', $d) . ' ' . Leave::$startId[1]),
                    'punch_end_time' => Leave::$endId[3],
                    'punch_end_time_num' => strtotime(date('Y-m-d', $d) . ' ' . Leave::$endId[3]),
                ];

                DailyDetail::create($data);
            }
        }

        $daily = DailyDetail::whereIn('day', [date('Y-m-d', $startDay), date('Y-m-d', $endDay)])
            ->where(['user_id' => $leave->user_id])
            ->first();

        if(!empty($daily->day)) return;

        if($startDay == $endDay) {
            $startData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => $leave->leave_id,
                'punch_start_time' => Leave::$startId[$leave->start_id],
                'punch_start_time_num' => strtotime(date('Y-m-d', $startDay) . ' ' . Leave::$startId[$leave->start_id]),
                'punch_end_time' => Leave::$endId[$leave->end_id],
                'punch_end_time_num' => strtotime(date('Y-m-d', $endDay) . ' ' . Leave::$endId[$leave->end_id]),

            ];

            DailyDetail::create($startData);
        }

        if($startDay < $endDay) {
            $startData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => $leave->leave_id,
                'punch_start_time' => Leave::$startId[$leave->start_id],
                'punch_start_time_num' => strtotime(date('Y-m-d', $startDay) . ' ' . Leave::$startId[$leave->start_id]),
                'punch_end_time' => Leave::$endId[3],
                'punch_end_time_num' => strtotime(date('Y-m-d', $startDay) . ' ' . Leave::$endId[3]),
            ];

            DailyDetail::create($startData);

            if($endDay + 43200 > strtotime(date('Y-m-d', $endDay) . ' ' . Leave::$endId[$leave->end_id])) {
                $endData = [
                    'user_id' => $leave->user_id,
                    'day' => date('Y-m-d', $endDay),
                    'leave_id' => $leave->leave_id,
                    'punch_start_time' => Leave::$startId[1],
                    'punch_start_time_num' => strtotime(date('Y-m-d', $endDay) . ' ' . Leave::$startId[1]),
                    'punch_end_time' => 0,
                    'punch_end_time_num' => 0,
                ];

                DailyDetail::create($endData);

            } else {
                $endData = [
                    'user_id' => $leave->user_id,
                    'day' => date('Y-m-d', $endDay),
                    'leave_id' => $leave->leave_id,
                    'punch_start_time' => Leave::$startId[1],
                    'punch_start_time_num' => strtotime(date('Y-m-d', $endDay) . ' ' . Leave::$startId[1]),
                    'punch_end_time' => Leave::$endId[$leave->end_id],
                    'punch_end_time_num' => strtotime(date('Y-m-d', $endDay) . ' ' . Leave::$endId[$leave->end_id]),
                ];

                DailyDetail::create($endData);
            }

        }
    }

    public static function setRecheckDailyDetail($leave)
    {

        $startDay = strtotime($leave->start_time);
        $endDay = strtotime($leave->end_time);

        //上班补打卡
        if($leave->holidayConfig->punch_type === 1) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $startDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();
            $startData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $startDay),
                'leave_id' => $leave->leave_id,
                'punch_start_time' => date('h:i', $startDay),
                'punch_start_time_num' => $startDay,
            ];

            $daily->update($startData);
        }

        //上班补打卡
        if($leave->holidayConfig->punch_type === 2) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $endDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();
            $endData = [
                'user_id' => $leave->user_id,
                'day' => date('Y-m-d', $endDay),
                'leave_id' => $leave->leave_id,
                'punch_end_time' => date('H:i', $endDay),
                'punch_end_time_num' => $endDay,
            ];

            $daily->update($endData);
        }
    }

}