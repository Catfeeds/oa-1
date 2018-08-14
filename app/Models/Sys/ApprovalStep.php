<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/8
 * Time: 20:04
 * 审核流程配置
 */

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ApprovalStep extends Model
{
    use LogsActivity;

    protected $table = 'approval_step';

    protected $primaryKey = 'step_id';

    protected $fillable = [
        'name',
        'step',
        'day',
    ];

}