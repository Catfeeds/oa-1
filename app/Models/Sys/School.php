<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/1
 * Time: 16:30
 * 学校配置表数据库
 */
namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class School extends Model
{
    use LogsActivity;

    protected $table = 'users_school';

    protected $primaryKey = 'school_id';

    protected $fillable = [
        'school',
    ];

    public static function getSchoolList()
    {
        return self::get(['school_id', 'school'])->pluck('school', 'school_id')->toArray();
    }
}