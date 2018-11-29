<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/1
 * Time: 15:57
 * 岗位配置表数据库
 */
namespace App\Models\Sys;

use App\Models\StaffManage\Entry;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Job extends Model
{
    use LogsActivity;

    protected $table = 'users_job';

    protected $primaryKey = 'job_id';

    protected $fillable = [
        'job',
    ];

    public static function getJobList()
    {
        return self::get(['job_id', 'job'])->pluck('job', 'job_id')->toArray();
    }

    public function users()
    {

        return $this->hasMany(User::class, 'job_id', 'job_id');
    }

    public function entry()
    {
        return $this->hasMany(Entry::class, 'job_id', 'job_id');
    }

}