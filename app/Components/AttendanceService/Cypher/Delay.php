<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/2
 * Time: 14:29
 * 延迟假计算类型
 */

namespace App\Components\AttendanceService\Cypher;


class Delay extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        return $this->backCypherData(true);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        return parent::getUserHoliday($entryTime, $userId, $holidayConfig);
    }

}