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

    protected $fillable = [
        'holiday',
        'memo',
        'num',
    ];

    public static function getHolidayList()
    {
        return self::get(['holiday_id', 'holiday'])->pluck('holiday', 'holiday_id')->toArray();
    }
}