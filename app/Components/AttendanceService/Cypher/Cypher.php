<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/17
 * Time: 11:18
 */
namespace App\Components\AttendanceService\Cypher;

use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;

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

    public function getUserHoliday($entryTime, $userId, $holidayConfig)
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

    public function getDaysByScope($scope, $userId, $holidays)
    {
        return $this->getPaidDaysByScope($scope, $userId, $holidays);
    }

    /**
     * 获取scope时间段内请带薪假/无薪假的天数
     * @param $scope
     * @param $userId
     * @param $holidays
     * @return int
     */
    public function getPaidDaysByScope($scope, $userId, $holidays)
    {
        $days = 0;
        foreach ($holidays as $holiday) {
            $days = $days + AttendanceHelper::getUserMonthHoliday($scope, $userId, $holiday)['apply_days'];
        }
        return $days;
    }

    /**
     * 获取scope时间内加班/调休的天数统计
     * @param $scope
     * @param $userId
     * @param $holidays
     * @param $applyType
     * @return int|mixed
     */
    public function getOverDaysByScope($scope, $userId, $holidays, $applyType)
    {
        $holidayIds = [];
        foreach ($holidays as $holiday) {
            if ($holiday->apply_type_id == $applyType) {
                $holidayIds[] = $holiday->holiday_id;
            }
        }
        $leaveIds = Leave::leaveBuilder(date('Y', strtotime($scope['start_time'])), date('m', strtotime($scope['start_time'])))
            ->whereIn('holiday_id', $holidayIds)->where('user_id', $userId)->get()->pluck('leave_id')->toArray();

        return AttendanceHelper::selectChangeInfo('', '', $leaveIds);
    }
}