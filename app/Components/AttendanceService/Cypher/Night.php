<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/6
 * Time: 19:58
 * 夜班加班调休 计算类型
 */
namespace App\Components\AttendanceService\Cypher;


class Night extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        if(!empty($holidayConfig->duration) && $numberDay < $holidayConfig->duration) {
            return $this->backCypherData(false, ['end_time' => '调休起效时长最少为:' . $holidayConfig->duration .'小时']);
        }

        return $this->backCypherData(true);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        return ['status' => 1, 'show_memo' => true, 'memo' => $holidayConfig->memo, 'show_time' => true];
    }

}