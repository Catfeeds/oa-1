<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/30
 * Time: 17:18
 * 审核流 配置 数据模型
 */

namespace App\Models\Sys;


use App\Models\Role;
use App\Models\RoleLeaveStep;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ReviewStepFlowConfig extends Model
{
    use LogsActivity;

    protected $table = 'sys_attendance_review_step_flow_config';

    protected $primaryKey = 'id';

    const ASSIGN_USER = 0;
    const ASSIGN_ROLE = 1;

    protected $fillable = [
        'step_id',
        'step_order_id',
        'assign_type',
        'assign_uid',
        'group_type_id',
        'assign_role_id',
    ];

    /**
     * 显示审核步骤人员
     */
    public static function showReviewUser()
    {
        $config = self::all();

        $users = User::getUsernameAliasAndDeptList();

        $leaderStepUid = [];
        foreach ($config as $lk => $lv) {
            if((int)$lv['assign_type'] === self::ASSIGN_USER) {
                $leaderStepUid[$lv['step_id']][$lv['step_order_id']] = $users[$lv['assign_uid']] ?? '未设置';
            }

            if((int)$lv['assign_type'] === self::ASSIGN_ROLE) {
                $roles = Role::where(['id' => $lv['assign_role_id']])->first();
                if(empty($roles->id)) continue;
                $leaderStepUid[$lv['step_id']][$lv['step_order_id']] = $roles->name;
            }
        }
        return $leaderStepUid;
    }
}