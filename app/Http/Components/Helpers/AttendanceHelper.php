<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/14
 * Time: 9:56
 */

namespace App\Http\Components\Helpers;


use App\Models\Attendance\Leave;
use App\Models\Role;
use App\Models\Sys\ApprovalStep;

class AttendanceHelper
{
    /**
     * 显示审核步骤流程
     * @param $stepId
     */
    public static function showApprovalStep($stepId)
    {
        $step = ApprovalStep::findOrFail($stepId);

        $roleId = $roleName = [];
        if(!empty($step->step)) {
            $roleId = json_decode($step->step, true);
        }
        foreach ($roleId as $k => $v) {
            $roleName[] = Role::getRoleText($v);
        }

        $roleName = implode('->', $roleName);
        return $roleName;
    }

    public static function getLeaveStartTime($startTime, $startId)
    {
        return date('Y-m-d', strtotime($startTime)) . ' ' . Leave::$startId[$startId];
    }

    public static function getLeaveEndTime($endTime, $endId)
    {
        return date('Y-m-d', strtotime($endTime)) . ' ' . Leave::$endId[$endId];
    }

}