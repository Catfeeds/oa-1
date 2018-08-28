<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/18
 * Time: 16:48
 */

namespace App\Http\Controllers\Admin\Sys;


use App\Models\Sys\Calendar;
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
        $data = Calendar::paginate();
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
}