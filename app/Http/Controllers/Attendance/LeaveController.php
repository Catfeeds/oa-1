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
use App\Http\Controllers\Controller;
use App\Models\Attendance\Leave;
use App\Models\RoleLeaveStep;
use App\Models\Sys\ApprovalStep;
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\User;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    private $_validateRule = [
        'start_time' => 'required',
        'end_time' => 'required',
        'reason' => 'required',
    ];

    public function index()
    {
        $data = Leave::orderBy('created_at', 'desc')
            ->paginate(30);

        $holidayList = HolidayConfig::getHolidayList();
        $title = trans('att.我的假期详情');
        return view('attendance.leave.index', compact('title', 'data', 'scope', 'holidayList'));

    }

    public function create()
    {
        $leave = (object)['holiday_id' => '', 'start_id' => '', 'end_id' => ''];
        $holidayList = HolidayConfig::getHolidayList();
        $title = trans('att.请假申请');
        return view('attendance.leave.edit', compact('title', 'holidayList', 'leave'));
    }

    public function edit($id)
    {
        Leave::findOrFail($id);
        $holidayList = HolidayConfig::getHolidayList();
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

        $leaveStep = json_decode($step->step, true);
        $leader = [];
        foreach ($leaveStep as $lk => $lv) {
            $userLeader = User::where(['role_id' => $lv, 'is_leader' => 1])->first();
            if (empty($userLeader)) continue;
            $leader[$userLeader->user_id] = $userLeader->user_id;
        }

        $data = [
            'apply_type_id' => 1,
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
            'review_user_id' => reset($leader),
            'remain_user' => json_encode(array_pop($leader)),
        ];

        Leave::create($data);
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
        $holidayList = HolidayConfig::getHolidayList();
        $dept = Dept::getDeptList();
        $title = trans('att.假期详情');
        return view('attendance.leave.info', compact('title', 'holidayList', 'leave', 'dept'));
    }

}