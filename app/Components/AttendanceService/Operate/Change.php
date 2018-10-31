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
        $this->validate($request, $this->_validateRule);
        //假期配置ID
        $holidayId = $p['holiday_id'];

        //批量调休人员名单
        $userList = $p['dept_users'] ?? '';
        $copyUser = $p['copy_user'] ?? '';
        //申请时间
        $startTime = (string)$p['start_time'];
        $endTime = (string)$p['end_time'];
        //拼接有效时间戳
        $startTimeS = trim($startTime .' '. Leave::$startId[$p['start_id'] ?? 0]);
        $endTimeS = trim($endTime .' '. Leave::$endId[$p['end_id'] ?? 0]);
        //时间判断
        if(strtotime($startTimeS) > strtotime($endTimeS)) {
            return $this->backLeaveData(false, ['end_time' => trans('请选择有效的时间范围')]);
        }
        //时间天数分配
        $numberDay = DataHelper::diffTime($startTimeS, $endTimeS);
        if(empty($numberDay)) {
            return $this->backLeaveData(false, ['end_time' => trans('申请失败,时间跨度最长为一周，有疑问请联系人事')]);
        }

        //验证是否已经有再提交的请假单,排除已拒绝的请假单
        $isLeaves = Leave::whereRaw("
                    status != 2 and 
                    `start_time` BETWEEN '{$startTime}' and '{$endTime}'
                        or 
                    `end_time` BETWEEN '{$startTime}' and '{$endTime}'
                ")->get();

        foreach ($isLeaves as $lk => $lv) {
            if(empty($lv->user_id)) continue;
            $diffEndTime = strtotime(AttendanceHelper::getLeaveEndTime($lv->end_time, $lv->end_id));
            if($diffEndTime >= strtotime($startTimeS)) {
                return $this->backLeaveData(false, ['end_time' => trans('已经有该时间段请假单')]);
            }
        }
        //渠道配置计算类型配置判断
        $holidayConfig = HolidayConfig::where(['holiday_id' => $holidayId])->first();
        $driver = HolidayConfig::$cypherTypeChar[$holidayConfig->cypher_type];
        $userHoliday = $this->driver($driver)->check($holidayConfig, $numberDay);

        //验证是否要上次附件
        if($holidayConfig->is_annex === HolidayConfig::STATUS_ENABLE && empty($p['annex'])) {
            return $this->backLeaveData(false, ['annex' => trans('请上传附件')]);
        }
        //员工剩余假期判断和假期使用完是否可在提交请假单
        if(!$userHoliday['success']) {
            return $this->backLeaveData(false, $userHoliday['msg']);
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
            'start_id' => $p['start_id'],
            'end_id'   => $p['end_id'],
        ];

        return  $this->backLeaveData(true, [], $data);
    }

    public function createLeave(array $leave): array
    {
        return parent::createLeave($leave);
    }

    /**
     * @param object $leave
     */
    public function leaveReviewPass($leave)
    {
        parent::leaveReviewPass($leave);
    }

}