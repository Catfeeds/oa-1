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
use App\Models\Sys\Dept;
use App\User;
use Illuminate\Http\Request;

class ApprovalStepController extends Controller
{
    protected $redirectTo = '/admin/sys/approval-step';

    private $_validateRule = [
        'step' => 'required',
        'dept_id' => 'required|numeric',
        'time_range_id' => 'required|numeric',
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
        $userList = User::getUsernameAliasList();
        $title = trans('app.添加审核流程配置');
        return view('admin.sys.approval-step-create', compact('title', 'roleList', 'userList'));
    }

    public function edit($id)
    {
        $roleList = Role::getRoleTextList();
        $roleId = $stepId = $checkStep = $checkId = [];
        $step = ApprovalStep::findOrFail($id);
        $userList = User::getUsernameAliasList();
        $steps = (array)json_decode($step->step);
        foreach ($steps as $k => $v) {
            $roleId[] = $v;
            $stepId[$v] = $k;
            $checkId[$k] = $k;
            $checkStep[$v] = $k .'$$'. $v;
        }
        ksort($checkId);
        $maxStep = end($checkId);
        $dept= Dept::getDeptList();
        $title = trans('app.编辑', ['value' => trans('app.审核流程配置')]);
        return view('admin.sys.approval-step-edit', compact('title', 'step', 'roleList', 'roleId', 'stepId', 'maxStep', 'checkStep', 'dept'));
    }

    public function store(Request $request)
    {
        dd($request->all());
        $this->validate($request, $this->_validateRule);

        $p = $request->all();

        $appStep = ApprovalStep::where(['dept_id' => $p['dept_id'], 'time_range_id' => $p['time_range_id']])->first();

        if(!empty($appStep->dept_id)) {
            flash(trans('app.添加失败,已存在该配置信息'), 'danger');
            return redirect($this->redirectTo);
        }

        $step = array_filter($p['check_step']);

        $steps = [];
        foreach ($step as $k => $v) {
            $exp = explode('$$', $v);
            $steps[$exp[0]] = $exp[1];
        }

        ksort($steps);
        $data = [
            'dept_id' => $p['dept_id'],
            'time_range_id' => $p['time_range_id'],
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

        $this->validate($request, $this->_validateRule);

        $appCheckStep = ApprovalStep::where(['dept_id' => $p['dept_id'], 'time_range_id' => $p['time_range_id']])->first();

        if(!empty($appCheckStep->dept_id) && (int)$appStep->time_range_id !== (int)$appCheckStep->time_range_id) {
            flash(trans('app.添加失败,已存在该配置信息'), 'danger');
            return redirect($this->redirectTo);
        }

        $step = array_filter($p['check_step']);

        $steps = [];
        foreach ($step as $k => $v) {
            $exp = explode('$$', $v);
            $steps[$exp[0]] = $exp[1];
        }

        ksort($steps);
        $data = [
            'dept_id' => $p['dept_id'],
            'time_range_id' => $p['time_range_id'],
            'step' => json_encode($steps),
        ];

        $appStep->update($data);

        flash(trans('app.编辑成功', ['value' => trans('app.审核流程配置')]), 'success');
        return redirect($this->redirectTo);
    }

}