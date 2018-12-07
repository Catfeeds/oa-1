<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/6
 * Time: 19:58
 * 夜班加班调休 计算类型
 */
namespace App\Components\AttendanceService\Cypher;

use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\PunchRulesConfig;

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
        $days = date('Y-m-d', time());

        list($year, $month, $day) = explode('-', $days);
        $punchRules = Calendar::where(['year' => (int)$year, 'month' => (int)$month, 'day' => (int)$day])->first();
        $config = PunchRulesConfig::getPunchRulesCfgToId($punchRules->punch_rules_id);

        $startId = end($config['end_time']);

        return ['status' => 1,
            'show_memo' => true,
            'memo' => $holidayConfig->memo,
            'show_time' => true,
            'day' => $days,
            'start_id' => [$startId],
            'end_day' => date('Y-m-d H:i:s', strtotime($days . end($config['end_time'])) + $holidayConfig->duration * 3600),
            'msg' =>  '<i class="fa fa-info-circle"></i> 调休起效时长最少为:' . $holidayConfig->duration .'小时',
        ];
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

        $startTime = strtotime($params['startTime']. ''. $params['startId']);
        if($startTime > strtotime($params['endTime'])) return $numberDay;

        $numberDay = (strtotime($params['endTime'])-$startTime) / 3600;

        return $numberDay;
    }

    /**
     * @param $data
     * @return array
     */
    public function spliceLeaveTime($params)
    {
        return [
            'time'=> DataHelper::dateTimeFormat($params['time'] .' '. $params['timeId'], 'Y-m-d H:i'),
            'number_day' => $params['number_day'] . '小时',
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
折合小时：'.$msgArr['number_day'].'
点击此处查看申请详情[<a href = "'.$msgArr['url'].'">点我前往</a>]';

        //企业微信通知审核人员
        OperateLogHelper::sendWXMsg($msgArr['send_user'], $content);
    }

}