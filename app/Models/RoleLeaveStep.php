<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/10
 * Time: 11:36
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleLeaveStep extends Model
{
    protected $primaryKey = null;

    public $incrementing = false;

    protected $table = 'roles_leave_step';

    protected $fillable = [
        'role_id',
        'step_id',
    ];

    // 没有 timestamps 相关字段
    public $timestamps = false;

}