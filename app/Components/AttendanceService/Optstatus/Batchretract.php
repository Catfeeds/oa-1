<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/20
 * Time: 9:51
 */

namespace App\Components\AttendanceService\Optstatus;


use App\Models\Attendance\Leave;
use App\Models\Sys\OperateLog;

class Batchretract extends Opt
{
    public function optLeaveStatus($leave, Int $status)
    {
        if($status !== Leave::BATCH_RETRACT_REVIEW && \Entrust::can(['leave.retract']) && $leave->user_id != \Auth::user()->user_id)
            //自定义报错异常，后续可日子记录
            throw new \Exception('错误操作记录');

        $msg = '批量撤回申请';

        $batchUserIds = json_decode($leave->user_list, true);

        //批量更新调休列表成员为撤回状态，排除已经撤回的人员
        foreach ($batchUserIds as $k => $uid) {
            Leave::where(['user_id' => $uid, 'parent_id' => $leave->leave_id])
                ->whereNotIn('status', [Leave::RETRACT_REVIEW])
                ->update(['status' => Leave::RETRACT_REVIEW]);
        }

        $leave->update(['status' => Leave::RETRACT_REVIEW]);

        $this->createOptLog(OperateLog::LEAVED, $leave->leave_id, $msg);

        //微信通知审核人员
        //OperateLogHelper::sendWXMsg($review_user_id, '测试下');

        return ['success' => true];
    }

}