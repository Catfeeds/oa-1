<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/14
 * Time: 9:48
 */

namespace App\Http\Controllers\StaffManage;

use App\Models\StaffManage\Firm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FirmController extends Controller
{
    private $_validateRule = [
        'firm' => 'required|unique:firm,firm|max:50',
    ];

    public function index()
    {
        $data = Firm::paginate();
        $title = trans('staff.公司列表');
        return view('staff-manage.firm.index', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加', ['value' => trans('staff.公司')]);
        return view('staff-manage.firm.edit', compact('title'));
    }

    public function edit($id)
    {
        $firm = Firm::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('staff.公司配置')]);
        return view('staff-manage.firm.edit', compact('title', 'firm'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        Firm::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('staff.公司')]), 'success');

        return redirect()->route('firm.list');
    }

    public function update(Request $request, $id)
    {
        $firm = Firm::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'firm' => 'required|max:50|unique:firm,firm,' . $firm->firm_id.',firm_id',
        ]));

        $firm->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('staff.公司配置')]), 'success');
        return redirect()->route('firm.list');
    }

}