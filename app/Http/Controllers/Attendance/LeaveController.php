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
use App\Http\Components\Helpers\AttendanceHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Http\Components\Helpers\ReviewHelper;
use App\Http\Components\ScopeAtt\LeaveScope;
use App\Models\Attendance\Appeal;
use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\OperateLog;
use App\Models\Sys\PunchRulesConfig;
use App\Models\UserExt;
use App\User;
use EasyWeChat\Kernel\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends AttController
{
    protected $scopeClass = LeaveScope::class;
    public $reviewHelper;

    public function __construct(ReviewHelper $reviewHelper)
    {
        $this->reviewHelper = $reviewHelper;
    }

    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'attendance.leave.scope';

        $types = Leave::$types;
        $type = \Request::get('type', key($types));

        $userIds = [];
        switch ($type) {
            //调休
            case Leave::DEPT_LEAVE :
                $where = AttendanceHelper::setChangeList($type)['where'];
                $userIds = AttendanceHelper::setChangeList($type)['user_ids'];
                break;
            //抄送
            case Leave::COPY_LEAVE :
                $where = AttendanceHelper::setChangeList($type)['where'];
                $userIds = AttendanceHelper::setChangeList($type)['user_ids'];
                break;
            default:
                $where = sprintf(" AND user_id = %s", \Auth::user()->user_id);
                break;
        }
        $data = Leave::whereRaw($scope->getWhere() . $where)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $remainWelfare = 0;

        $appealData = Appeal::getAppealResult(Appeal::APPEAL_LEAVE);

        $title = trans('att.我的假期详情');
        return view('attendance.leave.index',
            compact('title', 'data', 'scope', 'holidayList', 'userIds', 'types', 'type', 'remainWelfare', 'appealData')
        );
    }

    public function create($applyTypeId)
    {
        $leave = (object)['holiday_id' => '', 'start_id' => '', 'end_id' => ''];
        $reviewUserId = '';
        $allUsers = User::where(['status' => 1])->get();
        $time = date('Y-m-d', time());
        switch ($applyTypeId) {
            //请假
            case HolidayConfig::LEAVEID:
                $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::LEAVEID])
                    ->whereIn('restrict_sex', [\Auth::user()->userExt->sex, UserExt::SEX_NO_RESTRICT])
                    ->where(['is_show' => HolidayConfig::STATUS_ENABLE])
                    ->orderBy('sort', 'desc')
                    ->get(['holiday_id', 'holiday'])
                    ->pluck('holiday', 'holiday_id')
                    ->toArray();
                $models = 'edit';
                $title = trans('att.请假申请');
                break;
            //调休
            case HolidayConfig::CHANGE:
                $holidayList = HolidayConfig::where(['apply_type_id' => HolidayConfig::CHANGE])
                    ->orderBy('sort', 'desc')
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
                    ->orderBy('sort', 'desc')
                    ->get(['holiday_id', 'holiday', 'punch_type']);
                $daily = DailyDetail::where(['user_id' => \Auth::user()->user_id, 'day' => request()->day])->first();
                $models = 'recheck';
                $title = trans('att.补打卡');
                break;
            default:
                return redirect()->route('leave.info');
        }

        return view('attendance.leave.'.$models, compact('title', 'time', 'holidayList', 'leave', 'reviewUserId' , 'deptUsersSelected', 'deptUsers', 'allUsers', 'daily'));
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

        //申请单检验
        $retCheck = \AttendanceService::driver($driver)->checkLeave($request);
        if(!$retCheck['success']) return redirect()->back()->with(['holiday_id' => $p['holiday_id']])->withInput()->withErrors($retCheck['message']);
        //获取申请单审核步骤流程
        $step = \AttendanceService::driver($driver)->getLeaveStep($p, $retCheck['data']['number_day']);
        if(!$step['success']) return redirect()->back()->with(['holiday_id' => $p['holiday_id']])->withInput()->withErrors($step['message']);
        //设置上传图片
         $imagePath = AttendanceHelper::setAnnex($request);
        //数据整合
        $leave = array_merge($retCheck['data'], $step['data']);
        $leave['image_path'] = $imagePath;
        $leave['reason'] = $p['reason'];

        //事物开始
        DB::beginTransaction();
        try {
            //创建申请单
            $retLeave = \AttendanceService::driver($driver)->createLeave($leave);
            //日志记录操作
            if($retLeave['success']) {
                OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $retLeave['data']['leave_id'], '提交申请');
            }
            //微信通知审核人员
            //OperateLogHelper::sendWXMsg($review_user_id, '测试下');

        } catch (Exception $ex) {
            //事物回滚
            DB::rollBack();
            flash('申请失败,请重新提交申请!', 'danger');
            return redirect()->route('leave.info');
        }
        //事物提交
        DB::commit();

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


    public function optInfo($id, $type)
    {
        if(!in_array($type, [Leave::LOGIN_INFO, Leave::LOGIN_VERIFY_INFO])) return redirect()->route('leave.info');

        $leave = Leave::with('holidayConfig')->findOrFail($id);

        $logUserIds = OperateLogHelper::getLogUserIdToInId($leave->leave_id);
        $logUserIds[] = $leave->user_id;
        $logUserIds[] = $leave->review_user_id;
        $userDept = User::getUserAliasToId($leave->user_id);
        //调休包含人员也可以查看
        $userIds = json_decode($leave->user_list, true);
        if((in_array(\Auth::user()->user_id, $logUserIds) || in_array(\Auth::user()->user_id, $userIds)) && !empty($leave->leave_id) ) {
            $reviewUserId = $leave->review_user_id;
            $user = User::with(['dept'])->where(['user_id' => $reviewUserId])->first();
            $logs = OperateLog::where(['type_id' => 1, 'info_id' => $leave->leave_id])->get();
            $dept = Dept::getDeptList();
            $title = trans('att.假期详情');
            $applyTypeId = HolidayConfig::getHolidayApplyList()[$leave->holiday_id];
            $deptUsers = User::where(['dept_id' => $userDept->dept_id, 'status' => 1])->get()->toArray();
            $deptUsers = array_filter($deptUsers);
            return view('attendance.leave.info', compact('title',  'leave', 'dept', 'reviewUserId', 'user', 'logs', 'applyTypeId', 'userIds', 'deptUsers', 'type'));
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

        if(!in_array($status, [Leave::REFUSE_REVIEW, Leave::PASS_REVIEW, Leave::CANCEL_REVIEW]) || empty($id)) return response()->json(['status' => -1, 'msg' => '操作失败']);

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

        if(!in_array($status, [Leave::REFUSE_REVIEW, Leave::PASS_REVIEW, Leave::CANCEL_REVIEW]) || empty($status) || empty($leaveIds) || !is_array($leaveIds)){
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
        //mysql事物开始
        DB::beginTransaction();
        try {
            switch ($status) {
                //拒绝通过状态
                case Leave::REFUSE_REVIEW:
                    $msg = '拒绝通过';
                    $leave->update(['status' => Leave::REFUSE_REVIEW, 'review_user_id' => 0]);
                    break;
                //审核通过状态
                case Leave::PASS_REVIEW:
                    $msg = '审核通过';
                    //申请单状态操作
                    $driver = HolidayConfig::$driverType[$leave->holidayConfig->apply_type_id];
                    \AttendanceService::driver($driver)->leaveReviewPass($leave);
                    break;
                case Leave::CANCEL_REVIEW:
                    $msg = '取消申请';
                    $leave->update(['status' => Leave::CANCEL_REVIEW, 'review_user_id' => 0]);
                    break;
            }

            OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $leave->leave_id, $msg);

        } catch (Exception $ex) {
            //mysql事物回滚
            DB::rollBack();
            return false;
        }
        //mysql事物提交
        DB::commit();

        return true;
    }

    /**
     * 显示申请类型剩余情况和描述展示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMemo(Request $request)
    {
        $p = $request->all();
        $holidayConfig = HolidayConfig::checkHolidayToId($p['id']);
        if(empty($holidayConfig->holiday_id))  return response()->json(['status' => -1, 'memo' => '', 'day' => 0]);

        $driver = HolidayConfig::$cypherTypeChar[$holidayConfig->cypher_type];
        $ret = \AttendanceService::driver($driver, 'cypher')->getUserHoliday(\Auth::user()->userExt->entry_time, \Auth::user()->user_id, $holidayConfig);

        return response()->json($ret);
    }

    public function inquire(Request $request)
    {

        $p = $request->all();
        $holidayConfig = HolidayConfig::checkHolidayToId($p['holidayId']);

        if(empty($holidayConfig->holiday_id))  return response()->json(['status' => -1, 'memo' => '', 'day' => 0]);

        $numberDay = DataHelper::leaveDayDiff($p['startTime'], $p['startId'], $p['endTime'], $p['endId']);

        $driver = HolidayConfig::$cypherTypeChar[$holidayConfig->cypher_type];
        $ret = \AttendanceService::driver($driver, 'cypher')->showLeaveStep($holidayConfig->holiday_id, $numberDay);

        $html = <<<HTML
            $ret
HTML;

        return response()->json(['status' => 1, 'step' => $html]);
    }




    /**
     * 请假类型 显示每日时间点获取
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPunchRules(Request $request)
    {
        $p = $request->all();

        if(empty($p['time'])) return response()->json(['status' => -1, 'start_time' => '', 'end_time' => '']);

        list($year, $month, $day) = explode('-', $p['time']);
        $punchRules = Calendar::where(['year' => (int)$year, 'month' => (int)$month, 'day' => (int)$day])->first();

        if(empty($punchRules->punch_rules_id)) return response()->json(['status' => -1, 'start_time' => '', 'end_time' => '']);

        $config = PunchRulesConfig::getPunchRulesCfgToId($punchRules->punch_rules_id);

        if(!empty($config)) {
            return response()->json(['status' => 1, 'start_time' => $config['start_time'], 'end_time' => $config['end_time'], 'last_time' => [ end($config['end_time'])] ]);
        } else {
            return response()->json(['status' => -1, 'start_time' => '', 'end_time' => '']);
        }
    }

}