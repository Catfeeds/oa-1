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
use App\Models\Sys\Calendar;
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

    public static function setRecheckDailyDetail($leave)
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
                'punch_start_time' => date('h:i', $startDay),
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
                'punch_start_time' => date('h:i', $startDay),
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

/*        switch($holiday->condition_id) {
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
        }*/

    }

    /**
     * 检验 员工申请单为调休类型
     * @param $userId
     * @param $holiday
     * @param int $numberDay
     * @return array
     *
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
        $userWorkChangeDay = self::selectChangeInfo($userId, $changeHoliday->holiday_id, $leaves['leave_work_ids'] ?? []);
        //查询已经调休过的天数
        $userUseChangeDay = self::selectChangeInfo($userId, $holiday->holiday_id, $leaves['leave_change_ids'] ?? []);

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
     * 解析 申请配置公式格式
     * @param string $formula 公式格式 [0,0,0,0,0,0] | [年,月,日,时,分,秒]
     */
    public static function resolveConfigFormula($formula)
    {
        $format = json_decode($formula, true);
        if(empty($format)) return [];

        $date = [];
        foreach ($format as $k => $v) {
            if(empty($v)) continue;
            switch ($k) {
                case 0 :
                    $date['y'] = $v . 'year';
                    break;
                case 1 :
                    $date['m'] = $v . 'month';
                    break;
                case 2 :
                    $date['d'] = $v . 'day';
                    break;
                case 3 :
                    $date['h'] = $v . 'hour';
                    break;
                case 4 :
                    $date['i'] = $v . 'minute';
                    break;
                case 5 :
                    $date['s'] = $v . 'second';
                    break;
            }
        }

        return $date;
    }


    public static function resolveCycleConfigFormula($formula)
    {
        $format = json_decode($formula, true);
        if(empty($format)) return [];

        $date = [];
        foreach ($format as $k => $v) {
            if(empty($v)) continue;
            switch ($k) {
                case 0 :
                    $date['m'] = $v;
                    break;
                case 1 :
                    $date['d'] = $v ;
                    break;
                case 2 :
                    $date['h'] = $v ;
                    break;
                case 3 :
                    $date['i'] = $v ;
                    break;
                case 4 :
                    $date['s'] = $v;
                    break;
            }
        }

        return $date;
    }



    /**
     * 按员工入职时间维度获取带薪假期
     * @param $entryTime
     * @param $userId
     * @param $holiday
     * @return mixed 返回天数
     */
    public static function getUserPayableDayToEntryTime($entryTime, $userId, $holiday)
    {
        //获得带薪假期配置信息
        $claimDate = self::resolveConfigFormula($holiday->payable_claim_formula);
        $claimTime = date('Y-m-d H:i:s', strtotime('+'. implode(' ', $claimDate), strtotime($entryTime)));

        //入职未满配置时间范围，返回天数为0
        if(empty($entryTime) || strtotime($claimTime) > time()) return 0;

        //获取带薪假期重置配置信息
        $resetDate = self::resolveConfigFormula($holiday->payable_reset_formula);
        $resetTime = date('Y-m-d H:i:s', strtotime('+'. implode(' ', $resetDate), strtotime($claimTime)));

        //开始默认为带薪起效时间
        $startTime= $claimTime;
        //结束默认为带薪重置时间
        $endTime = $resetTime;

        //到期时间之后，重置开始时间和到期时间
        if(strtotime($endTime) < time()) {
            $startTime = date('Y', time()) . '-' . date('m-d H:i:s', strtotime($startTime));
            $endTime = date('Y-m-d H:i:s', strtotime('+'. implode(' ', $resetDate), strtotime($startTime)));
        }
        return self::selectLeaveInfo($startTime, $endTime, $userId, $holiday);
    }

    /**
     * 按自然周期时间维度获取带薪假期
     * @param $userId
     * @param $holiday
     * @return mixed
     */
    public static function getUserPayableDayToNaturalCycleTime($entryTime, $userId, $holiday)
    {
        $claimDate = self::resolveConfigFormula($holiday->payable_claim_formula);

        $claimTime = date('Y-m-d H:i:s', strtotime('+'. implode(' ', $claimDate), strtotime($entryTime)));
        //入职未满配置时间范围，返回天数为0
        if(empty($entryTime) || strtotime($claimTime) > time()) return 0;

        $resetDate = self::resolveCycleConfigFormula($holiday->payable_reset_formula);

        $resetTime = sprintf('%d %s:%s:%s', $resetDate['d'] ?? date('d', time()), $resetDate['h'] ?? '00', $resetDate['i'] ?? '00', $resetDate['s'] ?? '00');
        if(array_key_exists('m', $resetDate)) {
            $startTime = date('Y-m-d H:i:s', strtotime(date('Y', time()) .'-'. $resetDate['m'] . '-' . $resetTime));
            $endTime = date('Y-m-d H:i:s', strtotime('+1year', strtotime($startTime)));
        } elseif(empty($resetDate['m']) && !empty($resetDate['d'])) {
            $startTime = date('Y-m-d H:i:s', strtotime(date('Y', time()) .'-'. date('m', time()) . '-' . $resetTime));;
            $endTime = date('Y-m-d H:i:s', strtotime('+1month', strtotime($startTime)));
        } elseif(empty($resetDate['m']) && empty($resetDate['d']) && !empty($resetDate['h'])) {
            $startTime = date('Y-m-d H:i:s', strtotime(date('Y', time()) .'-'. date('m', time()) . '-' . $resetTime));;
            $endTime = date('Y-m-d H:i:s', strtotime('+1day', strtotime($startTime)));
        } else {
            $startTime = date('Y-m-d H:i:s', strtotime(date('Y', time()) .'-'. date('m', time()) . '-' . $resetTime));;
            $endTime = date('Y-m-d H:i:s', strtotime('+1hour', strtotime($startTime)));
        }

        return self::selectLeaveInfo($startTime, $endTime, $userId, $holiday);
    }

    /**
     * 获取员工 月类型 记录信息
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
        $userLeaveLog = Leave::select([
            \DB::raw('SUM(number_day) number_day')
            ])
            ->where('start_time', '>', $startDay)
            ->where('end_time', '<=', $endDay)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::WAIT_REVIEW, Leave::ON_REVIEW])
            ->where([
                'user_id' => $userId,
                'holiday_id' => $holiday->holiday_id,
            ])->groupBy('user_id')->first('number_day');

        return empty($userLeaveLog->number_day) ? $holiday->up_day : $holiday->up_day - $userLeaveLog->number_day;
    }

    /**
     * 审核通过后, 上班打卡字段与下班打卡字段的设置
     * @param $leave
     * @param $startDay
     * @param $endDay
     * @return array
     */
    public static function getPunch($leave, $startDay, $endDay)
    {
        $ps = (int)str_replace(':', '', Leave::$startId[$leave->start_id]);
        $pe = (int)str_replace(':', '', Leave::$endId[$leave->end_id]);
        $arr1 = [
            //大于13:45,意味下午请假,则上班打卡字段为空,为后面打卡记录导入的上班打卡留位置
            'punch_start_time' => $ps >= 1345 ? NULL : Leave::$startId[$leave->start_id],
            //不等于20点,意味晚上还要回来上班,下班打卡字段为空,为后面打卡记录导入的下班打卡留位置
            'punch_end_time' => $pe != 2000 ? NULL : Leave::$endId[$leave->end_id]
        ];

        $arr2 = [
            'punch_start_time_num' => empty($arr1['punch_start_time']) ?
                NULL : strtotime(date('Y-m-d', $startDay) . ' ' . $arr1['punch_start_time']),
            'punch_end_time_num' => empty($arr1['punch_end_time']) ?
                NULL : strtotime(date('Y-m-d', $endDay) . ' ' . $arr1['punch_end_time']),
        ];
        return array_merge($arr1, $arr2);
    }

    public static function addLeaveId($leaveId, $idArr = NULL)
    {
        $a = json_decode($idArr);
        $a[] = $leaveId;
        return json_encode($a);
    }

    public static function showLeaveIds($leaveIds)
    {
        if(empty($leaveIds) || !json_decode($leaveIds)) return '--';

        $show = '';
        $idList = Leave::getHolidayIdList();
        $list = HolidayConfig::getHolidayList();

        foreach (json_decode($leaveIds) ?? [] as $leaveId) {
            $sep = empty($show) ? '' : ", $show";
            $show = $list[$idList[$leaveId]].$sep;
        }
        return $show;
    }
}