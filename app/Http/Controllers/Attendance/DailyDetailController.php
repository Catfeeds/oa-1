<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/21
 * Time: 9:26
 * 考勤明细，考勤功能管理
 */

namespace App\Http\Controllers\Attendance;

use App\Http\Components\ScopeAtt\DailyScope;
use App\Http\Controllers\Controller;
use App\Models\Attendance\ConfirmAttendance;
use App\Models\Attendance\DailyDetail;
use App\Models\Sys\HolidayConfig;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class DailyDetailController extends AttController
{
    public $review;
    protected $scopeClass = DailyScope::class;

    public function __construct(ReviewController $review)
    {
        $this->review = $review;
    }

    public function index()
    {
        $data = DailyDetail::where(['user_id' => \Auth::user()->user_id])->orderBy('created_at', 'desc')
            ->paginate(30);
        $userInfo['username'] = \Auth::user()->username;
        $userInfo['alias'] = \Auth::user()->alias;

        $month = $this->getMonthAttendance(\Auth::user()->user_id);
        if ($month[0] == 'error') {
            foreach ($month[1] as $message) {
                flash($message['message'], $message['sign']);
            }
            return redirect()->route('holiday-config');
        }
        $monthData = $month[1];

        $scope = $this->scope;
        $title = trans('att.我的每日考勤详情');
        return view('attendance.daily-detail.index', compact('title', 'data', 'scope', 'userInfo', 'monthData', 'scope'));
    }

    //明细
    public function detail($id)
    {
        $data = DailyDetail::where('user_id', $id)->orderBy('created_at', 'desc')->paginate(30);
        $userInfo['username'] = User::where('user_id', $id)->first()->username;
        $userInfo['alias'] = User::where('user_id', $id)->first()->alias;
        $title = "用户{$id}的考勤详情";
        return view('attendance.daily-detail.index', compact('title', 'data', 'userInfo'));
    }

    //重新初始化scope,调用review控制器的方法
    public function getMonthAttendance($id)
    {
        $param = $this->setScopeParams();
        $param['daily_user_id'] = $id;
        $this->scope = new $this->scopeClass($param, null);
        return $this->review->dealAttendance($this->scope, $id);
    }

    //用户确认考勤通知
    public function confirm(Request $request)
    {
        list($year, $month) = explode('-', $request->date);
        Redis::del("att-".$request->date);
        $a = ConfirmAttendance::where(['user_id' => $request->id, 'year' => $year, 'month' => $month])
            ->update(['confirm' => ConfirmAttendance::CONFIRM]);
        if ($a) {
            flash('确认成功!', 'success');
        } else {
            flash('确认失败', 'danger');
        }
        return redirect()->route('daily-detail.info');
    }

}