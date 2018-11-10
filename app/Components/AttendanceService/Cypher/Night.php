<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/6
 * Time: 19:58
 * 夜班加班调休 计算类型
 */
namespace App\Components\AttendanceService\Cypher;


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


        return ['status' => 1,
            'show_memo' => true,
            'memo' => $holidayConfig->memo,
            'show_time' => true,
            'day' => $days,
            'end_day' => date('Y-m-d H:i:s', strtotime($days . end($config['end_time'])) + $holidayConfig->duration * 3600),
            'msg' =>  '<i class="fa fa-info-circle"></i> 调休起效时长最少为:' . $holidayConfig->duration .'小时',
        ];
    }

}