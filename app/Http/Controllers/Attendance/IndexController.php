<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 10:58
 */
namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\PunchRules;
use App\Models\UserHoliday;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        $title = '考勤系统首页';
        $data = $this->getRemainDay();
        return view('attendance.index', compact('title', 'data'));
    }

    //接收考勤系统首页发送过来的ajax请求,完成工作日历的显示
    public function getCalendarByAjax(Request $request)
    {
        $month = (int)$request->input('month');
        $data = Calendar::where('month', $month)->get();
        $info = [];
        foreach ($data as $v) {
            $punchRules = $v->punchRules;
            $info[] = [
                'date' => date('Y-m-d', strtotime($v->year . '-' . $v->month . '-' . $v->day)),
                'event' => $punchRules->name,
                'color' => PunchRules::$punchTypeColor[$punchRules->punch_type_id],
                'content' => "上班准备时间:" . $punchRules->ready_time . '<br>上班时间:' . $punchRules->work_start_time .
                    '<br>下班时间:' . $punchRules->work_end_time . '<br>备注:'.($v->memo ?? '暂无'),
                'data_id' => $v->id,
            ];
        }
        return $info;
    }

    //返回剩余天数与福利假期详情
    public function getRemainDay(){
        $datas = UserHoliday::with('holidayConfig')->where(['user_id' => \Auth::user()->user_id])->get();
        $info = [];
        foreach ($datas as $data){
            $info[] = [
                'id' => $data->user_id,
                'holiday' => $data->holidayConfig->holiday,
                'memo' => $data->holidayConfig->memo,
                'num' => $data->num
            ];
        }
        return $info;
    }
}
