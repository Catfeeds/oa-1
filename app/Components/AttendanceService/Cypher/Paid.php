<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 11:57
 * 带薪假 计算类型
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

        if(!empty($holidayConfig->cycle_num) && $leaveInfo['count_num'] > $holidayConfig->cycle_num) {
            return $this->backCypherData(false, ['end_time' => '周期内可申请该假期次数为'. $holidayConfig->cycle_num .'次']);
        }

        $data = [];
        if((int)$leaveInfo['number_day'] === 0 || (int)$leaveInfo['number_day'] < $numberDay ) {
            $data['exceed_day'] = $numberDay - (int)$leaveInfo['number_day'];
            $data['exceed_holiday_id'] = $holidayConfig->exceed_change_id;
        }
        return $this->backCypherData(true, [], $data);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        switch ($holidayConfig->reset_type) {
         case HolidayConfig::RESET_ENTRY_TIME:
             $leaveInfo = $this->getUserPayableDayToEntryTime($entryTime, $userId, $holidayConfig);
             return $this->returnData($leaveInfo, $holidayConfig);
             break;
         case HolidayConfig::RESET_NATURAL_CYCLE:
             $leaveInfo = $this->getUserPayableDayToNaturalCycleTime($entryTime, $userId, $holidayConfig);
             return $this->returnData($leaveInfo, $holidayConfig);
             break;
         default:
             $leaveInfo = $this->getUserPayableDayToNoCycleTime($userId, $holidayConfig);
             return $this->returnData($leaveInfo, $holidayConfig);
         }
    }

    public function returnData($leaveInfo, $holidayConfig) {
        $msg = '<i class="fa fa-info-circle"></i>剩余假期天数:' . $leaveInfo['number_day'] . '天';

        return [
            'status' => 1,
            'show_day' => true,
            'show_memo' => true,
            'memo' => $holidayConfig->memo,
            'holiday_name' => $holidayConfig->show_name,
            'holiday_id' => $holidayConfig->holiday_id,
            'number_day' => $leaveInfo['number_day'],
            'count_num' => $leaveInfo['count_num'],
            'msg' => $msg,
        ];
    }

    public function getDaysByScope($scope, $userId, $holidays)
    {
        return parent::getDaysByScope($scope, $userId, $holidays);
    }
}