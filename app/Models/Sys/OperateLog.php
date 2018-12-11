<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/13
 * Time: 21:11
 */

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OperateLog extends Model
{
    use LogsActivity;

    protected $table = 'operate_log';

    protected $primaryKey = 'id';

    //申请单
    const LEAVED = 1;
    const ENTRY = 2;

    public static $appId = [
        self::LEAVED,
        self::ENTRY,
    ];

    protected $fillable = [
        'type_id',
        'info_id',
        'opt_uid',
        'memo',
        'opt_name',
    ];

}