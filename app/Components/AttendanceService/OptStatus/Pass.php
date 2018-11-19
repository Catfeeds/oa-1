<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/16
 * Time: 10:27
 * 申请单 审核通过
 */
namespace App\Components\AttendanceService\OptStatus;

use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\OperateLog;

class Pass extends Opt
{
    public function optLeaveStatus($leave, Int $status)
    {
        if($status !== Leave::PASS_REVIEW && !in_array($leave->status, [Leave::WAIT_REVIEW, Leave::ON_REVIEW])
            && !\Entrust::can(['leave.review']) && $leave->review_user_id !== \Auth::user()->user_id)
            //自定义报错异常，后续可日子记录
            throw new \Exception('错误操作记录');

        $msg = '申请通过';
        //驱动
        $driver = HolidayConfig::$driverType[$leave->holidayConfig->apply_type_id];
        \AttendanceService::driver($driver)->leaveReviewPass($leave);

        $this->createOptLog(OperateLog::LEAVED, $leave->leave_id, $msg);


        //微信通知审核人员
        //OperateLogHelper::sendWXMsg($review_user_id, '测试下');

        return ['success' => true];
    }

}