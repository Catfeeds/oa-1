<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/16
 * Time: 10:28
 * 申请单 撤回申请
 */
namespace App\Components\AttendanceService\Optstatus;

use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\OperateLog;

class Retract extends Opt
{
    public function optLeaveStatus($leave, Int $status)
    {
        if($status !== Leave::RETRACT_REVIEW && \Entrust::can(['leave.retract']) && $leave->user_id != \Auth::user()->user_id)
            //自定义报错异常，后续可日子记录
            throw new \Exception('错误操作记录');

        $msg = '撤回申请';

        if($leave->holidayConfig->cypher_type === HolidayConfig::CYPHER_OVERTIME) {
            $userList = json_decode($leave->user_list, true);
            if(!empty($userList)) {
                unset($userList['id_'.\Auth::user()->user_id]);
                Leave::where(['user_id' => \Auth::user()->user_id, 'parent_id' => $leave->leave_id])
                    ->update(['status' => $status]);
                //更新主申请单申请人员列表
                $leave->update(['user_list' => json_encode($userList)]);
            } else {
                $leave->update(['status' => $status]);
            }

            $msg = '撤回加班申请';
        } else {
            $leave->update(['status' => $status]);
        }

        $this->createOptLog(OperateLog::LEAVED, $leave->leave_id, $msg);
        //微信通知审核人员
        //OperateLogHelper::sendWXMsg($review_user_id, '测试下');

        return ['success' => true];
    }

}