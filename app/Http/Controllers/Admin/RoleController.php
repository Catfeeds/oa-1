<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleLeaveStep;
use App\Models\Sys\ApprovalStep;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $redirectTo = '/admin/role';

    private $_validateRule = [
        'name' => 'required|unique:roles,name|max:255',
        'display_name' => 'required|max:255',
        'description' => 'max:255',
    ];

    public function index()
    {
        $data = Role::paginate();
        $title = trans('app.职务列表');
        return view('admin.roles.index', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加', ['value' => trans('app.职务')]);
        return view('admin.roles.edit', compact('title'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        Role::create($request->all());

        flash(trans('app.添加成功', ['value' => trans('app.职务')]), 'success');

        return redirect($this->redirectTo);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.职务')]);
        return view('admin.roles.edit', compact('title', 'role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'name' => 'required|max:255|unique:roles,name,' . $role->id,
        ]));

        $role->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.职务')]), 'success');
        return redirect($this->redirectTo);
    }

    public function appoint($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::orderBy('id', 'asc')->get();
        $enables = $role->perms()->get()->pluck('display_name', 'id')->toArray();
        $permissionsGroup = [];
        foreach ($permissions as $v) {
            $arr = explode('/', $v->display_name);
            $tmpGroup = array_shift($arr);
            $group = $tmpGroup;
            $permissionsGroup[$group][] = [
                'id' => $v->id,
                'name' => $v->name,
                'description' => $v->description,
                'display_name' => implode('/', $arr)
            ];
        }
        \Cache::tags(\Config::get('entrust.permission_role_table'))->flush();
        $title = trans('app.职务权限指派');
        return view('admin.roles.appoint', compact('role', 'permissionsGroup', 'enables', 'title'));
    }

    public function appointUpdate($id, Request $request)
    {
        $role = Role::findOrFail($id);
        $ps = $request->get('ps');

        $olds = $role->perms()->get();

        if (!$ps) {
            $enables = [];
            $disables = $olds;
        } else {
            $news = Permission::whereIn('id', $ps)->get();
            $disables = $olds->diff($news);
            $enables = $news->diff($olds);
        }

        $role->attachPermissions($enables);
        $role->detachPermissions($disables);

        return redirect()->back();
    }

    public function editLeaveStep($id)
    {
        $steps = ApprovalStep::paginate();
        $role = Role::with('leaveStep')->findOrFail($id);
        $leaveStep = $role->leaveStep;
        $stepList = [];

        foreach ($leaveStep as $s) {
            $stepList[] = $s->step_id;
        }

        $title = trans('app.考勤步骤关联');
        return view('admin.roles.step', compact('role', 'steps', 'title', 'stepList'));
    }

    public function updateLeaveStep(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $this->validate($request, [
            'step_id' => 'required',
        ]);

        if(empty($role->id)) return redirect($this->redirectTo);
        // 清掉之前的
        RoleLeaveStep::where(['role_id' => $role->id])->delete();

        $stepIds= $request->get('step_id');
        foreach($stepIds as $sid) {
            RoleLeaveStep::create(['role_id' => $role->id, 'step_id' => $sid]);
        }

        flash(trans('app.编辑成功', ['value' => trans('app.职务与审核步骤')]), 'success');

        return redirect($this->redirectTo);
    }

}
