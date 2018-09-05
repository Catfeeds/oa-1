<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/7
 * Time: 16:54
 * 假期配置表数据库
 */

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class HolidayConfig extends Model
{
    use LogsActivity;

    protected $table = 'users_holiday_config';

    protected $primaryKey = 'holiday_id';

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    const LEAVEID = 1;
    const CHANGE = 2;
    const OVERTIME = 2;
    const RECHECK = 3;

    const NO_SETTING = 0;
    const GO_WORK = 1;
    const OFF_WORK = 2;

    public static $applyType = [
        self::LEAVEID => '请假',
        self::CHANGE => '加班调休',
        self::RECHECK => '补打卡',
    ];

    public static $driverType = [
        self::LEAVEID => 'leaveEd',
        self::CHANGE => 'charge',
        self::RECHECK => 'reCheck',
    ];

    public static $isBoon = [
        self::STATUS_DISABLE => '否',
        self::STATUS_ENABLE => '是',

    ];

    public static $condition = [
        1 => '按年重置',
        2 => '按月重置'
    ];

    public static $punchType = [
        self::NO_SETTING => '不设置',
        self::GO_WORK => '上班补卡',
        self::OFF_WORK => '下班补卡'
    ];

    protected $fillable = [
        'holiday',
        'apply_type_id',
        'memo',
        'is_boon',
        'is_renew',
        'is_annex',
        'condition_id',
        'restrict_sex',
        'punch_type',
        'num',
    ];

    public static function getHolidayList()
    {
        return self::get(['holiday_id', 'holiday'])->pluck('holiday', 'holiday_id')->toArray();
    }

    public static function getHolidayApplyList()
    {
        return self::get(['holiday_id', 'apply_type_id'])->pluck('apply_type_id', 'holiday_id')->toArray();
    }

    public static function getHolidayListExceptRecheck(){
        return self::where('apply_type_id', '<>', self::RECHECK)->get(['holiday_id', 'holiday'])->pluck('holiday', 'holiday_id')->toArray();
    }
}