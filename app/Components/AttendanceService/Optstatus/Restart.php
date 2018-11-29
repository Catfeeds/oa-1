<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/16
 * Time: 17:17
 * 申请单 重启
 */

namespace App\Components\AttendanceService\Optstatus;


use App\Models\Attendance\Leave;

class Restart
{
    public function optLeaveStatus($leave, Int $status)
    {
        if($status !== Leave::RESTART_REVIEW && !in_array($leave->status, Leave::$restartList)
            && !\Entrust::can(['leave.restart']) && $leave->user_id !== \Auth::user()->user_id)
            //自定义报错异常，后续可日子记录
            throw new \Exception('错误操作记录');

        $url = redirect()->route('leave.restart', ['apply_type_id' => $leave->holidayConfig->apply_type_id, 'id' => $leave->leave_id]);
        if(empty($url)) throw new \Exception('错误操作记录');
        //微信通知审核人员
        //OperateLogHelper::sendWXMsg($review_user_id, '测试下');

        return ['success' => true, 'url' => $url->getTargetUrl()];
    }

}