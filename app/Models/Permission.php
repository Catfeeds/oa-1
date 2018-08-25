<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    use LogsActivity;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    // active log 使用
    public static $logAttributes = [
        'name',
        'display_name',
        'description',
    ];
}
