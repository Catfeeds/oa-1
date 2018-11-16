<?php

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'material_inventory';
    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    static $isShow = [
        self::STATUS_DISABLE => '否',
        self::STATUS_ENABLE => '是',
    ];

    protected $fillable = [
        'type', 'name', 'content', 'description', 'inv_remain', 'company', 'is_annex', 'is_show'
    ];
}
