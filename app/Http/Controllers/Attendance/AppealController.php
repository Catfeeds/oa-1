<?php

namespace App\Http\Controllers\Attendance;

use App\Models\Attendance\Appeal;
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppealController extends Controller
{
    public function reviewIndex()
    {
        $appeals = Appeal::with('users')->get()->toArray();
        $deptList = Dept::getDeptList();
        $operateUser = User::getAliasList();
        $countPending = Appeal::where('result', 0)->count();
        $countComplete = Appeal::where('result', '<>', 0)->count();

        $title = '申诉管理';
        return view('attendance.appeal.review', compact('title', 'appeals', 'deptList', 'operateUser', 'countPending', 'countComplete'));
    }

    public function store(Request $request)
    {
        $appealData = $request->except(['_token', 'appeal_data']);
        $arr = unserialize($request->appeal_data);
        $elseData = [
            'appeal_type'   => $arr['appeal_type'],
            'apply_type_id' => HolidayConfig::getHolidayApplyList()[$arr['holiday_id'] ?? ''] ?? NULL,
            'leave_id'      => $arr['leave_id'] ?? NULL,
            'daily_id'      => $arr['daily_id'] ?? NULL,
            'user_id'       => \Auth::user()->user_id,
            'result'        => 0,
        ];
        $appealData = array_merge($appealData, $elseData);
        Appeal::create($appealData);
        flash('申诉提交成功, 请等待结果', 'success');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $data = $request->only(['result', 'remark', 'operate_user_id']);
        Appeal::find($request->appeal_id)->update($data);
        flash('申诉处理成功', 'success');
        return redirect()->back();
    }
}
