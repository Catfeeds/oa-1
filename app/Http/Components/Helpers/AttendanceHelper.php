<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/14
 * Time: 9:56
 */

namespace App\Http\Components\Helpers;

use App\Components\Helper\DataHelper;
use App\Components\Helper\FileHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Role;
use App\Models\Sys\ApprovalStep;
use App\Models\Sys\HolidayConfig;
use App\Models\UserHoliday;
use App\User;
use EasyWeChat\Kernel\Exceptions\Exception;

class AttendanceHelper
{
    /**
     * 显示审核步骤流程
     * @param $stepId
     */
    public static function showApprovalStep($stepId)
    {
        $step = ApprovalStep::where(['step_id' => $stepId])->first();
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
     * 获取申请单开始日期
     * @param $startTime
     * @param $startId
     * @return string
     */
    public static function getLeaveStartTime($startTime, $startId)
    {
        return date('Y-m-d', strtotime($startTime)) . ' ' . Leave::$startId[$startId];
    }

    /**
     * 获取申请单结束日期
     * @param $endTime
     * @param $endId
     * @return string
     */
    public static function getLeaveEndTime($endTime, $endId)
    {
        return date('Y-m-d', strtotime($endTime)) . ' ' . Leave::$endId[$endId];
    }

    /**
     * 设置上传附件
     * @param $request
     * @return string
     */
    public static function setAnnex($request) {

        $file = 'annex';
        $imagePath = $imageName = '';
        if ($request->hasFile($file) && $request->file($file)->isValid()) {
            $time = date('Ymd', time());
            $uploadPath = 'assert/images/'. $time;
            $fileName = $file .'_'. time() . rand(100000, 999999);
            $imageName = FileHelper::uploadImage($request->file($file), $fileName, $uploadPath);
            $imagePath = $uploadPath .'/'. $imageName;
        }
        return $imagePath;
    }

    /**
     * 获取调休部门人员和假期ID
     * @param int $deptId
     * @return array
     */
    public static function getMyChangeLeaveId($deptId)
    {
        $leaveIds = $users = $changeWorkLeaveIds =  $changeLeaveIds =[];
        $user = User::where(['dept_id' => $deptId, 'is_leader' => 1])->first();

        if(empty($user->user_id)) return ['leave_ids' => $leaveIds, 'user_ids' => $users];

        $leave = Leave::with('holidayConfig')->where(['user_id' => $user->user_id])->where('user_list', '!=', '')->get();

        foreach ($leave as $k => $v) {
            $userList = json_decode($v->user_list);

            if(!empty($userList) && is_array($userList) && in_array(\Auth::user()->user_id, $userList)) {
                $leaveIds[] = $v->leave_id;
                $users[] = $userList;
            }

            if(!empty($userList) && !empty($v->holidayConfig) && $v->holidayConfig->change_type === HolidayConfig::WEEK_WORK && in_array(\Auth::user()->user_id, $userList)){
                $changeWorkLeaveIds[] = $v->leave_id;
            }

            if(!empty($userList) && !empty($v->holidayConfig) && $v->holidayConfig->change_type === HolidayConfig::WORK_CHANGE && in_array(\Auth::user()->user_id, $userList)){
                $changeLeaveIds[] = $v->leave_id;
            }

        }

        return ['leave_ids' => $leaveIds, 'user_ids' => $users, 'leave_work_ids' => $changeWorkLeaveIds, 'leave_change_ids' => $changeLeaveIds];
    }

    /**
     * 获取抄送人员ID
     * @return array
     */
    public static function getCopyUser()
    {
        $leaveIds = $users = [];

        $leaves = Leave::where('copy_user', '!=', '')->get();

        foreach ($leaves as $k => $v) {
            $copyUsers = json_decode($v->copy_user);

            if(!empty($copyUsers) && is_array($copyUsers) && in_array(\Auth::user()->user_id, $copyUsers)) {
                $leaveIds[] = $v->leave_id;
                $users[] = $copyUsers;
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
     * 获取有关自己的调休和抄送人员申请单列表
     * @param null $deptId
     * @param int $type
     * @return array
     */
    public static function setChangeList($type, $deptId = NULL)
    {
        if(empty($deptId)) $deptId = \Auth::user()->dept_id;

        $where = '';
        $changeLeaveIds = self::getMyChangeLeaveId($deptId);
        $copyLeaveIds = self::getCopyUser();

        switch ($type) {
            case Leave::DEPT_LEAVE://调休
                $leaveIds =  $changeLeaveIds['leave_ids'];
                if(!empty($leaveIds)) {
                    $leaveIds = implode(',', $leaveIds);
                    $where = " AND Leave_id in ($leaveIds)";
                } else {
                    $where = " AND Leave_id in (-1)";
                }
                break;
            case Leave::COPY_LEAVE://抄送
                $leaveIds =  $copyLeaveIds['leave_ids'];
                if(!empty($leaveIds)) {
                    $leaveIds = implode(',', $leaveIds);
                    $where = " AND Leave_id in ($leaveIds)";
                } else {
                    $where = " AND Leave_id in (-1)";
                }
        }
        $userIds = $changeLeaveIds['user_ids'] + $copyLeaveIds['user_ids'];

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

    /**
     * 员工申请单和福利假期信息返回
     * @param $success
     * @param array $msg
     * @param array $data
     * @return array
     */
    public static function backUserHolidayInfo($success, $msg = [], $data = [])
    {
        return ['success' => $success, 'msg' => $msg, 'data' => $data];
    }

    /**
     * 检验 员工申请单为请假类型
     * @param $request
     * @param $userId
     * @param $holiday
     * @param int $numberDay
     * @return array
     */
    public static function checkUserLeaveHoliday($request, $userId, $holiday, $numberDay)
    {
        $useExt = \Auth::user()->UserExt;

        switch($holiday->condition_id) {
            //申请单为年周期类型判断
            case HolidayConfig::YEAR_RESET:
                //判断入职时间是否满一年
                if(empty($useExt->entry_time) || strtotime($useExt->entry_time) + 84600 * 365 < time()) {
                    return self::backUserHolidayInfo(false, ['holiday_id' => '未有该假期天数,如有疑问,请联系人事']);
                }

                //获取年周期剩余天数
                $overDay = self::getUserYearHoliday($useExt->entry_time, $userId, $holiday);
                //判断申请的天数是否大于剩余的天数
                if($numberDay > $overDay) {
                    return self::backUserHolidayInfo(false, ['holiday_id' => '剩余假期不足,如有疑问,请联系人事']);
                }

                return self::backUserHolidayInfo(true);
                break;
            //申请单为月周期类型判断
            case HolidayConfig::MONTH_RESET:
                //获取月周期余天数
                $overDay = self::getUserMonthHoliday($request, $userId, $holiday);
                //判断申请的天数是否大于剩余的天数
                if($numberDay > $overDay)
                    return self::backUserHolidayInfo(false, ['holiday_id' => '剩余假期不足,如有疑问,请联系人事']);
                return self::backUserHolidayInfo(true);
                break;

            default :
                return self::backUserHolidayInfo(true);
                break;
        }
    }

    /**
     * 检验 员工申请单为调休类型
     * @param $request
     * @param $userId
     * @param $holiday
     * @param int $numberDay
     * @return array
     */
    public static function checkUserChangeHoliday($userId, $holiday, $numberDay = 0)
    {
        //调休类型
        switch($holiday->change_type)
        {
            //申请单为调休类型状态判断
            case HolidayConfig::WORK_CHANGE;
                $changeData = self::getUserChangeHoliday($userId, $holiday);

                $lostDay = $changeData['change_work_day'] - $changeData['change_use_day'];
                if($lostDay <= 0) {
                    return self::backUserHolidayInfo(false, ['holiday_id' => '申请天数不足或未有该申请类型,如有疑问,请联系人事']);
                }
                if($numberDay > $lostDay) {
                    return self::backUserHolidayInfo(false, ['holiday_id' => '申请天数不足或未有该申请类型,如有疑问,请联系人事']);
                }

                return self::backUserHolidayInfo(true);
                break;
            //其它类型默认返回正确
            default :
                return self::backUserHolidayInfo(true);
                break;

        }

    }

    /**
     * 获取 员工调休/加班天数
     * @param $userId
     * @param $holiday
     * @return array
     */
    public static function getUserChangeHoliday($userId, $holiday)
    {
        //获取节假日加班ID
        $changeHoliday = HolidayConfig::where(['change_type' => HolidayConfig::WEEK_WORK])->first();
        //获取部门批量调休或者加班的申请单ID
        $leaves = self::getMyChangeLeaveId(\Auth::user()->dept_id);
        //查询加班的所有记录天数
        $userWorkChangeDay = self::selectChangeInfo($userId, $changeHoliday->holiday_id, $leaves['leave_work_ids']);
        //查询已经调休过的天数
        $userUseChangeDay = self::selectChangeInfo($userId, $holiday->holiday_id, $leaves['leave_work_ids']);

        return ['change_work_day' => $userWorkChangeDay, 'change_use_day' => $userUseChangeDay];
    }

    /**
     * 查询 员工调休/加班的天数
     * @param $userId
     * @param $holidayId
     * @param $leaveIds
     * @return int|mixed
     */
    public static function selectChangeInfo($userId, $holidayId, $leaveIds)
    {
        //调休的默认查询单年
        $startDay = date("Y",time()) . "-01-01";
        $endDay = date("Y",time()) . "-12-31";

        $userChangeLog = Leave::select(\DB::raw('SUM(number_day) number_day'))
            ->where('start_time', '>', $startDay)
            ->where('end_time', '<=', $endDay)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::WAIT_REVIEW, Leave::ON_REVIEW])
            ->where(['user_id' => $userId, 'holiday_id' => $holidayId])
            ->orWhere(function ($query) use ($leaveIds) {
                $query->whereIn('leave_id', $leaveIds);
            })
            ->groupBy('user_id')->first('number_day');

        return empty($userChangeLog->number_day) ? 0 :  $userChangeLog->number_day;
    }


    /**
     * 获取员工 年假类型 记录信息
     * @param $entryTime
     * @param $userId
     * @param $holidayId
     */
    public static function getUserYearHoliday($entryTime, $userId, $holiday)
    {
        //默认为上一年的入职月份的开始时间
        $startDay= date("Y", strtotime("-1 year")) . '-' . date('m-d', strtotime($entryTime));
        //当年的员工年假到期时间
        $endDay = date("Y", time()) . '-' . date('m-d', strtotime($entryTime));

        //年假到期时间之后，重置年假的开始时间和到期时间
        if(strtotime($endDay) < time()) {
            $startDay = date("Y", time()) . '-' . date('m-d', strtotime($entryTime));
            $endDay = date("Y", time()) + 1 . '-' . date('m-d', strtotime($entryTime));
        }

        return self::selectLeaveInfo($startDay, $endDay, $userId, $holiday);
    }

    /** 获取员工 月类型 记录信息
     * @param $userId
     * @param $holiday
     * @return mixed
     */
    public static function getUserMonthHoliday($request, $userId, $holiday)
    {
        $startDay =  date('Y-m-01', strtotime($request['start_time']));
        $endDay =  date('Y-m-t', strtotime($request['end_time']));

        return self::selectLeaveInfo($startDay, $endDay, $userId, $holiday);
    }

    /**
     * 查询员工 福利假期类型 天数
     * @param $startDay
     * @param $endDay
     * @param $userId
     * @param $holiday
     * @return mixed
     */
    public static function selectLeaveInfo($startDay, $endDay, $userId, $holiday)
    {
        $userLeaveLog = Leave::select(\DB::raw('SUM(number_day) number_day'))
            ->where('start_time', '>', $startDay)
            ->where('end_time', '<=', $endDay)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::WAIT_REVIEW, Leave::ON_REVIEW])
            ->where([
                'user_id' => $userId,
                'holiday_id' => $holiday->holiday_id,
            ])->groupBy('user_id')->first('number_day');

        return  empty($userLeaveLog->number_day) ? $holiday->num : $holiday->num - $userLeaveLog->number_day;
    }
}