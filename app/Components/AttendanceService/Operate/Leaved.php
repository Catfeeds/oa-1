<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/3
 * Time: 19:52]
 * 申请请假配置类型
 */
namespace App\Components\AttendanceService\Operate;

use App\Components\AttendanceService\AttendanceInterface;
use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRules;
use App\Models\UserExt;
use App\User;


class Leaved extends Operate implements AttendanceInterface
{
    /**
     * 检验申请单验证
     * @param $request
     * @return array
     */
    public function checkLeave($request) : array
    {
        $p = $request->all();


        $this->validate($request, array_merge($this->_validateRule,[
            'start_id' => 'required',
            'end_id' => 'required',
        ]));

        //假期配置ID
        $holidayId = $p['holiday_id'];
        //抄送人员名单
        $copyUser = $p['copy_user'] ?? NULL;
        //获取计算驱动类型
        $holidayConfig = HolidayConfig::where(['holiday_id' => $holidayId])->first();
        $driver = HolidayConfig::$cypherTypeChar[$holidayConfig->cypher_type];
        //获取剩余假期情况
        $time = $this->driver($driver)->buildUpLeaveTime($p['start_time'], $p['end_time'], $p['start_id'], $p['end_id']);

        //分配时间
        $startTime = (string)$time['start_time'];
        $endTime = (string)$time['end_time'];
        $startId = $time['start_id'];
        $endId = $time['end_id'];
        $startTimeS = $time['start_timeS'];
        $endTimeS = $time['end_timeS'];
        //时间判断
        if(strtotime($startTimeS) > strtotime($endTimeS)) {
            return $this->backLeaveData(false, ['start_time' => trans('请选择有效的时间范围')]);
        }

        //时间天数分配
        $numberDay = DataHelper::leaveDayDiff($startTimeS, $startId, $endTimeS, $endId);
        if(empty($numberDay)) {
            return $this->backLeaveData(false, ['start_time' => trans('申请失败,时间跨度异常，有疑问请联系人事')]);
        }

        //验证是否已经有再提交的请假单,排除已拒绝的请假单
        $where =  sprintf(' and user_id =%d and status not in(%s)', \Auth::user()->user_id, implode(',', Leave::$applyList));
        $isLeaves = Leave::whereRaw("
                `start_time` BETWEEN '{$startTime}' and '{$endTime}'
                {$where}
            ")->orWhereRaw("`end_time` BETWEEN '{$startTime}' and '{$endTime}'
                {$where}
            ")->get();
        foreach ($isLeaves as $lk => $lv) {
            if(empty($lv->user_id)) continue;
            $diffEndTime = strtotime(AttendanceHelper::spliceLeaveTime($lv->holiday_id, $lv->end_time, $lv->end_id)['time']);
            if($diffEndTime >= strtotime($startTimeS)) {

                return $this->backLeaveData(false, ['start_time' => trans('已经有该时间段申请单')]);
            }
        }
        //获取剩余假期情况
        $userHoliday = $this->driver($driver)->check($holidayConfig, $numberDay);

        //验证是否要上次附件
        if($holidayConfig->is_annex === HolidayConfig::STATUS_ENABLE && empty($p['annex'])) {
            return $this->backLeaveData(false, ['annex' => trans('请上传附件')]);
        }
        //验证是否允许再节日前后申请
        if($holidayConfig->is_before_after === HolidayConfig::STATUS_ENABLE ) {
            $st = strtotime(date('Y-m-d', strtotime('-1day', strtotime($startTimeS))));
            $et = strtotime(date('Y-m-d', strtotime('+1day', strtotime($endTimeS))));
            $st = date('Y', $st) .'-'. (int)date('m', $st) .'-'. (int)date('d', $st);
            $et = date('Y', $et) .'-'. (int)date('m', $et) .'-'. (int)date('d', $et);

            $calendar= Calendar::with('punchRules')
                ->whereRaw('CONCAT(year,"-",month,"-",day)>='. $st .' and CONCAT(year,"-",month,"-",day) <= '. $et)
                ->get();

            foreach ($calendar as $ck => $cv) {
                if($cv->punchRules->punch_type_id === PunchRules::HOLIDAY) {
                    return $this->backLeaveData(false, ['start_time' => trans($cv->year.'-'.$cv->month.'-'.$cv->day.'有节假日，不允许连休!')]);
                }
            }
        }

        //员工剩余假期判断和假期使用完是否可在提交请假单
        if(!$userHoliday['success']) {
            return $this->backLeaveData(false, $userHoliday['message']);
        }

        //批量抄送组织可查询的JSON数据
        if(!empty($copyUser)) {
            $copyIds = [];
            foreach ($copyUser as $d => $v) {
                $copyIds['id_' . $v] = $v;
            }
            $copyUser = json_encode($copyIds);
        }

        //返回数据
        $data = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'holiday_id' => $holidayId,
            'number_day' => $numberDay,
            'copy_user' => $copyUser,
            'start_id' => $startId,
            'end_id' => $endId,
            'exceed_day' => $userHoliday['data']['exceed_day'] ?? NULL,
            'exceed_holiday_id' => $userHoliday['data']['exceed_holiday_id'] ?? NULL
        ];

        return  $this->backLeaveData(true, [], $data);
    }

    public function getLeaveStep($request, $numberDay): array
    {
        return parent::getLeaveStep($request, $numberDay);
    }

    /**
     * 创建申请单
     * @param array $leave
     * @return array
     */
    public function createLeave(array $leave): array
    {
        return parent::createLeave($leave);
    }

    /**
     * @param array $leave
     * @return array
     */
    public function updateLeave(array $leave): array
    {
        return parent::updateLeave($leave);
    }

    /**
     * @param object $leave
     */
    public function leaveReviewPass($leave)
    {
        if(empty($leave->remain_user)) {
            $leave->update(['status' => 3, 'review_user_id' => 0]);
            //预生成每日考勤信息
            $this->setDailyDetail($leave);
            //微信通知申请人
            $this->passWXSendContent($leave);

        } else {
            $remainUser = json_decode($leave->remain_user, true);

            $reviewUserId = reset($remainUser);
            array_shift($remainUser);

            if(empty($remainUser)) {
                $remainUser = '';
            } else {
                $remainUser = json_encode($remainUser);
            }

            $leave->update(['status' => 1, 'review_user_id' => $reviewUserId, 'remain_user' => $remainUser]);
        }
    }

    /**
     * @param $leave
     */
    public function setDailyDetail($leave)
    {
        return parent::setDailyDetail($leave);
    }

    /**
     * @param string $leaveId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getLeaveView($leaveId = '')
    {
        $title = trans('att.请假申请');
        $reviewUserId  = '';

        //申请单重启
        if(!empty($leaveId) && \Entrust::can(['leave.restart'])) {
            $leave = Leave::findOrFail($leaveId);
            if((int)$leave->user_id !== \Auth::user()->user_id || !in_array($leave->status, Leave::$restartList)) {
                flash('请勿非法操作', 'danger');
                return redirect()->route('leave.info');
            }
            $copyUserIds = json_decode($leave->copy_user, true);

            $title = trans('att.重启请假申请');
        }

        $allUsers = User::getUsernameAliasAndDeptList();
        $time = date('Y-m-d', time());

        $holidayList = HolidayConfig::getUserShowHolidayList();

        return view('attendance.leave.edit', compact('title', 'time', 'holidayList', 'leave', 'reviewUserId', 'copyUserIds', 'deptUsers', 'allUsers'));

    }
}
