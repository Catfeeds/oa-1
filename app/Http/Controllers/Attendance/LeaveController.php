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
                $where = sprintf(" AND user_id = %d AND parent_id !=''", \Auth::user()->user_id);
                break;
            //抄送
            case Leave::COPY_LEAVE :
                $res = AttendanceHelper::getCopyLeaveWhere($scope,\Auth::user()->user_id, 'copy_user');
                $where = $res['where'];
                $userIds = $res['user_ids'];
                break;
            default:
                $where = sprintf(" AND user_id = %d AND parent_id is null and is_stat = %d", \Auth::user()->user_id, Leave::IS_STAT_YES);
                break;
        }

        $data = Leave::whereRaw($scope->getWhere() . $where)
            ->whereHas('holidayConfig', function ($query) {
                $query->where('is_show', HolidayConfig::STATUS_ENABLE);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $remainWelfare = 0;

        $appealData = Appeal::getAppealResult(Appeal::APPEAL_LEAVE);

        $title = trans('att.我的假期详情');
        return view('attendance.leave.index',
            compact('title', 'data', 'scope', 'holidayList', 'userIds', 'types', 'type', 'remainWelfare', 'appealData', 'userIds')
        );
    }

    public function create($applyTypeId)
    {
        if(!in_array((int)$applyTypeId, HolidayConfig::$driverTypeId)) {
            flash('申请失败,请勿非法操作', 'danger');
            return redirect()->route('leave.info');
        }

        //驱动
        $driver = HolidayConfig::$driverType[$applyTypeId];
        //返回申请不同页面
        $res = \AttendanceService::driver($driver)->getLeaveView();
        return $res;
    }

    public function edit($applyTypeId, $id)
    {
        if(!in_array((int)$applyTypeId, HolidayConfig::$driverTypeId)) {
            flash('申请失败,请勿非法操作', 'danger');
            return redirect()->route('leave.info');
        }

        //驱动
        $driver = HolidayConfig::$driverType[$applyTypeId];
        //返回申请不同页面
        $res = \AttendanceService::driver($driver)->getLeaveView($id);
        return $res;
    }

    /**
     * 假期提交申请
     * @param Request $request 表单数据
     * @param int $applyTypeId 请假类型  1:请假 2:调休 3:补打卡 4:批量申请加班
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $applyTypeId)
    {
        //验证是否是有效的请假配置类型
        if(!in_array((int)$applyTypeId, HolidayConfig::$driverTypeId)) {
            flash('申请失败,请勿非法操作', 'danger');
            return redirect()->route('leave.info');
        }
        $p = $request->all();
        //驱动
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

    public function update(Request $request, $applyTypeId, $id)
    {
        $p = $request->all();

        //验证是否是有效的请假配置类型和数据
        if(empty($p['leave_id']) || !in_array((int)$applyTypeId, HolidayConfig::$driverTypeId)) {
            flash('申请失败,请重新提交申请!', 'danger');
            return redirect()->route('leave.info');
        }

        $leaveS = Leave::with('holidayConfig')->findOrFail($p['leave_id']);
        if(empty($leaveS->leave_id) || $leaveS->leave_id !== (int)$id) {
            flash('申请失败,请重新提交申请!', 'danger');
            return redirect()->route('leave.info');
        }

        //驱动
        $driver = HolidayConfig::$driverType[$applyTypeId];
        //申请单检验
        $retCheck = \AttendanceService::driver($driver)->checkLeave($request);
        if(!$retCheck['success']) return redirect()->back()->with(['holiday_id' => $p['holiday_id']])->withInput()->withErrors($retCheck['message']);
        //获取申请单审核步骤流程
        $step = \AttendanceService::driver($driver)->getLeaveStep($p, $retCheck['data']['number_day']);
        if(!$step['success']) return redirect()->back()->with(['holiday_id' => $p['holiday_id']])->withInput()->withErrors($step['message']);
        //设置上传图片
        $imagePath = AttendanceHelper::setAnnex($request, $leaveS->annex);
        //数据整合
        $leave = array_merge($retCheck['data'], $step['data']);
        $leave['image_path'] = $imagePath;
        $leave['reason'] = $p['reason'];
        $leave['leave_id'] = $leaveS->leave_id;

        //事物开始
        DB::beginTransaction();
        try {
            //创建申请单
            $retLeave = \AttendanceService::driver($driver)->updateLeave($leave);
            //日志记录操作
            if($retLeave['success']) {
                OperateLogHelper::createOperateLog(OperateLogHelper::LEAVE_TYPE_ID, $retLeave['data']['leave_id'], '重启申请单');
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

        flash(trans('att.重启申请单成功'), 'success');
        return redirect()->route('leave.info');
    }

    /**
     * 查看我的申请信息
     * @param $id
     * @return // View
     */
    public function optInfo($id)
    {

        $leave = Leave::with('holidayConfig')->findOrFail($id);
        if(empty($leave->leave_id)) return redirect()->route('leave.info');
        //抄送人员也可以查看
        $copyIds = json_decode($leave->copy_user, true);
        $userIds = json_decode($leave->user_list, true);

        if((!empty($copyIds) && in_array(\Auth::user()->user_id, $copyIds)) || (!empty($userIds) && in_array(\Auth::user()->user_id, $userIds)) || \Auth::user()->user_id === $leave->user_id) {
            $reviewUserId = $leave->review_user_id;
            $logs = OperateLog::where(['type_id' => OperateLog::LEAVED, 'info_id' => $leave->leave_id])->get();
            $dept = Dept::getDeptList();
            $title = trans('att.申请单详情');
            $applyTypeId = $leave->holidayConfig->apply_type_id;
            $cypherType = $leave->holidayConfig->cypher_type;
            $users = User::getUsernameAliasAndDeptList();
            return view('attendance.leave.info', compact('title',  'leave', 'dept', 'users', 'reviewUserId',  'logs', 'applyTypeId', 'userIds', 'deptUsers', 'cypherType'));
        } else {
            return redirect()->route('leave.info');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function optReviewInfo($id)
    {
        $leave = Leave::with('holidayConfig')->findOrFail($id);

        //操作过的人可查看
        $logUserIds = OperateLogHelper::getLogUserIdToInId($leave->leave_id);
        $logUserIds[] = $leave->user_id;
        $logUserIds[] = $leave->review_user_id;

        if((in_array(\Auth::user()->user_id, $logUserIds) || $leave->user_id === \Auth::user()->user_id ) && !empty($leave->leave_id) ) {
            $userIds = json_decode($leave->user_list, true);
            $reviewUserId = $leave->review_user_id;
            $logs = OperateLog::where(['type_id' => OperateLog::LEAVED, 'info_id' => $leave->leave_id])->get();
            $dept = Dept::getDeptList();
            $title = trans('att.申请单详情');
            $applyTypeId = $leave->holidayConfig->apply_type_id;
            $users = User::getUsernameAliasAndDeptList();
            return view('attendance.leave.review-info', compact('title',  'leave', 'dept', 'users', 'reviewUserId',  'logs', 'applyTypeId', 'userIds', 'deptUsers'));
        } else {
            return redirect()->route('leave.review.info');
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
     * 申请单操作
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviewOptStatus(Request $request, $id)
    {
        $status = $request->get('status');

        if(!in_array($status, Leave::$optStatus) || empty($id)) return response()->json(['status' => -1, 'msg' => '操作失败']);

        $res = self::OptStatus($id, $status);

        if($res['success']) {
            return response()->json(['status' => 1, 'msg' => '操作成功', 'url' => $res['url'] ?? '']);
        } else {
            return response()->json(['status' => -1, 'msg' => '操作失败,请勿频繁操作或记录已处理!']);
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

        if(!in_array($status, Leave::$optStatus) || empty($status) || empty($leaveIds) || !is_array($leaveIds)) {
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
     * @return array
     */
    public function OptStatus($leaveId, $status)
    {
        $leave = Leave::with('holidayConfig')->findOrFail($leaveId);

        if($leave->status === (int)$status || empty($leave->leave_id)) ['success' => false];

        //mysql事物开始
        DB::beginTransaction();
        try {
            //驱动
            $driver = Leave::$driverType[$status];
            //申请单状态操作
            $res = \AttendanceService::driver($driver, 'optStatus')->optLeaveStatus($leave, $status);

        } catch (Exception $ex) {
            //mysql事物回滚
            DB::rollBack();
            return ['success' => false];
        }
        //mysql事物提交
        DB::commit();

        return $res;
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

        $driver = HolidayConfig::$cypherTypeChar[$holidayConfig->cypher_type];

        $numberDay = \AttendanceService::driver($driver, 'cypher')->getLeaveNumberDay($p);
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