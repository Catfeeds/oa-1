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
use App\Http\Components\Helpers\PunchHelper;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;
use App\User;
use Illuminate\Foundation\Validation\ValidatesRequests;


class Recheck extends Operate implements AttendanceInterface
{
    use  ValidatesRequests;

    public function checkLeave($request): array
    {
        //补打卡不需要end_time
        unset($this->_validateRule['end_time']);
        $this->validate($request, $this->_validateRule);
        $p = $request->all();

        //假期配置ID
        $holidayId = $p['holiday_id'] ?? '';
        //批量调休人员名单
        $copyUser = $p['copy_user'] ?? '';

        //补下班打卡
        if (empty($startTime)) {
            $this->validate($request, array_merge($this->_validateRule, [
                'start_time' => 'required',
            ]), ['请选择有效的时间范围']);
        }

        $startTime = $endTime = NULL;

        $config = HolidayConfig::where(['holiday_id' => $holidayId, 'apply_type_id' => HolidayConfig::RECHECK])->first();

        if(empty($config->holiday_id)) {
            return $this->backLeaveData(false, ['holiday_id' => trans('无效申请ID')]);
        }

        if($config->punch_type === HolidayConfig::GO_WORK) {
            $startTime = $p['start_time'];
        }

        if($config->punch_type === HolidayConfig::OFF_WORK) {
            $endTime = $p['start_time'];
        }

        //批量抄送组织可查询的JSON数据
        if (!empty($copyUser)) {
            $copyIds = [];
            foreach ($copyUser as $d => $v) {
                $roleIds['id_' . $v] = $v;
            }
            $copyUser = json_encode($copyIds);
        }
        $data = [
            'start_time'        => $startTime,
            'end_time'          => $endTime,
            'holiday_id'        => (int)$holidayId,
            'number_day'        => 0,//补打卡默认天数未0
            'copy_user'         => $copyUser,
            'start_id'          => NULL,
            'end_id'            => NULL,
            'exceed_day'        => NULL,
            'exceed_holiday_id' => NULL,
        ];

        return $this->backLeaveData(true, [], $data);
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
     * @param $leave
     */
    public function setDailyDetail($leave)
    {
        $startDay = strtotime($leave->start_time);
        $endDay = strtotime($leave->end_time);

        //上下班都要补打卡的情况
        if (date('Y-m-d', $startDay) == date('Y-m-d', $endDay)) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $startDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();

            $daily->user_id = $leave->user_id;
            $daily->day = date('Y-m-d', $startDay);
            $daily->leave_id = self::addLeaveId($leave->leave_id, $daily->leave_id);
            $daily->punch_start_time = date('H:i', $startDay);
            $daily->punch_start_time_num = $startDay;
            $daily->punch_end_time = date('H:i', $endDay);
            $daily->punch_end_time_num = $endDay;

            $this->updateSwitchInLeave($daily);
            $daily->save();
            return;
        }
        //上班补打卡
        if ($leave->holidayConfig->punch_type === 1) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $startDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();

            $daily->user_id = $leave->user_id;
            $daily->day = date('Y-m-d', $startDay);
            $daily->leave_id = self::addLeaveId($leave->leave_id, $daily->leave_id);
            $daily->punch_start_time = date('H:i', $startDay);
            $daily->punch_start_time_num = $startDay;

            $this->updateSwitchInLeave($daily);
            $daily->save();
        }

        //下班补打卡
        if ($leave->holidayConfig->punch_type === 2) {
            $daily = DailyDetail::whereIn('day', [date('Y-m-d', $endDay)])
                ->where(['user_id' => $leave->user_id])
                ->first();

            $daily->user_id = $leave->user_id;
            $daily->day = date('Y-m-d', $endDay);
            $daily->leave_id = self::addLeaveId($leave->leave_id, $daily->leave_id);
            $daily->punch_end_time = date('H:i', $endDay);
            $daily->punch_end_time_num = $endDay;

            $this->updateSwitchInLeave($daily);
            $daily->save();
        }

    }

    /**
     * @param $dailyDetail
     */
    public function updateSwitchInLeave($dailyDetail)
    {
        $punchHelper = app(PunchHelper::class);
        $formulaCalPunRuleConf = PunchHelper::getCalendarPunchRules($dailyDetail->day, $dailyDetail->day)['formula'];
        $switch = Leave::where(['user_id' => $dailyDetail->user_id, 'start_time' => $dailyDetail->day])->whereIn('status', [
            Leave::SWITCH_REVIEW_ON, Leave::SWITCH_REVIEW_OFF,
        ])->first();
        if (empty($switch)) return;
        $deduct = $punchHelper->countDeduct($dailyDetail->punch_start_time, $dailyDetail->punch_end_time,
            $formulaCalPunRuleConf[$dailyDetail->day], $dailyDetail);

        if ($deduct['deduct_day'] <= 0) {
            $switch->status = Leave::SWITCH_REVIEW_OFF;
            $switch->save();
        } else {
            $switch->number_day = $deduct['deduct_day'];
            $switch->save();
        }
    }

    /**
     * @param string $leaveId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getLeaveView($leaveId = '')
    {
        $title = trans('att.补打卡');
        $reviewUserId = '';

        //申请单重启
        if (!empty($leaveId) && \Entrust::can(['leave.restart'])) {
            $leave = Leave::with('holidayConfig')->findOrFail($leaveId);
            if ((int)$leave->user_id !== \Auth::user()->user_id || !in_array($leave->status, Leave::$restartList)) {
                flash('请勿非法操作', 'danger');
                return redirect()->route('leave.info');
            }
            $title = trans('att.重启补打卡');
        }

        $allUsers = User::where(['status' => User::STATUS_ENABLE])->get();
        $time = date('Y-m-d H:i', time());

        $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::RECHECK])
            ->orderBy('sort', 'asc')
            ->get(['holiday_id', 'show_name'])
            ->pluck('show_name', 'holiday_id')
            ->toArray();
        $daily = DailyDetail::where(['user_id' => \Auth::user()->user_id, 'day' => request()->day])->first();

        return view('attendance.leave.recheck', compact('title', 'time', 'holidayList', 'leave', 'reviewUserId', 'daily', 'allUsers'));

    }
}