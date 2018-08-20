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
        'ready_time',
        'work_start_time',
        'work_end_time',
    ];

    public static $punchType = [
        1 => '正常上班',
        2 => '休息日',
        3 => '节假日',
    ];

    public static function getPunchTypeList()
    {
        return self::get(['punch_type_id', 'name'])->pluck('name', 'punch_type_id')->toArray();
    }

}