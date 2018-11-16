<?php
/**
 * Created by PhpStorm.
 * User: wangyingjie
 * Date: 2018/11/15
 * Time: 16:22
 */

namespace App\Components\AttendanceService\Operate;


use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\Attendance\Leave;
use App\Models\Material\Apply;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\ReviewStepFlow;
use App\User;

class Material extends Operate
{
    public function getLeaveStep($request = NULL, $numberDay = 0) : array
    {
        $step = ReviewStepFlow::with('config')->where('apply_type_id', HolidayConfig::MATERIAL)->first()->toArray();
        if(empty($step['config'])) return self::backLeaveData(false, ['material_id' => trans('申请失败, 未设置部门审核人员，有疑问请联系人事')]);

        $leaderStepUid = [];
        foreach ($step['config'] as $lk => $lv) {

            if((int)$lv['assign_type'] === 0) {
                $leaderStepUid[$lv['step_order_id']] = $lv['assign_uid'];
            }

            if((int)$lv['assign_type'] === 1) {
                $roleId = sprintf('JSON_EXTRACT(role_id, "$.id_%d") = "%d"', $lv['assign_role_id'], $lv['assign_role_id']);
                $dept = ' and dept_id ='.\Auth::user()->dept_id ;
                if((int)$lv['group_type_id'] === 1) $dept = '';
                $userLeader = User::whereRaw( $roleId . $dept )->first();
                if(empty($userLeader->user_id)) return self::backLeaveData(false, ['material_id' => trans('申请失败, 未设置部门主管权限，有疑问请联系人事')]);
                $leaderStepUid[$lv['step_order_id']] = $userLeader->user_id;
            }
        }

        ksort($leaderStepUid);
        $stepUser = json_encode($leaderStepUid);
        $reviewUserId = reset($leaderStepUid);
        array_shift($leaderStepUid);

        if(empty($leaderStepUid)) {
            $remainUser = '';
        } else {
            $remainUser = json_encode($leaderStepUid);
        }

        $stepId = $step['step_id'];

        $data = [
            'step_id' => $stepId,
            'remain_user'  => $remainUser,
            'review_user_id' => $reviewUserId,
            'step_user' => $stepUser,
        ];

        return self::backLeaveData(true, [], $data);
    }

    public function checkLeave($request)
    {
        //验证....
    }

    public function leaveReviewPass($apply)
    {
        if(empty($apply->remain_user)) {
            $apply->update(['state' => Apply::APPLY_BORROW, 'review_user_id' => 0]);
        } else {
            $remainUser = json_decode($apply->remain_user, true);

            $reviewUserId = reset($remainUser);
            array_shift($remainUser);

            if(empty($remainUser)) {
                $remainUser = '';
            } else {
                $remainUser = json_encode($remainUser);
            }

            $apply->update(['state' => Apply::APPLY_REVIEW, 'review_user_id' => $reviewUserId, 'remain_user' => $remainUser]);
        }
    }
}