<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 12:04
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
        return parent::getUserHoliday($entryTime, $userId, $holidayConfig);
    }

    public function getDaysByScope($scope, $userId, $holidays)
    {
        return parent::getOverDaysByScope($scope, $userId, $holidays, HolidayConfig::OVERTIME);
    }
}