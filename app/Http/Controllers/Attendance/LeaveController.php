<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 11:51
 */

namespace App\Http\Controllers\Attendance;


use App\Http\Controllers\Controller;
use App\Models\Attendance\Leave;

class LeaveController extends Controller
{
    public function index()
    {
        $data = Leave::where(['leave_id' => 1 ])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.我的假期详情');
        return view('attendance.leave.index', compact('title', 'data', 'scope'));

    }

    public function create()
    {
        $title = trans('att.请假申请');
        return view('attendance.leave.edit', compact('title'));

    }

    public function edit()
    {

    }
}