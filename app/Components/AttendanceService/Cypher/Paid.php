<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 11:57
 */

namespace App\Components\AttendanceService\Cypher;


use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\Sys\HolidayConfig;

class Paid extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        //带薪假，假期下限天数判断
        $minDay = $holidayConfig->under_day;

        if(!empty($minDay) && $numberDay < $minDay) {
            return $this->backCypherData(false, ['end_time' => '申请假期最短为'. $minDay. '天']);
        }

        $leaveInfo = self::getUserHoliday(\Auth::user()->userExt->entry_time, \Auth::user()->user_id, $holidayConfig);
        if($leaveInfo['count_num'] > $holidayConfig->cycle_num) {
            return $this->backCypherData(false, ['end_time' => '周期内可申请该假期次数为'. $holidayConfig->cycle_num .'次']);
        }

        return $this->backCypherData(true);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        switch ($holidayConfig->reset_type) {
         case HolidayConfig::RESET_ENTRY_TIME:
             $leaveInfo = AttendanceHelper::getUserPayableDayToEntryTime($entryTime, $userId, $holidayConfig);
             $msg = $day = '<i class="fa fa-info-circle"></i>剩余假期天数:' . $leaveInfo['number_day'] . '天';
             return [
                 'status' => 1,
                 'show_day' => true,
                 'show_memo' => true,
                 'memo' => $holidayConfig->memo,
                 'number_day' => $leaveInfo['number_day'],
                 'count_num' => $leaveInfo['count_num'],
                 'msg' => $msg
             ];
             break;
         case HolidayConfig::RESET_NATURAL_CYCLE:
             $leaveInfo = AttendanceHelper::getUserPayableDayToNaturalCycleTime($entryTime, $userId, $holidayConfig);
             $msg = $day = '<i class="fa fa-info-circle"></i>剩余假期天数:' . $leaveInfo['number_day'] . '天';
             return [
                 'status' => 1,
                 'show_day' => true,
                 'show_memo' => true,
                 'memo' => $holidayConfig->memo,
                 'number_day' => $leaveInfo['number_day'],
                 'count_num' => $leaveInfo['count_num'],
                 'msg' => $msg
             ];
             break;
         default:
             $leaveInfo = AttendanceHelper::getUserPayableDayToNoCycleTime($userId, $holidayConfig);
             $msg = $day = '<i class="fa fa-info-circle"></i>剩余假期天数:' . $leaveInfo['number_day'] . '天';
             return [
                 'status' => 1,
                 'show_day' => true,
                 'show_memo' => true,
                 'memo' => $holidayConfig->memo,
                 'number_day' => $leaveInfo['number_day'],
                 'count_num' => $leaveInfo['count_num'],
                 'msg' => $msg
             ];
         }
    }

    public function getDaysByScope($scope, $userId, $holidays)
    {
        return parent::getDaysByScope($scope, $userId, $holidays);
    }
}