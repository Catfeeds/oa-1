<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/18
 * Time: 16:48
 */

namespace App\Http\Controllers\Admin\Sys;


use App\Http\Components\ScopeAtt\CalendarScope;
use App\Http\Controllers\Attendance\AttController;
use App\Models\Attendance\DailyDetail;
use App\Models\Sys\Calendar;
use App\Models\Sys\PunchRules;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CalendarController extends AttController
{
    protected $redirectTo = '/admin/sys/calendar';
    protected $scopeClass = CalendarScope::class;

    private $_validateRule = [
        'year'  => 'required|max:11',
        'month' => 'required|max:11',
        'day'   => 'required|max:11',
        'week'  => 'required|max:11',
    ];

    public function index()
    {
        //$data = Calendar::orderByRaw('year desc, month desc, day desc')->paginate(31);
        $title = trans('app.日历表');
        $scope = $this->scope;
        $startMonth = date('m', strtotime($scope->startDate));
        $endMonth = date('m', strtotime($scope->endDate));
        return view('admin.sys.calendar', compact('title', 'data', 'scope', 'startMonth', 'endMonth'));
    }

    public function list(Request $request)
    {
        $title = trans('app.日历表');
        $scope = $this->scope;
        $startMonth = date('m', strtotime($scope->startDate));
        $endMonth = date('m', strtotime($scope->endDate));
        $year = date('Y', strtotime($scope->startDate));
        $data = Calendar::where([['year', '=', $year], ['month', '<=', $endMonth], ['month', '>=', $startMonth]])
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('day', 'asc')
            ->get();
        return view('admin.sys.calendar-list', compact('title', 'data', 'scope', 'startMonth', 'endMonth'));
    }

    public function create()
    {
        $calendar = (object)['punch_rules_id' => '', 'week' => ''];
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

        $calendar->update($request->except('back'));

        flash(trans('app.编辑成功', ['value' => trans('app.日历表')]), 'success');

        if ($request->input('back') !== 0) {
            return redirect(urldecode($request->input('back')));
        }
        return redirect($this->redirectTo);
    }

    //批量添加当月的工作日历
    public function storeAllMonth(Request $request)
    {
        $punch_rules_id = $request->input('punch_rules_id');
        $selectDates = json_decode($request->input('select_date'));
        array_shift($selectDates);

        collect($selectDates)->flatten()->Map(function ($date) use ($punch_rules_id) {
            $arrDate = explode('-', $date);
            $cal = Calendar::where([
                'year'  => $arrDate[0],
                'month' => $arrDate[1],
                'day'   => $arrDate[2],
                'week'  => date('N', strtotime($date))
            ])->first();

            if (isset($cal)) {
                $cal->punch_rules_id = $punch_rules_id;
                $cal->save();
            }else {
                Calendar::create([
                    'year'  => $arrDate[0],
                    'month' => $arrDate[1],
                    'day'   => $arrDate[2],
                    'week'  => date('N', strtotime($date)),
                    'punch_rules_id' => $punch_rules_id
                ]);
            }
        });

        return redirect()->back();
    }
}