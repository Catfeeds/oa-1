<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/15
 * Time: 11:10
 * 批量申请加班
 */

namespace App\Components\AttendanceService\Operate;

use App\Components\AttendanceService\AttendanceInterface;
use App\Components\Helper\DataHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;
use App\User;

class Batchchange extends Operate implements AttendanceInterface
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
        $userList = $p['dept_users'] ?? NULL;
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
            'user_list' => $userList,
            'copy_user' => $copyUser,
            'start_id' => $startId,
            'end_id' => $endId,
            'exceed_day' => $userHoliday['exceed_day'] ?? NULL,
            'exceed_holiday_id' => $userHoliday ['exceed_holiday_id'] ?? NULL,
        ];

        return  $this->backLeaveData(true, [], $data);
    }

    public function getLeaveStep($request, $numberDay): array
    {
        return parent::getLeaveStep($request, $numberDay);
    }

    public function createLeave(array $leave): array
    {

       $userList = NULL;
        //批量申请组织可查询的JSON数据
        if(!empty($leave['user_list'])) {
            $userIds = [];
            foreach ($leave['user_list'] as $k => $v) {
                $userIds['id_' . $v] = $v;
            }
            $userList = json_encode($userIds);
        }
        //基本数据
        $data = [
            'holiday_id' => $leave['holiday_id'],
            'step_id' => $leave['step_id'],
            'start_time' => $leave['start_time'],
            'start_id' => $leave['start_id'],
            'end_time' => $leave['end_time'],
            'end_id' => $leave['end_id'],
            'number_day' => $leave['number_day'],
            'reason' => $leave['reason'],
            'status' => 0, //默认 0 待审批
        ];

        //创建批量申请主订单，不纳入统计
        $parentData = [
            'user_id' => \Auth::user()->user_id,
            'user_list' => $userList,
            'copy_user' => $leave['copy_user'] ?? NULL,
            'is_stat' => Leave::IS_STAT_NO,
            'annex' => $leave['image_path'] ?? '',
            'review_user_id' => $leave['review_user_id'],
            'remain_user' => $leave['remain_user'],
            'step_user' => $leave['step_user'] ?? NULL
        ];
        $parent = Leave::create($data + $parentData);

        //批量生成调休单成员
        foreach ($leave['user_list'] as $uk => $uid) {
            $data['user_id'] = $uid;
            $data['parent_id'] = $parent->leave_id;
            Leave::create($data);
        }

        $parent->update(['parent_id' => $parent->leave_id]);

        return $this->backLeaveData(true, [], ['leave_id' => $parent->leave_id]);
    }

    /**
     * @param array $leave
     * @return array
     */
    public function updateLeave(array $leave): array
    {
        $userList = NULL;
        //批量申请组织可查询的JSON数据
        if(!empty($leave['user_list'])) {
            $userIds = [];
            foreach ($leave['user_list'] as $k => $v) {
                $userIds['id_' . $v] = $v;
            }
            $userList = json_encode($userIds);
        }
        //基本数据
        $data = [
            'holiday_id' => $leave['holiday_id'],
            'step_id' => $leave['step_id'],
            'start_time' => $leave['start_time'],
            'start_id' => $leave['start_id'],
            'end_time' => $leave['end_time'],
            'end_id' => $leave['end_id'],
            'number_day' => $leave['number_day'],
            'reason' => $leave['reason'],
            'status' => 0, //默认 0 待审批
        ];

        //创建批量申请主订单，不纳入统计
        $parentData = [
            'user_list' => $userList,
            'copy_user' => $leave['copy_user'] ?? NULL,
            'is_stat' => Leave::IS_STAT_NO,
            'annex' => $leave['image_path'] ?? '',
            'review_user_id' => $leave['review_user_id'],
            'remain_user' => $leave['remain_user'],
            'step_user' => $leave['step_user'] ?? NULL
        ];
        Leave::where(['user_id' => \Auth::user()->user_id, 'leave_id' => $leave['leave_id']])
            ->update($data + $parentData);

        //批量生成调休单成员
        if(!empty($leave['user_list'])) {
            foreach ($leave['user_list'] as $uk => $uid) {
                $user = Leave::where(['user_id' => $uid, 'parent_id' => $leave['leave_id']])
                    ->first();

                if(empty($user->user_id)) {
                    $data['user_id'] = $uid;
                    $data['parent_id'] = $leave['leave_id'];
                    Leave::create($data);
                } else {

                    $user->update($data);
                }
            }
        }

        return $this->backLeaveData(true, [], ['leave_id' => $leave['leave_id']]);
    }

    /**
     * 审核操作
     * @param object $leave
     */
    public function leaveReviewPass($leave)
    {
        if(empty($leave->remain_user)) {
            $leave->update(['status' => Leave::PASS_REVIEW, 'review_user_id' => 0]);
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
        return true;
    }

    public function getLeaveView($leaveId = '')
    {
        $title = trans('att.批量申请加班');
        $reviewUserId = $startId = '';
        $deptUsersSelected = $copyUserIds = [];

        //申请单重启
        if(!empty($leaveId) && \Entrust::can(['leave.restart'])) {
            $leave = Leave::findOrFail($leaveId);
            if((int)$leave->user_id !== \Auth::user()->user_id || !in_array($leave->status, Leave::$restartList)) {
                flash('请勿非法操作', 'danger');
                return redirect()->route('leave.info');
            }
            $deptUsersSelected = json_decode($leave->user_list, true);
            $copyUserIds = json_decode($leave->copy_user, true);
            $startId = (int)$leave->number_day;

            $title = trans('att.重启批量加班申请');
        }

        $allUsers = User::where(['status' => 1])->get();
        $time = date('Y-m-d', time());

        $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::CHANGE, 'cypher_type' => HolidayConfig::CYPHER_OVERTIME])
            ->orderBy('sort', 'desc')
            ->get(['holiday_id', 'holiday'])
            ->pluck('holiday', 'holiday_id')->toArray();

        //获取所有部门员工
        $deptUsers = User::getUsernameAliasAndDeptList();
        $isBatch = true;

        return view('attendance.leave.change', compact('title', 'time', 'startId', 'holidayList', 'leave', 'reviewUserId' , 'deptUsersSelected', 'deptUsers', 'copyUserIds', 'allUsers', 'isBatch'));
    }

}