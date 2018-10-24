<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 12:04
 */

namespace App\Components\AttendanceService\Cypher;


class Overtime extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        return parent::check($holidayConfig, $numberDay);
    }

    public function getUserHoliday($userId, $holidayConfig)
    {
        return parent::getUserHoliday($userId, $holidayConfig);
    }

}