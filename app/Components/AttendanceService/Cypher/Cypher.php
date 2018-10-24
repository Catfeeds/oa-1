<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 11:18
 */
namespace App\Components\AttendanceService\Cypher;

class Cypher
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
        return ['status' => 1, 'show_memo' => true, 'memo' => $holidayConfig->memo];

    }

    /**
     * 申请单验证和数据返回
     * @param $success
     * @param array $message
     * @param array $data
     * @return array
     */
    public function backCypherData($success, $message = [], $data = [])
    {
        return ['success' => $success, 'message' => $message , 'data' => $data];
    }

}