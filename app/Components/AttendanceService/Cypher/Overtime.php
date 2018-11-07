<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 12:04
 * 加班 计算类型
 */

namespace App\Components\AttendanceService\Cypher;

use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;

class Overtime extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        return parent::check($holidayConfig, $numberDay);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        $pointList = [];
        foreach (Leave::$workTimePoint as $k => $v){
            $pointList[] = ['id' => $k, 'text' => $v];
        }

        return [
            'status' => 1,
            'show_day' => true,
            'show_memo' => true,
            'memo' => $holidayConfig->memo,
            'point_list' => $pointList,
        ];
    }

    public function getDaysByScope($scope, $userId, $holidays)
    {
        return parent::getOverDaysByScope($scope, $userId, $holidays, HolidayConfig::OVERTIME);
    }
}