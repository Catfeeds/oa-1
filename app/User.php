<?php

namespace App;


use App\Models\Role;
use App\Models\Sys\Dept;
use App\Models\UserExt;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;
    use LogsActivity;

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    const IS_MOBILE_FALSE = 0;
    const IS_MOBILE_TRUE = 1;

    const IS_LEADER_FALSE = 0;
    const IS_LEADER_TRUE = 1;

    public static $statusList = [
        self::STATUS_ENABLE => '在职',
        self::STATUS_DISABLE => '离职',
    ];

    public static $statusClass = [
        self::STATUS_ENABLE => 'success',
        self::STATUS_DISABLE => 'danger',
    ];

    public static $isMobileList = [
        self::IS_MOBILE_FALSE => '否',
        self::IS_MOBILE_TRUE => '是',
    ];

    public static $isLeader = [
        self::IS_LEADER_FALSE => '否',
        self::IS_LEADER_TRUE => '是',
    ];

    protected $primaryKey = 'user_id';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'alias',
        'email',
        'password',
        'status',
        'role_id',
        'creater_id',
        'remember_token',
        'mobile',
        'is_mobile',
        'dept_id',
        'job_id',
        'is_leader',
        'desc',
    ];

    // 可用 $user->is_admin 来判断角色是否属于 admin
    protected $appends = [
        'is_admin'
    ];

    // active log 使用
    public static $logAttributes = [
        'username',
        'alias',
        'email',
        'password',
        'status',
        'role_id',
        'creater_id',
        'mobile',
        'is_mobile',
        'dept_id',
        'job_id',
        'is_leader',
        'desc',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getIsAdminAttribute()
    {
        return $this->hasRole('admin');
    }

    public function role()
    {
        return $this->hasMany(Role::class, 'id', 'role_id');
    }

    public function dept()
    {
        return $this->hasOne(Dept::class, 'dept_id', 'dept_id');
    }

    public function userExt()
    {
        return $this->hasOne(UserExt::class, 'user_id', 'user_id');
    }

    public static function getStatusList()
    {
        $res = [];
        foreach (self::$statusList as $k => $v) {
            $res[$k] = trans('app.' . $v);
        }
        return $res;
    }

    public static function getAliasList()
    {
        return self::get(['user_id', 'alias'])->pluck('alias', 'user_id')->toArray();
    }

    public static function getUsernameAliasList()
    {
        $users = self::get(['user_id', 'alias', 'username'])->toArray();

        $res = [];
        foreach ($users as $k => $v) {
            $res[$v['user_id']] = $v['alias'] . '('. $v['username'] . ')';
        }

        return $res;
    }

    public static function getStatusText($status)
    {
        return sprintf('<span class="label label-%s">%s</span>', self::$statusClass[$status], self::$statusList[$status]);
    }

    public static function getIsMobileTest($user)
    {
        $value = $user->is_mobile == self::IS_MOBILE_TRUE ? self::IS_MOBILE_TRUE : self::IS_MOBILE_FALSE;
        $list = [
            self::IS_MOBILE_TRUE => 'success',
            self::IS_MOBILE_FALSE => 'warning',
        ];
        return sprintf('<a href="%s"><span class="label label-%s">%s</span></a>', route('user.isMobile', ['id' => $user->user_id, 'is_mobile' => $value == self::IS_MOBILE_FALSE ? self::IS_MOBILE_TRUE : self::IS_MOBILE_FALSE]), $list[$value], self::$isMobileList[$value]);
    }

    public static function getUserAliasToId($userId)
    {
        return self::where(['user_id' => $userId])->first();
    }
}
