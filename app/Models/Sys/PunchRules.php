<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/18
 * Time: 16:49
 * 上下班时间规则数据库
 */

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PunchRules extends Model
{
    use LogsActivity;

    protected $table = 'punch_rules';

    protected $primaryKey = 'id';

    protected $fillable = [
        'punch_type_id',
        'name',

        //待删除
        'ready_time',
        'work_start_time',
        'work_end_time',
    ];

    const NORMALWORK = 1;
    const RESTDAY = 2;
    const HOLIDAY = 3;
    const HOLIDAY_WORK = 4;

    const BEGIN_TIME = '7:00';//以早上7:00为上班打卡起始点
    const END_TIME = '24:00';//24:00为下班结束点 超过24:00 以 24:00+ 计

    const LATE_WORK = 1;
    const LATE_OFF_WORK = 2;


    public static $punchType = [
        self::NORMALWORK => '正常上班',
        self::RESTDAY => '休息日',
        self::HOLIDAY => '节假日',
        self::HOLIDAY_WORK => '节假日加班',
    ];

    public static $lateType = [
        self::LATE_WORK => '上班迟到',
        self::LATE_OFF_WORK => '下班早退'
    ];

    public static $punchTypeChar = [
        self::NORMALWORK => '班',
        self::RESTDAY => '休',
        self::HOLIDAY => '假',
    ];

    public static $punchTypeColor = [
        self::NORMALWORK => '#337ab7',
        self::RESTDAY => '#1ab394',
        self::HOLIDAY => '#b31a57'
    ];

    public static function getPunchTypeList()
    {
        return self::get(['punch_type_id', 'name'])->pluck('name', 'punch_type_id')->toArray();
    }

    public function config()
    {
        return $this->hasMany(PunchRulesConfig::class, 'punch_rules_id', 'id');
    }

    public static function getPunchRulesList(){
        return self::get(['id', 'name'])->pluck('name', 'id')->toArray();
    }

}