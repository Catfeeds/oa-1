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

class Change extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        return $this->backCypherData(true);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        return parent::getUserHoliday($entryTime, $userId, $holidayConfig);
    }

    public function getDaysByScope($scope, $userId, $holidays)
    {
        return parent::getOverDaysByScope($scope, $userId, $holidays, HolidayConfig::CHANGE);
    }
}