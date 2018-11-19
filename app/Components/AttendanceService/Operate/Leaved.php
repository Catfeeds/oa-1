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
use App\Models\Attendance\DailyDetail;
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
        //申请时间
        $startTime = (string)$p['start_time'];
        $endTime = (string)$p['end_time'];
        //拼接有效时间戳
        $startTimeS = trim($startTime .' '. $p['start_id']);
        $endTimeS = trim($endTime .' '. $p['end_id']);
        //时间判断
        if(strtotime($startTimeS) > strtotime($endTimeS)) {
            return $this->backLeaveData(false, ['end_time' => trans('请选择有效的时间范围')]);
        }

        //时间天数分配
        $numberDay = DataHelper::leaveDayDiff($startTimeS, $p['start_id'], $endTimeS, $p['end_id']);
        if(empty($numberDay)) {
            return $this->backLeaveData(false, ['end_time' => trans('申请失败,时间跨度异常，有疑问请联系人事')]);
        }
        //验证是否已经有再提交的请假单,排除已拒绝的请假单
        $where =  sprintf(' and user_id =%d and status not in(%s)',\Auth::user()->user_id, implode(',', [Leave::REFUSE_REVIEW, Leave::RETRACT_REVIEW]));
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
                return $this->backLeaveData(false, ['end_time' => trans('已经有该时间段申请单')]);
            }
        }
        //申请配置计算类型配置判断
        $holidayConfig = HolidayConfig::where(['holiday_id' => $holidayId])->first();
        $driver = HolidayConfig::$cypherTypeChar[$holidayConfig->cypher_type];
        $userHoliday = $this->driver($driver)->check($holidayConfig, $numberDay);

        //验证是否要上次附件
        if($holidayConfig->is_annex === HolidayConfig::STATUS_ENABLE && empty($p['annex'])) {
            return $this->backLeaveData(false, ['annex' => trans('请上传附件')]);
        }
        //验证是否允许再节日前后申请
        if($holidayConfig->is_before_after === HolidayConfig::STATUS_ENABLE ) {
            $st =  strtotime(date('Y-m-d', strtotime('-1day', strtotime($startTimeS))));
            $et =  strtotime(date('Y-m-d', strtotime('+1day', strtotime($endTimeS))));
            $st = date('Y', $st) .'-'. (int)date('m', $st) .'-'. (int)date('d', $st);
            $et = date('Y', $et) .'-'. (int)date('m', $et) .'-'. (int)date('d', $et);

            $calendar= Calendar::with('punchRules')
                ->whereRaw('CONCAT(year,"-",month,"-",day)>='. $st .' and CONCAT(year,"-",month,"-",day) <= '. $et)
                ->get();

            foreach ($calendar as $ck => $cv) {
                if($cv->punchRules->punch_type_id === PunchRules::HOLIDAY) {
                    return $this->backLeaveData(false, ['end_time' => trans($cv->year.'-'.$cv->month.'-'.$cv->day.'有节假日，不允许连休!')]);
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
            'start_id' => $p['start_id'],
            'end_id' => $p['end_id'],
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
        $reviewUserId = $startId = $endId = '';

        //申请单重启
        if(!empty($leaveId) && \Entrust::can(['leave.restart'])) {
            $leave = Leave::findOrFail($leaveId);
            if((int)$leave->user_id !== \Auth::user()->user_id || !in_array($leave->status, Leave::$restartList)) {
                flash('请勿非法操作', 'danger');
                return redirect()->route('leave.info');
            }
            $startId = $leave->start_id;
            $endId = $leave->end_id;
            $title = trans('att.重启请假申请');
        }

        $allUsers = User::where(['status' => 1])->get();
        $time = date('Y-m-d', time());

        $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::LEAVEID])
            ->whereIn('restrict_sex', [\Auth::user()->userExt->sex, UserExt::SEX_NO_RESTRICT])
            ->where(['is_show' => HolidayConfig::STATUS_ENABLE])
            ->orderBy('sort', 'desc')
            ->get(['holiday_id', 'holiday'])
            ->pluck('holiday', 'holiday_id')
            ->toArray();


        return view('attendance.leave.edit', compact('title', 'time', 'startId', 'endId', 'holidayList', 'leave', 'reviewUserId' ,  'deptUsers', 'allUsers'));

    }
}
