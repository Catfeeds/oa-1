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

    const LEAVEID = 1;
    const OVERTIME = 2;
    const RECHECK = 3;

    public static $applyType = [
        1 => '请假',
        2 => '加班调休',
        3 => '补打卡',
        0 => ''
    ];

    protected $fillable = [
        'holiday',
        'apply_type_id',
        'memo',
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