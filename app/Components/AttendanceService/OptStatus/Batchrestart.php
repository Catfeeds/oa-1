<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/20
 * Time: 9:51
 */

namespace App\Components\AttendanceService\OptStatus;

use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\OperateLog;

class Batchrestart extends Opt
{
    public function optLeaveStatus($leave, Int $status)
    {
        if($status !== Leave::RESTART_REVIEW && !in_array($leave->status, Leave::$restartList)
            && !\Entrust::can(['leave.restart']) && $leave->user_id !== \Auth::user()->user_id)
            //自定义报错异常，后续可日子记录
            throw new \Exception('错误操作记录');

        $url = redirect()->route('leave.restart', ['apply_type_id' => HolidayConfig::OVERTIME, 'id' => $leave->leave_id]);

        if(empty($url)) throw new \Exception('错误操作记录');

        //微信通知审核人员
        //OperateLogHelper::sendWXMsg($review_user_id, '测试下');

        return ['success' => true, 'url' => $url->getTargetUrl()];
    }

}