<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/1
 * Time: 15:57
 * 部门配置表数据库
 */
namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Dept extends Model
{
    use LogsActivity;

    protected $table = 'users_dept';

    protected $primaryKey = 'dept_id';

    protected $fillable = [
        'dept',
        'parent_id',
    ];

    public static function getDeptList()
    {
        return self::get(['dept_id', 'dept'])->pluck('dept', 'dept_id')->toArray();
    }

}