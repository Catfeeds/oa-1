<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/30
 * Time: 17:18
 * 审核流 配置 数据模型
 */

namespace App\Models\Sys;


use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ReviewStepFlowConfig extends Model
{
    use LogsActivity;

    protected $table = 'review_step_flow_config';

    protected $primaryKey = 'id';

    protected $fillable = [
        'step_id',
        'step_order_id',
        'assign_type',
        'assign_uid',
        'group_type_id',
        'assign_role_id',
    ];
}