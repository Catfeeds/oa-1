<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 10:58
 */
namespace App\Http\Controllers\Attendance;

use App\Http\Components\Helpers\PunchHelper;
use App\Http\Controllers\Controller;
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
        $year = (int)$request->input('year');$month = (int)$request->input('month');
        $startDate = sprintf('%s-%02s-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        $p = PunchHelper::getCalendarPunchRules($startDate, $endDate, true);
        $formulaCalPunRuleConf = $p['formula'];
        $info = [];
        foreach ($formulaCalPunRuleConf as $key => $v) {
            $info[] = [
                'date' => $key,
                'event_char' => PunchRules::$punchTypeChar[$p['event'][$key]->punch_type_id],
                'event' => $p['event'][$key]->name,
                'color' => PunchRules::$punchTypeColor[$p['event'][$key]->punch_type_id],
                'content' => "上班准备时间:" . explode('$$', array_first(array_keys($v['sort'])))[2] . '<br>上班时间:' . $v['start_time'][0] .
                    '<br>下班时间:' . array_last($v['end_time']) . '<br>备注:'.($p['calendar'][$key]->memo ?? '暂无'),
                'data_id' => $p['calendar'][$key]->id,
            ];
        }
        return $info;
    }

    //返回剩余天数与福利假期详情
    public function getRemainDay(){
        $info = [];
        return $info;
    }
}