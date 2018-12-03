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
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\ReviewStepFlow;
use App\Models\UserHoliday;
use App\User;
use EasyWeChat\Kernel\Exceptions\Exception;

class AttendanceHelper
{
    /**
     * 显示审核步骤流程
     * @param $stepUser
     */
    public static function showApprovalStep($stepUser)
    {

        $stepUser = json_decode($stepUser, true);

        if(empty($stepUser)) return '获取审核步骤人员异常';

        $roleName = [];
        foreach ($stepUser as $k => $v) {
            $roleName[] = User::getUsernameAliasList()[$v];
        }

        $roleName = implode('>>', $roleName);
        return $roleName;
    }

    /**
     * 获取申请单开始日期
     * @param $startTime
     * @param $startId
     * @return string
     */
    public static function getLeaveTime($time, $id)
    {
        return date('Y-m-d', strtotime($time)) . ' ' . $id;
    }

    /**
     * 设置上传附件
     * @param $request
     * @return string
     */
    public static function setAnnex($request, $imagePath = '') {

        $file = 'annex';
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
     * 回退功能暂时不用
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

    public static function spliceLeaveTime($holidayId, $time, $timeId, $numberDay = 0)
    {
        $holidayCfg =  HolidayConfig::getHolidayApplyList();

        $day = DataHelper::dateTimeFormat($time, 'Y-m-d');

        $msg = '天';
        switch ($holidayCfg[$holidayId]) {
            case HolidayConfig::LEAVEID:
                return [
                    'time'=> DataHelper::dateTimeFormat($day .' '. $timeId, 'Y-m-d H:i'),
                    'number_day' => $numberDay . $msg
                ];
                break;
            case HolidayConfig::CHANGE:
                return [
                    'time'=> $day,
                    'number_day' => Leave::$workTimePoint[(int)$numberDay] ?? '数据异常',
                ];
                break;
            case HolidayConfig::RECHECK:
                return [
                    'time'=> $day,
                    'number_day' => DataHelper::dateTimeFormat($time, 'H:i')
                ];
                break;
            default:
                return [
                    'time'=> '',
                    'number_day' => ''
                ];
        }
    }

    /**
     * 获取有关自己抄送人员申请单列表
     * @param null $deptId
     * @return string
     */
    public static function getCopyLeaveWhere($scope, $userId, $field)
    {
        $userIds = [];
        $res = self::getCopyLeaveId($scope, $userId, $field);

        if(!empty($res['leave_id'])) {
            $leaveIds = implode(',', $res['leave_id']);
            $where = " AND Leave_id in ($leaveIds)";
            $userIds = $res['user_ids'];
        } else {
            $where = " AND Leave_id in (-1)";
        }

        return ['where' => $where, 'user_ids' => $userIds];
    }


    /**
     * 获取抄送者ID，和申请单ID
     * @param int $deptId
     * @return array
     */
    public static function getCopyLeaveId($scope, $userId, $field)
    {
        $leaveIds = $userIds = [];
        $leave = Leave::with('holidayConfig')
            ->whereRaw($scope->where)
            ->whereRaw( $field . ' != "" and ' . sprintf('JSON_EXTRACT('.$field.', "$.id_%d") = "%d"', $userId, $userId))
            ->get();

        if(!empty($leave)) {
            foreach ($leave as $k => $v) {
                if(!empty($v->holidayConfig)  && $field == 'copy_user') {
                    $userIds[$v->leave_id] = json_decode($v->copy_user, true);
                    $leaveIds[] = $v->leave_id;
                }
            }
        }

        return ['leave_id' => $leaveIds, 'user_ids' => $userIds];
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

    public static function resolveCycleConfigFormula($formula)
    {
        $format = json_decode($formula, true);
        if(empty($format)) return [];

        $date = [];
        foreach ($format as $k => $v) {
            if(empty($v)) continue;
            if($v <10) $v = '0'.$v;
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
        $claimTime = DataHelper::dateTimeAddToFormula($entryTime, $holiday->payable_claim_formula);
        //入职未满配置时间范围，返回天数为0
        if(empty($entryTime) || strtotime($claimTime) > time()) return 0;
        //获取带薪假期重置配置信息
        $resetTime = DataHelper::dateTimeAddToFormula($entryTime, $holiday->payable_reset_formula);

        //开始默认为带薪起效时间
        $startTime= $claimTime;
        //结束默认为带薪重置时间
        $endTime = $resetTime;

        //到期时间之后，重置开始时间和到期时间
        if(strtotime($endTime) < time()) {
            $startTime = DataHelper::dateTimeFormat('now', 'Y') . '-' . DataHelper::dateTimeFormat($endTime, 'm-d H:i:s');
            $endTime = DataHelper::dateTimeAddToFormula($startTime, $holiday->payable_reset_formula);
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
        $claimTime = DataHelper::dateTimeAddToFormula($entryTime, $holiday->payable_claim_formula);
        //入职未满配置时间范围，返回天数为0
        if(empty($entryTime) || strtotime($claimTime) > time()) return 0;

        $resetDate = self::resolveCycleConfigFormula($holiday->payable_reset_formula);
        $resetTime = sprintf('%s %s:%s:%s', $resetDate['d'] ??  '01', $resetDate['h'] ?? '00', $resetDate['i'] ?? '00', $resetDate['s'] ?? '00');
        $resetMoney = DataHelper::dateTimeFormat('now', 'Y') .'-'. DataHelper::dateTimeFormat('now', 'm') . '-' . $resetTime;

        if(array_key_exists('m', $resetDate)) {
            $startTime = DataHelper::dateTimeFormat('now', 'Y')  .'-'. $resetDate['m'] . '-' . $resetTime;
            $endTime = DataHelper::dateTimeAdd($startTime, '1Y');
        } elseif(empty($resetDate['m']) && !empty($resetDate['d'])) {
            $startTime = $resetMoney;
            $endTime = DataHelper::dateTimeAdd($startTime, '1M');
        } elseif(empty($resetDate['m']) && empty($resetDate['d']) && !empty($resetDate['h'])) {
            $startTime = $resetMoney;
            $endTime = DataHelper::dateTimeAdd($startTime, '1D');
        } else {
            $startTime = $resetMoney;
            $endTime = DataHelper::dateTimeAdd($startTime, 'T1H');
        }

        return self::selectLeaveInfo($startTime, $endTime, $userId, $holiday);
    }

    public static function getUserPayableDayToNoCycleTime($userId, $holiday)
    {
        $startTime = date('Y').'-01-01';
        $endTime = date('Y').'-12-31';
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
     * 查询员工 周期内 申请信息
     * @param $startDay
     * @param $endDay
     * @param $userId
     * @param $holiday
     * @return array number_day 申请天数 count_num申请次数
     */
    public static function selectLeaveInfo($startDay, $endDay, $userId, $holiday)
    {
        $userLeaveLog = Leave::select([
                \DB::raw('SUM(number_day) number_day'),
                \DB::raw('count(*) count_num'),
            ])
            ->where('start_time', '>=', $startDay)
            ->where('end_time', '<=', $endDay)
            ->whereIn('status', [Leave::PASS_REVIEW, Leave::WAIT_REVIEW, Leave::ON_REVIEW, Leave::WAIT_EFFECTIVE])
            ->where([
                'user_id' => $userId,
                'holiday_id' => $holiday->holiday_id,
            ])->groupBy('user_id')->first('number_day');

        $numberDay = empty($userLeaveLog->number_day) ? $holiday->up_day : $holiday->up_day - $userLeaveLog->number_day;

        return ['number_day' => $numberDay, 'count_num' => $userLeaveLog->count_num ?? 0, 'apply_days' => $userLeaveLog->number_day ?? 0];
    }

    /**
     * 审核通过后, 上班打卡字段与下班打卡字段的设置
     * @param $leave
     * @param $startDay
     * @param $endDay
     * @return array
     */
    /*public static function getPunch($leave, $startDay, $endDay)
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
    }*/
/*
    public static function addLeaveId($leaveId, $idArr = NULL)
    {
        $a = json_decode($idArr);
        $a[] = $leaveId;
        return json_encode($a);
    }*/

    public static function showLeaveIds($leaveIds)
    {
        if (empty($leaveIds) || !json_decode($leaveIds)) return '--';

        $show = '';
        $idList = Leave::getHolidayIdList(true);
        $list = HolidayConfig::getHolidayList(true);

        foreach (json_decode($leaveIds) ?? [] as $leaveId) {
            $sep = empty($show) ? '' : ", $show";
            $show = ($list[$idList[$leaveId] ?? ''] ?? '') . $sep;
        }
        return $show;
    }

}