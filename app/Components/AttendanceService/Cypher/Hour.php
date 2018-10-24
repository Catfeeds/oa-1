<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/20
 * Time: 11:03
 */

namespace App\Components\AttendanceService\Cypher;


class Hour extends Cypher
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