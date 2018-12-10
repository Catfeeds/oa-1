<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/1
 * Time: 15:57
 * 部门配置表数据库
 */
namespace App\Models\Sys;

use App\Models\StaffManage\Entry;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Dept extends Model
{
    use LogsActivity;

    protected $table = 'sys_users_dept';

    protected $primaryKey = 'dept_id';

    protected $fillable = [
        'dept',
        'parent_id',
    ];

    public static function getDeptList()
    {
        return self::get(['dept_id', 'dept'])->pluck('dept', 'dept_id')->toArray();
    }

    public function users()
    {
        return $this->hasMany(User::class, 'dept_id', 'dept_id');
    }

    public function entry()
    {
        return $this->hasMany(Entry::class, 'dept_id', 'dept_id');
    }

}