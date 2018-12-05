<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/25
 * Time: 12:02
 */

namespace App\Models\Sys;

use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\AttendanceHelper;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PunchRulesConfig extends Model
{
    use LogsActivity;

    const DEDUCT_SCORE = 1;
    const DEDUCT_DAY = 2;

    static $deductType = [
        self::DEDUCT_SCORE => '扣分',
        self::DEDUCT_DAY => '扣天',
    ];

    protected $table = 'punch_rules_config';

    protected $primaryKey = 'id';

    protected $fillable = [
        'punch_rules_id',
        'ready_time',
        'work_start_time',
        'work_end_time',
        'rule_desc',
        'late_type',
        'start_gap',
        'end_gap',
        'ded_type',
        'holiday_id',
        'ded_num',
        'is_show_start',
        'is_show_end',
    ];

    public static function getPunchRulesCfgToId($id)
    {
        $config = self::where(['punch_rules_id' => $id])->get()->toArray();
        return self::getPunchRules($config);
    }

    public static function getPunchRules($config)
    {
        $list = $list_ = $arr = $arr_ = [];
        foreach ($config as $k => $v) {

            $sKey = self::resolveFormula($v['work_start_time']);
            $eKey = self::resolveFormula($v['work_end_time']);
            $rKey = self::resolveFormula($v['ready_time']);

            //申请单前端显示
            if(empty($v['is_show_start']))  $list_['start_time'][$sKey] =  $sKey;
            if(empty($v['is_show_end']))  $list_['end_time'][$eKey] =  $eKey;

            $list['start_time'][$sKey] =  $sKey;
            $list['end_time'][$eKey] =  $eKey;

            $arr[$sKey.'$$'.$eKey.'$$'.$rKey]['ded_num'][] = [
                'start_gap' => self::resolveGapFormula($v['start_gap']),
                'end_gap' => self::resolveGapFormula($v['end_gap']),
                'late_type' => $v['late_type'],
                'ded_type' => $v['ded_type'],
                'ded_num' => $v['ded_num'],
            ];
            $arr_[$sKey.'$$'.$eKey.'$$'.$rKey] = strtotime($rKey);
        }

        asort($arr_);
        return [
            'start_time' => array_values($list['start_time'] ?? []),
            'end_time' => array_values($list['end_time'] ?? []),
            'show_start_time' => array_values($list_['start_time'] ?? []),
            'show_end_time' => array_values($list_['end_time'] ?? []),
            'cfg' => $arr,
            'sort' => $arr_,
        ];
    }

    public static function resolveFormula($formula)
    {
        $date = json_decode($formula, true);

        $time = sprintf('%s:%s', !empty($date[3])&&$date[3] > 1 ? $date[3]  : '00',  !empty($date[4])&&$date[4] > 1 ? $date[4]  : '00' );
        unset($date);
        return $time;
    }

    public static function resolveGapFormula($formula)
    {
        $data = json_decode($formula, true);

        $date = [];
        foreach ($data as $k => $v) {
            if(empty($v)) continue;
            switch ($k) {
                case 0 :
                    $date[] = $v;
                    break;
                case 1 :
                    $date[] = $v * 24 * 60 * 60;
                    break;
                case 2 :
                    $date[] = $v * 60 * 60;
                    break;
                case 3 :
                    $date[] = $v * 60;
                    break;
                default :
                    $date[] = $v;
                    break;
            }
        }
        $second = array_sum($date);

        return $second;
    }

    /**
     * 获取一天正常的上班时间与下班时间
     * @param $punchRulesConfigs
     * @return array
     */
    public static function getTodayNormalWorkTime($punchRulesConfigs)
    {
        $minReadyTime = 9999;
        $maxEndTime = 0;
        foreach ($punchRulesConfigs as $punchRulesConfig) {
            $readyTime = self::resolveFormula($punchRulesConfig->ready_time);
            $endTime = self::resolveFormula($punchRulesConfig->work_end_time);
            $minReadyTime = (int)str_replace(':', '', $readyTime) < $minReadyTime ? $readyTime : $minReadyTime;
            $maxEndTime = (int)str_replace(':', '', $endTime) > $maxEndTime ? $endTime : $maxEndTime;
            $arr[$readyTime.'$$'.$endTime] = strtotime($readyTime);
        }
        asort($arr);
        return ['ready_time' => $minReadyTime, 'end_time' => $maxEndTime, 'sort_config' => $arr];
    }
}