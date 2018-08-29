<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 11:51
 * 假期申请控制
 */

namespace App\Http\Controllers\Attendance;

use App\Components\Helper\DataHelper;
use App\Components\Helper\FileHelper;
use App\Http\Components\Helpers\AttendanceHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Http\Components\ScopeAtt\LeaveScope;
use App\Models\Attendance\Leave;
use App\Models\RoleLeaveStep;
use App\Models\Sys\ApprovalStep;
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\OperateLog;
use App\Models\UserExt;
use App\Models\UserHoliday;
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

        $data = Leave::where(['user_id' => \Auth::user()->user_id])
            ->whereRaw($scope->getWhere())
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.我的假期详情');
        return view('attendance.leave.index', compact('title', 'data', 'scope', 'holidayList'));
    }

    public function create($applyTypeId)
    {
        $leave = (object)['holiday_id' => '', 'start_id' => '', 'end_id' => ''];
        $reviewUserId = '';
        switch ($applyTypeId) {
            //请假
            case 1:
                $userExt = UserExt::where(['user_id' => \Auth::user()->user_id])->first();
                $holidayList = HolidayConfig::where(['apply_type_id' => 1])
                    ->whereIn('restrict_sex',[$userExt->sex, 2])
                    ->get(['holiday_id', 'holiday'])
                    ->pluck('holiday', 'holiday_id')->toArray();
                $models = 'edit';
                $title = trans('att.请假申请');
                break;
            //调休
            case 2:
                $holidayList = HolidayConfig::where(['apply_type_id' => 2])
                    ->get(['holiday_id', 'holiday'])
                    ->pluck('holiday', 'holiday_id')->toArray();
                $models = 'change';
                $title = trans('att.调休');
                break;
            //补打卡
            case 3:
                $holidayList = HolidayConfig::where(['apply_type_id' => 3])
                    ->get(['holiday_id', 'holiday', 'punch_type']);
                $models = 'recheck';
                $title = trans('att.补打卡');
                break;
            default:
                return redirect()->route('leave.info');
        }

        return view('attendance.leave.'.$models, compact('title', 'holidayList', 'leave', 'reviewUserId'));
    }

    //设置上传附件
    public function setAnnex($request) {
        $file = 'annex';
        $imagePath = $imageName = '';
        if ($request->hasFile($file) && $request->file($file)->isValid()) {
            $time = date('Ymd', time());
            $uploadPath = 'assert/images/'. $time;
            $fileName = $file .'_'. time() . rand(100000, 999999);
            $imageName = FileHelper::uploadImage($request->file($file), $fileName, $uploadPath);
            $imagePath = $uploadPath .'/'. $imageName;
        }
        return $imagePath;
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
        if(!in_array((int)$applyTypeId, [1, 2, 3])) {
            flash('申请失败,请勿非法操作', 'danger');
            return redirect()->route('leave.info');
        }
        $p = $request->all();
        $holidayId = $p['holiday_id'];

        //补打卡特殊表单验证
        if((int)$applyTypeId === 3) {
            $this->validate($request, $this->_validateRuleRe);
            list($hId, $punchType) = explode('$$', $p['holiday_id']);
            $holidayId = $hId;
            if($punchType === 1 && empty($p['start_time'])) {
                return redirect()->back()->withInput()->withErrors(['start_time' => trans('请选择补打卡时间')]);
            }

            if($punchType === 2 && empty($p['end_time'])) {
                return redirect()->back()->withInput()->withErrors(['end_time' => trans('请选择补打卡时间')]);
            }
            //其余使用基本
        } else {
            $this->validate($request, $this->_validateRule);
        }

        $userConfig = [];
        //请假和调休，时间验证
        if(in_array($applyTypeId, [1, 2])) {
            // 拼接 有效时间戳
            $startTime = (string)$p['start_time'];
            $endTime = (string)$p['end_time'];

            $startTimeS = trim($startTime .' '. Leave::$startId[$p['start_id'] ?? 0]);
            $endTimeS = trim($endTime .' '. Leave::$endId[$p['end_id'] ?? 0]);
            //时间判断
            if(strtotime($startTimeS) > strtotime($endTimeS)) {
                return redirect()->back()->withInput()->withErrors(['end_time' => trans('请选择有效的时间范围')]);
            }
            //时间天数分配
            $day = DataHelper::diffTime($startTimeS, $endTimeS);
            if(empty($day)) {
                flash('申请失败,时间跨度最长为一周，有疑问请联系人事', 'danger');
                return redirect()->route('leave.info');
            }
            //查询假期配置和员工剩余假期
            $holidayConfig = HolidayConfig::where(['holiday_id' => $holidayId])->first();
            $userConfig = UserHoliday::where(['user_id' => \Auth::user()->user_id, 'holiday_id' => $holidayConfig->holiday_id])->first();
            //员工剩余假期判断和假期使用完是否可在提交请假单
            if(!empty($userConfig->num) && $userConfig->num < $day &&  $holidayConfig->condition_id === 1) {
                flash('申请失败!['.$holidayConfig->holiday.']假期剩余天数不足, 有疑问请联系人事', 'danger');
                return redirect()->route('leave.info');
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
                        return redirect()->back()->withInput()->withErrors(['start_time' => trans('已经有该时间段请假单')]);
                    }
            }

        } else {
            $startTime = $p['start_time'];
            $endTime = $p['end_time'];
            //补打卡的时间验证
            if((!empty($startTime) && !empty($endTime)) && strtotime($startTime) > strtotime($endTime)) {
                return redirect()->back()->withInput()->withErrors(['end_time' => trans('请选择有效的时间范围')]);
            }
            //补打卡不需要天数,默认设置为0
            $day = 0;
        }

        //设置上传图片
        $imagePath = self::setAnnex($request);

        //查找职务绑定审核步骤
        $user = User::findOrFail(\Auth::user()->user_id);
        $stepId = RoleLeaveStep::where(['role_id' => $user->role_id])->get(['step_id'])->pluck('step_id');

        //职务绑定的审核步骤ID
        $steps = ApprovalStep::whereIn('step_id', $stepId)->get();
        $step = (object)[];
        foreach ($steps as $sk => $sv) {
            //判断请假天数，是否在绑定的审核步骤时间范围之内
            if($sv->min_day <= $day && $sv->max_day >= $day) $step = $sv;
        }
        if(empty($step->step)) {
            flash('申请失败,未匹配到假期模版，请联系人事', 'danger');
            return redirect()->route('leave.info');
        }
        $leaveStep = json_decode($step->step, true);

        $leader = [];

        foreach ($leaveStep as $lk => $lv) {
            $userLeader = User::where(['role_id' => $lv, 'is_leader' => 1])->first();

            if (empty($userLeader)) continue;
            $leader[$userLeader->user_id] = $userLeader->user_id;
        }

        if(empty($leader)) {
            flash('申请失败,未匹配审核人员,请联系人事', 'danger');
            return redirect()->route('leave.info');
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
            'holiday_id' => $holidayId,
            'step_id' => $step->step_id,
            'start_time' => $startTime,
            'start_id' => $p['start_id'] ?? 0,
            'end_time' => $endTime,
            'end_id' => $p['end_id'] ?? 0,
            'reason' => $p['reason'],
            'user_list' => $p['user_list'] ?? '',
            'status' => 0, //默认 0 待审批
            'annex' => $imagePath ?? '',
            'review_user_id' => $review_user_id,
            'remain_user' => $remain_user,
        ];

        try {
            $leave = Leave::create($data);
            if(!empty($leave->leave_id)) {
                OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $leave->leave_id, '提交申请');
            }
            if(!empty($userConfig)) $userConfig->update(['num' => $userConfig->num - $day]);
            //通知审核人员
            OperateLogHelper::sendWXMsg($review_user_id, '测试下');

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

        flash(trans('att.审核成功', ['value' => trans('att.假期申请')]), 'success');

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
                    $leave->update(['status' => 2, 'review_user_id' => 0]);

                    //拒绝之后，福利假回退
                    $holidayConfig = HolidayConfig::where(['holiday_id' => $leave->holiday_id])->first();
                    $userConfig = UserHoliday::where(['user_id' => $leave->user_id, 'holiday_id' => $holidayConfig->holiday_id])->first();
                    if(!empty($userConfig->num) && $holidayConfig->num >= $userConfig->num) {
                        $startTime = date('Y-m-d', strtotime($leave->start_time)) .' '. Leave::$startId[$leave->start_id];
                        $endTime = date('Y-m-d', strtotime($leave->end_time)) .' '. Leave::$endId[$leave->end_id];
                        //时间天数分配
                        $day = DataHelper::diffTime($startTime, $endTime);
                        $num = $userConfig->num + $day;
                        if($num > $holidayConfig->num) $num = $holidayConfig->num;

                        $userConfig->update(['num' => $num]);
                    }

                    break;
            }

            OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $leave->leave_id, $msg);

        } catch (Exception $ex) {
            return false;
        }

        return true;
    }
}