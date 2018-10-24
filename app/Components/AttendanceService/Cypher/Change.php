<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 11:57
 */

namespace App\Components\AttendanceService\Cypher;


class Change extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        return $this->backCypherData(true);
    }

    public function getUserHoliday($userId, $holidayConfig)
    {
        return parent::getUserHoliday($userId, $holidayConfig);
    }

}