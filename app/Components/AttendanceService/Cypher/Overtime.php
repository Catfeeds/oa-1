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
use App\Http\Components\Helpers\OperateLogHelper;
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

    /*public function getDaysByScope($scope, $userId, $holidays)
    {
        return parent::getOverDaysByScope($scope, $userId, $holidays, HolidayConfig::OVERTIME);

    }/*

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

    /**
     * @param $params
     * @return array
     */
    public function spliceLeaveTime($params)
    {
        return [
            'time'=> $params['time'],
            'number_day' => Leave::$workTimePoint[(int)$params['number_day']] ?? '数据异常',
        ];
    }

    /**
     * 微信消息内容
     * @param $msgArr
     */
    public function sendWXContent($msgArr)
    {
        $content = '【'.$msgArr['applyType'].'】'.$msgArr['notice'].'
申请人：'.$msgArr['username'].'
所属部门：'.$msgArr['dept'].'
申请事项：'.$msgArr['holiday'].'
开始时间：'.$msgArr['start_time'].'
结束时间：'.$msgArr['end_time'].'
折合天数：'.Leave::$workTimePoint[$msgArr['number_day']] ?? '获取异常'.'
点击此处查看申请详情[<a href = "'.$msgArr['url'].'">点我前往</a>]';

        //企业微信通知审核人员
        OperateLogHelper::sendWXMsg($msgArr['send_user'], $content);
    }

}