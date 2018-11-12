<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 12:04
 * 加班 计算类型
 */

namespace App\Components\AttendanceService\Cypher;

use App\Components\Helper\DataHelper;
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

    /**
     * 获取申请天数
     * @param $params
     * @return int|number
     */
    public function getLeaveNumberDay($params)
    {
        $numberDay = 0;
        if(empty($params['startId'])) return $numberDay;

        $startId = Leave::$workTimePointChar[$params['startId']]['start_time'];
        $endId = Leave::$workTimePointChar[$params['startId']]['end_time'];
        $numberDay = DataHelper::leaveDayDiff($params['startTime'], $startId, $params['startTime'], $endId);

        return $numberDay;
    }
}