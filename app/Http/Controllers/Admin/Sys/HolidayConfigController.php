<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/7
 * Time: 16:49
 * 假期管理配置控制
 */

namespace App\Http\Controllers\Admin\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\HolidayConfig;
use Illuminate\Http\Request;

class HolidayConfigController extends Controller
{
    protected $redirectTo = '/admin/sys/holiday-config';

    private $_validateRule = [
        'holiday' => 'required|unique:users_holiday_config,holiday|max:20',
        'memo' => 'required',
        'num' => 'required|numeric'
    ];

    public function index()
    {
        $data = HolidayConfig::paginate();
        $title = trans('app.假期配置列表');
        return view('admin.sys.holiday-config', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加假期配置');
        return view('admin.sys.holiday-config-edit', compact('title'));
    }

    public function edit($id)
    {
        $holiday = HolidayConfig::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.假期配置')]);
        return view('admin.sys.holiday-config-edit', compact('title', 'holiday'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        HolidayConfig::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('app.假期配置')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $holiday = HolidayConfig::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'holiday' => 'required|max:50|unique:users_holiday_config,holiday,' . $holiday->holiday.',holiday',
        ]));

        $holiday->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.假期配置')]), 'success');
        return redirect($this->redirectTo);
    }

}