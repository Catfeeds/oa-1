<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/4
 * Time: 10:20
 * 考勤操作通用方法类
 */

namespace App\Components\AttendanceService\Operate;
use App\Models\Sys\ApprovalStep;
use App\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Models\Attendance\Leave;
use App\Models\Sys\HolidayConfig;
use App\Models\UserHoliday;

class Operate
{
    use  ValidatesRequests;

    public $_validateRule = [
        'holiday_id' => 'required',
        'start_time' => 'required',
        'end_time' => 'required',
        'reason' => 'required',
    ];

    public $_validateRuleRe = [
        'holiday_id' => 'required',
        'annex' => 'required|file',
        'reason' => 'required',
    ];

    /**
     * 获取员工审核步骤
     * @param $numberDay //申请单天数
     * @return array
     */
    public function getLeaveStep($numberDay) : array
    {
        $steps = ApprovalStep::where(['dept_id' => \Auth::user()->dept_id])->get()->toArray();

        $step = [];
        foreach ($steps as $sk => $sv) {
            if(empty($sv['time_range_id'])) continue;
            $rangeTime = ApprovalStep::$timeRange[$sv['time_range_id']];
            if($rangeTime['min'] <= $numberDay && $rangeTime['max'] >= $numberDay) $step = $sv;
        }

        if(empty($step)) return self::backLeaveData(false, ['holiday_id' => trans('申请失败, 未设置部门审核人员，有疑问请联系人事')]);

        $leaveStep = json_decode($step['step'], true);
        $leader = [];
        foreach ($leaveStep as $lk => $lv) {
            $userLeader = User::where(['role_id' => $lv, 'is_leader' => 1])->first();
            if(empty($userLeader->user_id)) return self::backLeaveData(false, ['holiday_id' => trans('申请失败, 未设置部门主管权限，有疑问请联系人事')]);
            $leader[$userLeader->user_id] = $userLeader->user_id;
        }

        $reviewUserId = reset($leader);
        unset($leader[$reviewUserId]);

        if(empty($leader)) {
            $remainUser = '';
        } else {
            $remainUser = json_encode($leader);
        }

        $data = [
            'step_id' => $step['step_id'],
            'remain_user'  => $remainUser,
            'review_user_id' => $reviewUserId,
        ];
        return self::backLeaveData(true, [], $data);
    }


    /**
     * 创建申请单
     * @param array $leave
     */
    public function createLeave(array $leave) : array
    {
        $data = [
            'user_id' => \Auth::user()->user_id,
            'holiday_id' => $leave['holiday_id'],
            'step_id' => $leave['step_id'],
            'start_time' => $leave['start_time'],
            'start_id' => $leave['start_id'],
            'end_time' => $leave['end_time'],
            'end_id' => $leave['end_id'],
            'reason' => $leave['reason'],
            'user_list' => $leave['user_list'],
            'status' => 0, //默认 0 待审批
            'annex' => $leave['image_path'] ?? '',
            'review_user_id' => $leave['review_user_id'],
            'remain_user' => $leave['remain_user'],
            'copy_user' => $leave['copy_user'],
        ];

         $ret = Leave::create($data);

         if(!empty($leave['user_config'])) $leave['user_config']->update(['num' => $leave['user_config']->num - $leave['number_day']]);

         return $this->backLeaveData(true, [], ['leave_id' => $ret->leave_id]);
    }


    public function checkLeave($request) : array
    {
        $this->validate($request, $this->_validateRule);
        $p = $request->all();

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
        //查询假期配置和员工剩余假期
        $holidayConfig = HolidayConfig::where(['holiday_id' => $holidayId])->first();
        $userConfig = UserHoliday::where(['user_id' => \Auth::user()->user_id, 'holiday_id' => $holidayConfig->holiday_id])->first();
        //员工剩余假期判断和假期使用完是否可在提交请假单
        if(!empty($userConfig->num) && $userConfig->num < $numberDay &&  $holidayConfig->condition_id === 1) {
            return $this->backLeaveData(false, ['holiday_id' => '申请失败!['.$holidayConfig->holiday.']假期剩余天数不足, 有疑问请联系人事']);
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
        $userList = json_encode($userList);
        $data = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'holiday_id' => $holidayId,
            'number_day' => $numberDay,
            'user_list' => $userList,
            'user_config' => $userConfig,
            'copy_user' => json_encode($copyUser),
            'start_id' => $p['start_id'],
            'end_id'   => $p['end_id'],
        ];

        return  $this->backLeaveData(true, [], $data);
    }

    /**
     * 申请单验证和数据返回
     * @param $success
     * @param array $message
     * @param array $data
     * @return array
     */
    public function backLeaveData($success, $message = [], $data = [])
    {
       return ['success' => $success, 'message' => $message , 'data' => $data];
    }


}