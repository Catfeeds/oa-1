<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/21
 * Time: 16:06
 * 打卡导入日志记录
 */

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PunchRecord extends Model
{
    use LogsActivity;

    protected $table = 'punch_record';

    protected $primaryKey = 'id';

    public static $status = [
        0 => '未生成',
        1 => '生成中',
        2 => '生成失败',
        3 => '生成完成',
    ];

    protected $fillable = [
        'name',
        'annex',
        'log_file',
        'status',
    ];

}