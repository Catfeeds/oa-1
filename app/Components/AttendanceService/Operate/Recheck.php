<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/3
 * Time: 19:52
 * 补打卡
 */

namespace App\Components\AttendanceService\Operate;

use App\Components\AttendanceService\AttendanceInterface;
use App\Models\Sys\HolidayConfig;
use Illuminate\Foundation\Validation\ValidatesRequests;


class Recheck extends Operate implements AttendanceInterface
{
    use  ValidatesRequests;

    public function checkLeave($request) : array
    {
        $this->validate($request, $this->_validateRuleRe);
        $p = $request->all();

        //假期配置ID
        $holidayId = $p['holiday_id'] ?? '';
        //批量调休人员名单
        $copyUser = $p['copy_user'] ?? '';

        $startTime = $p['start_time'];
        $endTime = $p['end_time'];

        list($hId, $punchType) = explode('$$', $holidayId);

        $holidayId = $hId;
        //补上班打卡
        if((int)$punchType === HolidayConfig::GO_WORK && empty($startTime)) {
            $this->validate($request, array_merge($this->_validateRule, [
                'start_time' => 'required'
            ]));
        }
        //补下班打卡
        if((int)$punchType === HolidayConfig::OFF_WORK && empty($endTime)) {
            $this->validate($request, array_merge($this->_validateRule, [
                'end_time' => 'required'
            ]));
        }

        //补打卡的时间验证
        if((!empty($startTime) && !empty($endTime)) && strtotime($startTime) > strtotime($endTime)) {
            $this->validate($request, array_merge($this->_validateRule, [
                'start_time' => 'required',
                'end_time' => 'required|after:start_time'
            ]),['请选择有效的时间范围']);
        }

        $data = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'holiday_id' => $holidayId,
            'number_day' => 0,//补打卡默认天数未0
            'user_list' => '',
            'copy_user' => json_encode($copyUser),
            'start_id' => 0,
            'end_id'   => 0,
        ];

        return  $this->backLeaveData(true, [], $data);
    }

    public function createLeave(array $leave): array
    {
        return parent::createLeave($leave);
    }
}