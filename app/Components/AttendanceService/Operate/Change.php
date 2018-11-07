<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/3
 * Time: 19:53
 * 申请调休
 */
namespace App\Components\AttendanceService\Operate;

use App\Components\AttendanceService\AttendanceInterface;
use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;

class Change extends Operate implements AttendanceInterface
{
    /**
     * 检验申请单验证
     * @param $request
     * @return array
     */
    public function checkLeave($request) : array
    {
        $p = $request->all();
        if(empty($p['end_time'])) unset($this->_validateRule['end_time']);
        $this->validate($request, $this->_validateRule);
        //假期配置ID
        $holidayId = $p['holiday_id'];

        //批量调休人员名单
        $userList = $p['dept_users'] ?? '';
        $copyUser = $p['copy_user'] ?? '';

        //针对夜班调休情况判断
        if(!empty($p['end_time']) || !empty($p['start_id']) && strlen($p['start_id']) >= 3 ) {
            $startTime = (string)$p['start_time'];
            $endTime =  (string)$p['end_time'];
            $startTimeS = trim($startTime .' '. $p['start_id']);
            //时间判断
            if(strtotime($startTimeS) > strtotime($endTime)) {
                return $this->backLeaveData(false, ['end_time' => trans('请选择有效的时间范围')]);
            }
            //申请时间
            $numberDay = sprintf("%.1f", (strtotime($endTime) - strtotime($startTimeS))/3600) ;
            $startId = $p['start_id'];
            $endId = DataHelper::dateTimeFormat($p['end_time'], 'H:i');

        } else {
            //加班调休获得的时间点范围ID,可查看leave模型里面配置的$workTimePoint
            //申请时间
            $startTime = (string)$p['start_time'];
            $endTime =  (string)$p['start_time'];
            $numberDay = (int)$p['start_id'];
            $timePointChar = Leave::$workTimePointChar;
            $startId = $timePointChar[$p['start_id']]['start_time'];
            $endId = $timePointChar[$p['start_id']]['end_time'];
        }

        //验证是否已经有再提交的请假单,排除已拒绝的请假单
        $isLeaves = Leave::whereRaw("
                    status != 2 and 
                    `start_time` BETWEEN '{$startTime}' and '{$endTime}'
                        or 
                    `end_time` BETWEEN '{$startTime}' and '{$endTime}'
                ")->get();

        //申请配置计算类型配置判断
        $holidayConfig = HolidayConfig::where(['holiday_id' => $holidayId])->first();
        $driver = HolidayConfig::$cypherTypeChar[$holidayConfig->cypher_type];

        //判断是否已经有该调休的范围点内
        foreach ($isLeaves as $lk => $lv) {
            if(empty($lv->user_id)) continue;
            $diffEndTime = strtotime(DataHelper::dateTimeFormat($lv->start_time, 'Y-m-d') .' '. $lv->end_id);
            if($diffEndTime >= strtotime(trim($startTime .' '. $startId))) {
                return $this->backLeaveData(false, ['start_time' => trans('已经有该时间段申请单')]);
            }
        }

        //计算类型驱动调用
        $userHoliday = $this->driver($driver)->check($holidayConfig, $numberDay);

        //验证是否要上次附件
        if($holidayConfig->is_annex === HolidayConfig::STATUS_ENABLE && empty($p['annex'])) {
            return $this->backLeaveData(false, ['annex' => trans('请上传附件')]);
        }
        //员工剩余假期判断和假期使用完是否可在提交请假单
        if(!$userHoliday['success']) {
            return $this->backLeaveData(false, $userHoliday['message']);
        }

        //返回数据
        $userList = json_encode($userList);
        $data = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'holiday_id' => $holidayId,
            'number_day' => $numberDay,
            'user_list' => $userList,
            'copy_user' => json_encode($copyUser),
            'start_id' => $startId,
            'end_id' => $endId,
            'exceed_day' => $userHoliday['exceed_day'],
            'exceed_holiday_id' => $userHoliday ['exceed_holiday_id'],
        ];

        return  $this->backLeaveData(true, [], $data);
    }

    public function getLeaveStep($holidayId, $numberDay): array
    {
        return parent::getLeaveStep($holidayId, $numberDay);
    }

    public function createLeave(array $leave): array
    {
        return parent::createLeave($leave);
    }

    /**
     * 审核操作
     * @param object $leave
     */
    public function leaveReviewPass($leave)
    {
        if(empty($leave->remain_user)) {
            $leave->update(['status' => Leave::WAIT_EFFECTIVE, 'review_user_id' => 0]);
            self::setDailyDetail($leave);
        } else {
            $remainUser = json_decode($leave->remain_user, true);

            $reviewUserId = reset($remainUser);
            array_shift($remainUser);

            if(empty($remainUser)) {
                $remainUser = '';
            } else {
                $remainUser = json_encode($remainUser);
            }

            $leave->update(['status' => Leave::ON_REVIEW, 'review_user_id' => $reviewUserId, 'remain_user' => $remainUser]);
        }
    }

    public function setDailyDetail($leave)
    {
        return parent::setDailyDetail($leave);
    }

}