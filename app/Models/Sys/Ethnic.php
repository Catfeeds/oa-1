<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/29
 * Time: 16:07
 */

namespace App\Models\Sys;

use App\Models\StaffManage\Entry;
use App\Models\UserExt;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Ethnic extends Model
{
    use LogsActivity;

    protected $table = 'sys_users_ethnic';

    protected $primaryKey = 'ethnic_id';

    protected $fillable = [
        'ethnic',
        'sort',
    ];

    public static function getEthnicList()
    {
        return self::orderBy('sort', 'asc')->get(['ethnic_id', 'ethnic'])->pluck('ethnic', 'ethnic_id')->toArray();
    }

    public function usersExt()
    {
        return $this->hasMany(UserExt::class, 'ethnic_id', 'ethnic_id');
    }

    public function entry()
    {
        return $this->hasMany(Entry::class, 'ethnic_id', 'ethnic_id');
    }
}