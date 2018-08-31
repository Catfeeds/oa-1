<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/18
 * Time: 16:48
 */

namespace App\Http\Controllers\Admin\Sys;


use App\Models\Sys\Calendar;
use App\Models\Sys\PunchRules;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    protected $redirectTo = '/admin/sys/calendar';

    private $_validateRule = [
        'year' => 'required|max:11',
        'month' => 'required|max:11',
        'day' => 'required|max:11',
        'week' => 'required|max:11',
    ];

    public function index()
    {
        $data = Calendar::orderByRaw('year desc, month desc, day desc')->paginate(31);
        $title = trans('app.日历表');
        return view('admin.sys.calendar', compact('title', 'data'));
    }

    public function create()
    {
        $calendar = (object)['punch_rules_id' => '' ,'week' => ''];
        $title = trans('app.添加日历表');
        return view('admin.sys.calendar-edit', compact('title', 'calendar'));
    }

    public function edit($id)
    {
        $calendar = Calendar::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.日历表')]);
        return view('admin.sys.calendar-edit', compact('title', 'calendar'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        Calendar::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('app.日历表')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $calendar = Calendar::findOrFail($id);

        $this->validate($request, $this->_validateRule);

        $calendar->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.日历表')]), 'success');
        return redirect($this->redirectTo);
    }

    //批量添加当月的工作日历
    public function storeAllMonth(Request $request){
        $punch_rules_id = $request->input('punch_rules_id');
        $year = date('Y', time());
        $month = date('m', time());

        if (isset($punch_rules_id)) {
            //一键生成日历需要休息这一项, 没有的话自动创建
            $rest = PunchRules::firstOrCreate(['punch_type_id' => 2, 'name' => '周末休息']);

            for ($day = 1; $day <= (int)date('t', time()); $day++) {
                //星期几
                $week = date('N', strtotime("$year-$month-$day"));

                //如果是周日还有双周的周六 ,修改排班规则为周末休息
                if (($week == 6 && !$this->ifSingleWeek("$year-$month-$day")) || $week == 7) {
                    $prId = $rest->id;
                }else{
                    $prId = $punch_rules_id;
                }

                //若日历表已有部分天数已配置则不进行覆盖添加, 没有则添加
                Calendar::firstOrCreate(
                    [
                        'year' => $year,
                        'month' => $month,
                        'day' => $day,
                        'week' => $week
                    ], ['punch_rules_id' => $prId]);
            }
            flash('批量添加日历成功', 'success');
        }else{
            flash('请选择批量添加日历的排班规则', 'danger');
        }
        return redirect()->back();
    }

    //判断单双周
    public function ifSingleWeek($arg_date){
        $date = '2018-7-30';//已知改天为单周 且星期一
        $timeDiff = strtotime($arg_date) - strtotime($date);

        if (intval($timeDiff/24/3600/7) % 2 == 1){
            return false;//双周
        }else{
            return true;//单周
        }
    }
}