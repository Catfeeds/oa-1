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
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;
use App\User;

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
        $copyUser = $p['copy_user'] ?? NULL;

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
            if(empty($p['start_id']))  return $this->backLeaveData(false, ['start_time' => trans('未有剩余调休假期')]);
            $startTime = (string)$p['start_time'];
            $endTime =  (string)$p['start_time'];
            $numberDay = (int)$p['start_id'];

            $timePointChar = Leave::$workTimePointChar;
            $startId = $timePointChar[$p['start_id']]['start_time'];
            $endId = $timePointChar[$p['start_id']]['end_time'];
        }
        //验证是否已经有再提交的请假单,排除已拒绝的请假单
        $where =  sprintf(' and user_id =%d and status not in(%s)',\Auth::user()->user_id, implode(',', [Leave::REFUSE_REVIEW, Leave::RETRACT_REVIEW]));
        $isLeaves = Leave::whereRaw("
                `start_time` BETWEEN '{$startTime}' and '{$endTime}'
                {$where}
            ")->orWhereRaw("`end_time` BETWEEN '{$startTime}' and '{$endTime}'
                {$where}
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

        //抄送
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
            'exceed_holiday_id' => $userHoliday['data']['exceed_holiday_id'] ?? NULL,

        ];

        return  $this->backLeaveData(true, [], $data);
    }

    /**
     * @param $request
     * @param $numberDay
     * @return array
     */
    public function getLeaveStep($request, $numberDay): array
    {
        return parent::getLeaveStep($request, $numberDay);
    }

    /**
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
     * 审核操作
     * @param object $leave
     */
    public function leaveReviewPass($leave)
    {
        if(empty($leave->remain_user)) {
            $leave->update(['status' => Leave::WAIT_EFFECTIVE, 'review_user_id' => 0]);
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

            $leave->update(['status' => Leave::ON_REVIEW, 'review_user_id' => $reviewUserId, 'remain_user' => $remainUser]);
        }
    }

    /**
     * @param $leave
     * @return bool
     */
    public function setDailyDetail($leave)
    {
        //夜班加班情况,添加隔天的明细
        if ($leave->holidayConfig->cypher_type == HolidayConfig::CYPHER_NIGHT) {
            $leave->start_time = date('Y-m-d', strtotime('+1 day', strtotime($leave->start_time)));
            $leave->end_time = date('Y-m-d', strtotime('+1 day', strtotime($leave->end_time)));

            $ifNeedUpdate = DailyDetail::where(\DB::raw('DATE_FORMAT(day, "%Y-%m-%d")'), $leave->start_time)
                ->where('user_id', $leave->user_id)->first();
            if (empty($ifNeedUpdate)) {
                DailyDetail::create([
                    'user_id' => $leave->user_id,
                    'day' => $leave->start_time,
                    'leave_id' => self::addLeaveId($leave->leave_id),
                    'punch_start_time' => NULL,
                    'punch_start_time_num' => NULL,
                    'punch_end_time' => NULL,
                    'punch_end_time_num' => NULL,
                ]);
            }else {
                $ifNeedUpdate->leave_id = self::addLeaveId($leave->leave_id, $ifNeedUpdate->leave_id);
                $ifNeedUpdate->save();
            }
            return true;
        }
        return parent::setDailyDetail($leave);
    }

    /**
     * @param string $leaveId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getLeaveView($leaveId = '')
    {
        $title = trans('att.加班/调休申请');
        $reviewUserId = $startId = $endId = '';
        $copyUserIds = [];
        //申请单重启
        if(!empty($leaveId) && \Entrust::can(['leave.restart'])) {
            $leave = Leave::findOrFail($leaveId);
            if((int)$leave->user_id !== \Auth::user()->user_id || !in_array($leave->status, Leave::$restartList)) {
                flash('请勿非法操作', 'danger');
                return redirect()->route('leave.info');
            }
            $copyUserIds = json_decode($leave->copy_user, true);
            $startId = (int)$leave->number_day;
            $endId = $leave->start_id;

            $title = trans('att.重启调休申请');
        }

        $allUsers = User::where(['status' => User::STATUS_ENABLE])->get();
        $time = date('Y-m-d', time());

        $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::CHANGE])
            ->orderBy('sort', 'asc')
            ->get(['holiday_id', 'show_name'])
            ->pluck('show_name', 'holiday_id')->toArray();

        $isBatch = false;

        return view('attendance.leave.change', compact('title', 'startId', 'endId', 'time', 'holidayList', 'leave', 'reviewUserId', 'allUsers', 'isBatch', 'copyUserIds'));
    }

}