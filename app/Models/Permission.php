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

    public static function getPemDesc()
    {
        return self::get(['id', 'display_name'])->pluck('id', 'display_name')->toArray();
    }
    public static function getPemDisDesc()
    {
        return self::get(['description', 'display_name'])->pluck('description', 'display_name')->toArray();
    }

    public static function getPemAllName()
    {
        return self::where('name', 'like', '%all')->get(['id', 'name'])->pluck('id', 'name')->toArray();
    }
    public static function getPemNameNoAll()
    {
        return self::where('name', 'not like', '%all')->get(['name'])->pluck('name')->toArray();
    }
}
