<?php

namespace App\Models\Sys;

use App\Models\Material\Apply;
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

    public function apply()
    {
        return $this->belongsToMany(Apply::class, 'material_apply_inventory', 'inventory_id', 'apply_id')->withPivot('part');
    }
}
