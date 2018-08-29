<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/27
 * Time: 9:59
 */

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\DailyDetail;
use Illuminate\Http\Request;


class ReviewController extends Controller
{
    public function index()
    {
        $data = DailyDetail::where(['user_id' => \Auth::user()->user_id])->orderBy('created_at', 'desc')
            ->paginate(30);

        $title = trans('att.考勤管理');
        return view('attendance.daily-detail.review', compact('title', 'data', 'scope'));

    }

}