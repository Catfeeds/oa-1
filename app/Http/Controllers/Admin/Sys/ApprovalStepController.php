<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/8
 * Time: 20:03
 */

namespace App\Http\Controllers\Admin\Sys;


use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Sys\ApprovalStep;
use Illuminate\Http\Request;

class ApprovalStepController extends Controller
{
    protected $redirectTo = '/admin/sys/approval-step';

    private $_validateRule = [
        'name' => 'required|unique:approval_step,name|max:20',
        'step' => 'required',
        'day' => 'required|numeric'
    ];

    public function index()
    {
        $data = ApprovalStep::paginate();
        $title = trans('app.审核流程配置列表');
        return view('admin.sys.approval-step', compact('title', 'data'));
    }

    public function create()
    {

        $roleList = Role::getRoleTextList();
        $roleId = [];
        $title = trans('app.添加审核流程配置');
        return view('admin.sys.approval-step-edit', compact('title', 'roleList', 'roleId'));
    }

    public function edit($id)
    {
        $roleList = Role::getRoleTextList();
        $roleId = $stepId = $checkStep = $checkId = [];
        $step = ApprovalStep::findOrFail($id);

        $steps = (array)json_decode($step->step);
        foreach ($steps as $k => $v) {
            $roleId[] = $v;
            $stepId[$v] = $k;
            $checkId[$k] = $k;
            $checkStep[$v] = $k .'$$'. $v;
        }
        ksort($checkId);
        $maxStep = end($checkId);

        $title = trans('app.编辑', ['value' => trans('app.审核流程配置')]);
        return view('admin.sys.approval-step-edit', compact('title', 'step', 'roleList', 'roleId', 'stepId', 'maxStep', 'checkStep'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        $p = $request->all();
        $step = array_filter($p['check_step']);

        $steps = [];
        foreach ($step as $k => $v) {
            $exp = explode('$$', $v);
            $steps[$exp[0]] = $exp[1];
        }

        ksort($steps);
        $data = [
            'name' => $p['name'],
            'day' => $p['day'],
            'step' => json_encode($steps),
        ];

        ApprovalStep::create($data);
        flash(trans('app.添加成功', ['value' => trans('app.审核流程配置')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $appStep = ApprovalStep::findOrFail($id);
        $p = $request->all();
        $this->validate($request, array_merge($this->_validateRule, [
            'name' => 'required|max:20|unique:approval_step,name,' . $p['name'] .',name',
        ]));


        $step = array_filter($p['check_step']);

        $steps = [];
        foreach ($step as $k => $v) {
            $exp = explode('$$', $v);
            $steps[$exp[0]] = $exp[1];
        }

        ksort($steps);
        $data = [
            'name' => $p['name'],
            'day' => $p['day'],
            'step' => json_encode($steps),
        ];

        $appStep->update($data);

        flash(trans('app.编辑成功', ['value' => trans('app.审核流程配置')]), 'success');
        return redirect($this->redirectTo);
    }

}