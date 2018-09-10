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
            'number_day' => $leave['number_day'],
            'reason' => $leave['reason'],
            'user_list' => $leave['user_list'],
            'status' => 0, //默认 0 待审批
            'annex' => $leave['image_path'] ?? '',
            'review_user_id' => $leave['review_user_id'],
            'remain_user' => $leave['remain_user'],
            'copy_user' => $leave['copy_user'],
        ];

         $ret = Leave::create($data);

         return $this->backLeaveData(true, [], ['leave_id' => $ret->leave_id]);
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