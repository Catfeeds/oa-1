<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 11:51
 * 假期申请控制
 */

namespace App\Http\Controllers\Attendance;

use App\Http\Components\Helpers\AttendanceHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Http\Components\ScopeAtt\LeaveScope;
use App\Models\Attendance\Leave;
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\OperateLog;
use App\Models\UserExt;
use App\User;
use EasyWeChat\Kernel\Exceptions\Exception;
use Illuminate\Http\Request;

class LeaveController extends AttController
{
    protected $scopeClass = LeaveScope::class;

    private $_validateRule = [
        'holiday_id' => 'required',
        'start_time' => 'required',
        'end_time' => 'required',
        'reason' => 'required',
    ];

    private $_validateRuleRe = [
        'holiday_id' => 'required',
        'annex' => 'required|file',
        'reason' => 'required',
    ];

    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'attendance.leave.scope';

        $where = AttendanceHelper::setChangeList()['where'];
        $userIds = AttendanceHelper::setChangeList()['user_ids'];
        $data = Leave::where(['user_id' => \Auth::user()->user_id])
            ->whereRaw($scope->getWhere() . $where)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.我的假期详情');
        return view('attendance.leave.index', compact('title', 'data', 'scope', 'holidayList',  'userIds'));
    }

    public function create($applyTypeId)
    {
        $leave = (object)['holiday_id' => '', 'start_id' => '', 'end_id' => ''];
        $reviewUserId = '';
        $allUsers = User::where(['status' => 1])->get();
        switch ($applyTypeId) {
            //请假
            case HolidayConfig::LEAVEID:
                $userExt = UserExt::where(['user_id' => \Auth::user()->user_id])->first();
                $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::LEAVEID])
                    ->whereIn('restrict_sex',[$userExt->sex, 2])
                    ->get(['holiday_id', 'holiday'])
                    ->pluck('holiday', 'holiday_id')->toArray();
                $models = 'edit';
                $title = trans('att.请假申请');
                break;
            //调休
            case HolidayConfig::CHANGE:
                $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::CHANGE])
                    ->get(['holiday_id', 'holiday'])
                    ->pluck('holiday', 'holiday_id')->toArray();
                $models = 'change';
                $title = trans('att.调休申请');

                $deptUsersSelected = [];
                $deptUsers = [];
                //是否上级获取部门所有人员
                if (\Auth::user()->is_leader === 1) {
                    $deptUsers = User::where(['dept_id' => \Auth::user()->dept_id, 'status' => 1])->get()->toArray();
                }
                break;
            //补打卡
            case HolidayConfig::RECHECK:
                $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::RECHECK])
                    ->get(['holiday_id', 'holiday', 'punch_type']);
                $models = 'recheck';
                $title = trans('att.补打卡');
                break;
            default:
                return redirect()->route('leave.info');
        }

        return view('attendance.leave.'.$models, compact('title', 'holidayList', 'leave', 'reviewUserId' , 'deptUsersSelected', 'deptUsers', 'allUsers'));
    }

    /**
     * 假期提交申请
     * @param Request $request 表单数据
     * @param int $applyTypeId 请假类型  1:请假 2:调休 3:补打卡
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $applyTypeId)
    {
        //验证是否是有效的请假配置类型
        if(!in_array((int)$applyTypeId, [HolidayConfig::LEAVEID, HolidayConfig::CHANGE, HolidayConfig::RECHECK])) {
            flash('申请失败,请勿非法操作', 'danger');
            return redirect()->route('leave.info');
        }

        $p = $request->all();

        $driver = HolidayConfig::$driverType[$applyTypeId];

        $retCheck = \AttendanceService::driver($driver)->checkLeave($request);
        if(!$retCheck['success']) return redirect()->back()->withInput()->withErrors($retCheck['message']);


        $step = \AttendanceService::driver($driver)->getLeaveStep($retCheck['data']['number_day']);
        if(!$step['success']) return redirect()->back()->withInput()->withErrors($step['message']);

        //设置上传图片
         $imagePath = AttendanceHelper::setAnnex($request);

        $leave = array_merge($retCheck['data'], $step['data']);
        $leave['image_path'] = $imagePath;
        $leave['reason'] = $p['reason'];

        try {
            $retLeave = \AttendanceService::driver($driver)->createLeave($leave);

            if($retLeave['success']) {
                OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $retLeave['data']['leave_id'], '提交申请');
            }
            //通知审核人员
            //OperateLogHelper::sendWXMsg($review_user_id, '测试下');

        } catch (Exception $ex) {
            flash('申请失败,请重新提交申请!', 'danger');
            return redirect()->route('leave.info');
        }

        flash(trans('app.添加成功', ['value' => trans('att.假期申请')]), 'success');

        return redirect()->route('leave.info');
    }

    public function update(Request $request)
    {
        $leave = Leave::findOrFail(\Auth::user()->user_id);

        $leave->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('att.假期申请')]), 'success');
        return redirect()->route('leave.info');
    }

    public function optInfo($id)
    {
        $leave = Leave::with('holidayConfig')->findOrFail($id);

        $logUserIds = OperateLogHelper::getLogUserIdToInId($leave->leave_id);
        $logUserIds[] = $leave->user_id;
        $logUserIds[] = $leave->review_user_id;
        $userDept = User::getUserAliasToId($leave->user_id);
        //调休包含人员也可以查看
        $userIds = AttendanceHelper::setChangeList($userDept->dept_id)['user_ids'];
        if((in_array(\Auth::user()->user_id, $logUserIds) || in_array(\Auth::user()->user_id, $userIds)) && !empty($leave->leave_id) ) {
            $reviewUserId = $leave->review_user_id;
            $user = User::with(['role', 'dept'])->where(['user_id' => $reviewUserId])->first();
            $logs = OperateLog::where(['type_id' => 1, 'info_id' => $leave->leave_id])->get();
            $dept = Dept::getDeptList();
            $title = trans('att.假期详情');
            $applyTypeId = HolidayConfig::getHolidayApplyList()[$leave->holiday_id];
            $deptUsers = User::where(['dept_id' => $userDept->dept_id, 'status' => 1])->get()->toArray();
            $deptUsers = array_filter($deptUsers);
            return view('attendance.leave.info', compact('title',  'leave', 'dept', 'reviewUserId', 'user', 'logs', 'applyTypeId', 'userIds', 'deptUsers'));
        } else {
            return redirect()->route('leave.info');
        }
    }

    /**
     * 审核管理页面
     */
    public function reviewIndex()
    {
        $scope = $this->scope;
        $scope->block = 'attendance.leave.scope';

        $ids = OperateLogHelper::getLogInfoIdToUid(\Auth::user()->user_id);

        $data = Leave::where(function ($query) use ($ids){
            $query->whereIn('leave_id', $ids)->orWhereRaw('review_user_id = '.\Auth::user()->user_id);
        })
            ->whereRaw($scope->getWhere())
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.申请单管理');
        return view('attendance.leave.review', compact('title', 'data', 'scope'));
    }

    /**
     * 审核人员操作
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviewOptStatus(Request $request, $id)
    {
        $status = $request->get('status');

        if(!in_array($status, [2, 3, 4]) || empty($id)) return response()->json(['status' => -1, 'msg' => '操作失败']);

        $optStatus = self::OptStatus($id, $status);

        if($optStatus) {
            return response()->json(['status' => 1, 'msg' => '操作成功']);
        } else {
            return response()->json(['status' => -1, 'msg' => '操作失败']);
        }
    }

    /**
     * 批量审核操作
     * @param Request $request
     * @param $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reviewBatchOptStatus(Request $request, $status)
    {
        $leaveIds = $request->get('leaveIds');

        if(!in_array($status, [2, 3, 4]) || empty($status) || empty($leaveIds) || !is_array($leaveIds)){
            flash(trans('att.审核失败', ['value' => trans('att.假期申请')]), 'danger');
            return redirect()->route('leave.review.info');
        }

        foreach ($leaveIds as $id) {
            self::OptStatus($id, $status);
        }

        flash(trans('att.审核成功', ['value' => trans('att.假期申请')]), 'success');

        return redirect()->route('leave.review.info');
    }

    /**
     * 设置申请单状态和关联信息
     * @param $leaveId
     * @param $status
     * @return bool
     */
    public function OptStatus($leaveId, $status)
    {
        $leave = Leave::with('holidayConfig')->findOrFail($leaveId);

        if($leave->status === $status) return false;

        if(empty($leave->leave_id) || $leave->review_user_id != \Auth::user()->user_id) {
            return false;
        }

        $msg = '';
        try {
            switch ($status) {
                //拒绝通过状态
                case 2 :
                    $msg = '拒绝通过';
                    $leave->update(['status' => 2, 'review_user_id' => 0]);
                    //假期天数回退
                    AttendanceHelper::leaveNumBack($leave);
                    break;
                //审核通过状态
                case 3 :
                    $msg = '审核通过';
                    //申请单状态操作
                    AttendanceHelper::leaveReviewPass($leave);
                    //提前生成每日详情信息
                    switch ($leave->holidayConfig->apply_type_id) {
                        case HolidayConfig::LEAVEID;
                        case HolidayConfig::CHANGE;
                            AttendanceHelper::setDailyDetail($leave);
                            break;
                        case HolidayConfig::RECHECK;
                            AttendanceHelper::setRecheckDailyDetail($leave);
                            break;
                    }
                    break;
                case 4 :
                    $msg = '取消申请';
                    $leave->update(['status' => 4, 'review_user_id' => 0]);
                    //假期天数回退
                    AttendanceHelper::leaveNumBack($leave);
                    break;
            }

            OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $leave->leave_id, $msg);

        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

}