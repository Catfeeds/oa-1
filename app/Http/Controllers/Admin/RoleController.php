<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
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
        $title = trans('app.权限列表');
        return view('admin.roles.index', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加', ['value' => trans('app.权限')]);
        return view('admin.roles.edit', compact('title'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        Role::create($request->all());

        flash(trans('app.添加成功', ['value' => trans('app.权限')]), 'success');

        return redirect($this->redirectTo);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.权限')]);
        return view('admin.roles.edit', compact('title', 'role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'name' => 'required|max:255|unique:roles,name,' . $role->id,
        ]));

        $role->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.权限')]), 'success');
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
        $title = trans('app.权限指派');
        return view('admin.roles.appoint', compact('role', 'permissionsGroup', 'enables', 'title', 'id'));
    }

    public function appointUpdate($id, Request $request)
    {
        $data = $request->all();

        if(empty($data['nodesJson'])) {
            return response()->json(['status' => -1, 'url' => $this->redirectTo]);
        }
        $nodesJson = json_decode($data['nodesJson']);
        $ps = [];
        $except = Permission::getPemAllName();
        foreach ($nodesJson as $k => $v) {
            if(in_array($v->id, $except)) continue;
            $ps[] = $v->id;
        }
        $role = Role::findOrFail($id);

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

        flash(trans('app.编辑成功', ['value' => trans('app.权限')]), 'success');
        return response()->json(['status' => 1, 'url' => route('role.appoint', ['id' => $id])]);
    }

    public function getAppoint($id)
    {
        $role = Role::findOrFail($id)->perms()->get(['id'])->pluck('id')->toArray();
        $roleDesc = Role::findOrFail($id)->perms()->get(['display_name'])->pluck('display_name')->toArray();
        $permissions = Permission::orderBy('id', 'asc')->get();
        $desc = Permission::getPemDesc();

        $pemDisDesc = Permission::getPemDisDesc();

        $permissionsGroup = $checkDesc = [];
        foreach ($roleDesc as $k => $v) {
            $checkDesc[] = $pemDisDesc[$v];
            unset($pemDisDesc[$v]);
        }

        $checkDesc = array_unique($checkDesc);
        $checkDisAll = $checkDesc;

        while (!empty($checkDesc)) {
            $ext = [];
            foreach ($checkDesc as $ck => $cv) {
                if(!empty($pemDisDesc[$cv])) {
                    $checkDisAll[] = $pemDisDesc[$cv];
                    $ext[] = $pemDisDesc[$cv];
                    unset($checkDesc[$ck], $pemDisDesc[$cv]);
                }
            }
            $checkDesc = $ext;
        }
        $checkDisAll = array_unique($checkDisAll);

        if(empty($checkDisAll)) $checkDisAll[] = '所有权限';
        foreach ($permissions as $v) {
            if(in_array($v->display_name, $checkDisAll) || in_array($v->id, $role)) {
                $checked = true;
                $open = true;
            } else {
                $checked = false;
                $open = false;
            }

            $permissionsGroup[] = [
                'id' => $v->id,
                'pid' => $desc[$v->description] ?? '',
                'name' => $v->display_name,
                'open' => $open,
                'checked' => $checked,
            ];
        }

        return response()->json($permissionsGroup);
    }
}
