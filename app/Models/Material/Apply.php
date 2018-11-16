<?php

namespace App\Models\Material;

use App\Models\Sys\Inventory;
use Illuminate\Database\Eloquent\Model;

class Apply extends Model
{
    const APPLY_SUBMIT = 0;
    const APPLY_BORROW = 1;
    const APPLY_RETURN = 2;

    const APPLY_REVIEW = 3;
    const APPLY_PASS = 4;
    const APPLY_FAIL = 5;
    const APPLY_REDRAW = 6;
    const APPLY_CANCEL = 7;


    static $stateChar = [
        self::APPLY_SUBMIT  => '申请中',
        self::APPLY_REVIEW => '审批中',
        self::APPLY_BORROW => '借用中',
        self::APPLY_PASS => '已通过',
        self::APPLY_RETURN  => '已归还',
        self::APPLY_FAIL => '拒绝通过',
        self::APPLY_REDRAW => '撤回',
        self::APPLY_CANCEL => '取消',
    ];

    protected $table = 'material_apply';
    protected $fillable = [
        'reason', 'expect_return_time', 'state', 'annex', 'user_id',
        'review_user_id', 'remain_user', 'step_user', 'step_id'
    ];

    public function inventory()
    {
        return $this->belongsToMany(Inventory::class, 'material_apply_inventory', 'apply_id', 'inventory_id');
    }
}
