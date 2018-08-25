<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/21
 * Time: 9:26
 * 考勤明细，考勤功能管理
 */

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\DailyDetail;
use Illuminate\Http\Request;

class DailyDetailController extends Controller
{
    public function index()
    {
        $data = DailyDetail::where(['user_id' => \Auth::user()->user_id])->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.我的每日考勤详情');
        return view('attendance.daily-detail.index', compact('title', 'data', 'scope'));

    }

    public function reviewIndex()
    {
        $data = DailyDetail::where(['user_id' => \Auth::user()->user_id])->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.考勤功能');
        return view('attendance.daily-detail.review', compact('title', 'data', 'scope'));

    }

    public function reviewImport(Request $request)
    {

    }

}