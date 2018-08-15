<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 11:51
 */

namespace App\Http\Controllers\Attendance;

use App\Components\Helper\DataHelper;
use App\Components\Helper\FileHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance\Leave;
use App\Models\RoleLeaveStep;
use App\Models\Sys\ApprovalStep;
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\OperateLog;
use App\User;
use EasyWeChat\Kernel\Exceptions\Exception;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    private $_validateRule = [
        'holiday_id' => 'required',
        'start_time' => 'required',
        'end_time' => 'required',
        'reason' => 'required',
    ];

    public function index()
    {
        $data = Leave::where(['user_id' => \Auth::user()->user_id])->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.我的假期详情');
        return view('attendance.leave.index', compact('title', 'data', 'scope', 'holidayList'));

    }

    public function create()
    {
        $leave = (object)['holiday_id' => '', 'start_id' => '', 'end_id' => ''];
        $reviewUserId = '';
        $title = trans('att.请假申请');
        return view('attendance.leave.edit', compact('title', 'holidayList', 'leave', 'reviewUserId'));
    }

    public function edit($id)
    {
        $leave = Leave::findOrFail($id);
        $title = trans('att.请假申请');
        return view('attendance.leave.edit', compact('title', 'holidayList', 'leave'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        $p = $request->all();

        $file = 'annex';
        $image_path = $image_name = '';
        if ($request->hasFile($file) && $request->file($file)->isValid()) {
            $time = date('Ymd', time());
            $uploadPath = 'assert/images/'. $time;
            $file_name = $file .'_'. time() . rand(100000, 999999);
            $image_name = FileHelper::uploadImage($request->file($file), $file_name, $uploadPath);
            $image_path = $uploadPath .'/'. $image_name;
        }

        $startTime = $p['start_time'] .' '. Leave::$startId[$p['start_id']];
        $endTime = $p['end_time'] .' '. Leave::$endId[$p['end_id']];
        $day = DataHelper::diffTime($startTime, $endTime);
        $user = User::findOrFail(\Auth::user()->user_id);
        $stepId = RoleLeaveStep::where(['role_id' => $user->role_id])->get(['step_id'])->pluck('step_id');
        $betDay = 1;
        switch ($day) {
            case $day > 0 && $day < 3;
                $day = 2;
                break;
            default:
                $betDay = 3;
        }
        $step = ApprovalStep::whereIn('step_id', $stepId)->whereBetween('day', [$betDay, $day])->first();

        if(empty($step->step)) {
            flash('申请失败,未匹配到请假模版，请联系人事', 'danger');
            return redirect()->route('leave.info');
        }
        $leaveStep = json_decode($step->step, true);

        $leader = [];

        foreach ($leaveStep as $lk => $lv) {

            $userLeader = User::where(['role_id' => $lv, 'is_leader' => 1])->first();

            if (empty($userLeader)) continue;
            $leader[$userLeader->user_id] = $userLeader->user_id;
        }

        $review_user_id = reset($leader);
        unset($leader[$review_user_id]);

        if(empty($leader)) {
            $remain_user = '';
        } else {
            $remain_user = json_encode($leader);
        }

        $data = [
            'user_id' => \Auth::user()->user_id,
            'holiday_id' => $p['holiday_id'],
            'step_id' => $step->step_id,
            'start_time' => $p['start_time'],
            'start_id' => $p['start_id'],
            'end_time' => $p['end_time'],
            'end_id' => $p['end_id'],
            'reason' => $p['reason'],
            'user_list' => $p['user_list'] ?? '',
            'status' => 0, //默认 0 待审批
            'annex' => $image_path ?? '',
            'review_user_id' => $review_user_id,
            'remain_user' => $remain_user,
        ];

        try {
            $leave = Leave::create($data);
            if(!empty($leave->leave_id)) {
                OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $leave->leave_id, '提交申请');
            }
        } catch (Exception $ex) {
            flash('申请失败,请重新提交申请!', 'danger');
            return redirect()->route('leave.info');
        }

        flash(trans('app.添加成功', ['value' => trans('app.假期申请')]), 'success');

        return redirect()->route('leave.info');
    }

    public function update(Request $request)
    {
        $leave = Leave::findOrFail(\Auth::user()->user_id);

        $leave->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.假期申请')]), 'success');
        return redirect()->route('leave.info');
    }

    public function optInfo($id)
    {
        $leave = Leave::findOrFail($id);
        $logUserIds = OperateLogHelper::getLogUserIdToInId($leave->leave_id);
        $logUserIds[] = $leave->user_id;
        $logUserIds[] = $leave->review_user_id;
        if(in_array(\Auth::user()->user_id, $logUserIds) && !empty($leave->leave_id) ) {
            $reviewUserId = json_decode($leave->review_user_id, true);
            $user = User::with(['role', 'dept'])->where(['user_id' => $reviewUserId])->first();
            $logs = OperateLog::where(['type_id' => 1, 'info_id' => $leave->leave_id])->get();
            $dept = Dept::getDeptList();
            $title = trans('att.假期详情');
            return view('attendance.leave.info', compact('title',  'leave', 'dept', 'reviewUserId', 'user', 'logs'));
        } else {
            return redirect()->route('leave.info');
        }
    }

    /**
     * 审核管理页面
     */
    public function reviewIndex()
    {
        $ids = OperateLogHelper::getLogInfoIdToUid(\Auth::user()->user_id);

        $data = Leave::whereIn('leave_id', $ids)->orWhereRaw('review_user_id = '.\Auth::user()->user_id )->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.申请单管理');
        return view('attendance.leave.review', compact('title', 'data', 'scope'));
    }

    public function reviewOptStatus(Request $request, $id)
    {
        $status = $request->get('status');

        if(!in_array($status, [1, 2]) || empty($id)) return response()->json(['status' => -1, 'msg' => '操作失败']);

        $optStatus = self::OptStatus($id, $status);

        if($optStatus) {
            return response()->json(['status' => 1, 'msg' => '操作成功']);
        } else {
            return response()->json(['status' => -1, 'msg' => '操作失败']);
        }
    }

    public function reviewBatchOptStatus(Request $request, $status)
    {
        $leaveIds = $request->get('leaveIds');

        if(!in_array($status, [1, 2]) || empty($status) || empty($leaveIds) || !is_array($leaveIds)) return response()->json(['status' => -1, 'msg' => '操作失败']);

        foreach ($leaveIds as $id) {
            self::OptStatus($id, $status);
        }

        flash(trans('app.审核成功', ['value' => trans('app.假期申请')]), 'success');

        return redirect()->route('leave.review.info');
    }

    public function OptStatus($leaveId, $status)
    {
        $leave = Leave::findOrFail($leaveId);

        if(empty($leave->leave_id) || $leave->review_user_id != \Auth::user()->user_id) {
            return false;
        }
        $msg = '';
        try {
            switch ($status) {
                case 1 :
                    $msg = '审核通过';
                    if(empty($leave->remain_user)) {
                        $leave->update(['status' => 3, 'review_user_id' => 0]);
                    } else {
                        $remain_user = json_decode($leave->remain_user, true);

                        $review_user_id = reset($remain_user);
                        unset($remain_user[$review_user_id]);
                        if(empty($remain_user)) {
                            $remain_user = '';
                        } else {
                            $remain_user = json_encode($remain_user);
                        }

                        $leave->update(['status' => 1, 'review_user_id' => $review_user_id, 'remain_user' => $remain_user]);
                    }
                    break;
                case 2 :
                    $msg = '拒绝通过';
                    $leave->update(['status' => 2, 'review_user_id' => '']);
                    break;
            }

            OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $leave->leave_id, $msg);

        } catch (Exception $ex) {
            return false;
        }

        return true;
    }
}