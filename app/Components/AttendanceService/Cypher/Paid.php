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

        return $this->backCypherData(true);
    }

    public function getUserHoliday($userId, $holidayConfig)
    {
        $entryTime = \Auth::user()->userExt->entry_time;
        switch ($holidayConfig->reset_type) {
         case HolidayConfig::RESET_ENTRY_TIME:
             $numberDay = AttendanceHelper::getUserPayableDayToEntryTime($entryTime, $userId, $holidayConfig);
             $msg = $day = '<i class="fa fa-info-circle"></i>剩余假期天数:.' . $numberDay . '天';
             return ['status' => 1, 'show_day' => $numberDay ? true : false, 'show_memo' => true, 'memo' => $holidayConfig->memo, 'number_day' => $numberDay, 'msg' => $msg];
             break;
         case HolidayConfig::RESET_NATURAL_CYCLE:
             $numberDay = AttendanceHelper::getUserPayableDayToNaturalCycleTime($entryTime, $userId, $holidayConfig);
             $msg = $day = '<i class="fa fa-info-circle"></i>剩余假期天数:.' . $numberDay . '天';
             return ['status' => 1, 'show_day' => $numberDay ? false : true, 'show_memo' => true, 'memo' => $holidayConfig->memo, 'number_day' => $numberDay, 'msg' => $msg];
             break;
         default:
             return ['status' => 1, 'show_memo' => true, 'memo' => $holidayConfig->memo];
        }
    }
}