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

    protected $fillable = [
        'type_id',
        'info_id',
        'opt_uid',
        'memo',
        'opt_name',
    ];

}