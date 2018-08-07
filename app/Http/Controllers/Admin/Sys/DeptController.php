<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/1
 * Time: 15:56
 * 部门管理控制
 */
namespace App\Http\Controllers\Admin\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\Dept;
use Illuminate\Http\Request;

class DeptController extends Controller
{

    protected $redirectTo = '/admin/sys/dept';

    private $_validateRule = [
        'dept' => 'required|unique:users_dept,dept|max:50',
    ];

    public function index()
    {
        $data = Dept::paginate();
        $title = trans('app.部门列表');
        return view('admin.sys.dept', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('att.添加部门');
        return view('admin.sys.dept-edit', compact('title'));
    }

    public function edit($id)
    {
        $dept = Dept::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.部门')]);
        return view('admin.sys.dept-edit', compact('title', 'dept'));
    }

    public function store(Request $request)
    {

        $this->validate($request, $this->_validateRule);

        Dept::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('app.部门')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $dept = Dept::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'dept' => 'required|max:50|unique:users_dept,dept,' . $dept->dept_id.',dept_id',
        ]));

        $dept->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.部门')]), 'success');
        return redirect($this->redirectTo);
    }

}