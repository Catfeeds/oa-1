<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    use LogsActivity;

    protected $table = 'roles';

    protected static $list = [];

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

    public static function getRoleTextList() {
        if ( ! empty(self::$list)) {
            return self::$list;
        }

        $res = self::all(['id', 'display_name']);
        foreach($res as $v) {
            self::$list[$v->id] = $v->display_name;
        }

        return self::$list;
    }

    public static function getRoleText($id) {
        $list = self::getRoleTextList();
        return isset($list[$id]) ? $list[$id] : '';
    }
}