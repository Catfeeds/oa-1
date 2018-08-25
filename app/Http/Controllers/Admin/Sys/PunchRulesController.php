<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/18
 * Time: 16:47
 * 上下班时间规则列表控制
 */

namespace App\Http\Controllers\Admin\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\PunchRules;
use Illuminate\Http\Request;

class PunchRulesController extends Controller
{

    protected $redirectTo = '/admin/sys/punch-rules';

    private $_validateRule = [
        'name' => 'required|unique:punch_rules,name|max:32',
    ];

    public function index()
    {
        $data = PunchRules::paginate();
        $title = trans('app.上下班时间规则列表');
        return view('admin.sys.punch-rules', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加上下班时间规则');
        return view('admin.sys.punch-rules-edit', compact('title'));
    }

    public function edit($id)
    {
        $punchRules = PunchRules::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.上下班时间规则')]);
        return view('admin.sys.punch-rules-edit', compact('title', 'punchRules'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        PunchRules::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('app.上下班时间规则')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $punchRules = PunchRules::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'name' => 'required|max:32|unique:punch_rules,name,' . $punchRules->id .',id',
        ]));

        $punchRules->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.上下班时间规则')]), 'success');
        return redirect($this->redirectTo);
    }
}