<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/20
 * Time: 11:03
 * 小时假 计算类型
 */
namespace App\Components\AttendanceService\Cypher;

class Hour extends Cypher
{
    public function check($holidayConfig, $numberDay)
    {
        return $this->backCypherData(true);
    }

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
    {
        return [
            'status' => 1,
            'show_memo' => true,
            'memo' => $holidayConfig->memo,
            'close_time' => true,
        ];
    }

    /**
     * 重新组装时间
     * @return array
     */
    public function buildUpLeaveTime($startTime, $endTime, $startId, $endId)
    {
       //小时假，默认 19:00 ~ 20:00
        $startId = '19:00';
        $endId = '20:00';
        $endTime = $startTime;

        return [
            'start_time' => $startTime,
            'endTime' => $endTime,
            'startTimeS' => trim($startTime .' '. $startId),
            'endTimeS' => trim($startTime .' '. $endId),
        ];
    }
}