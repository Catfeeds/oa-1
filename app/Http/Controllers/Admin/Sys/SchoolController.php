<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/1
 * Time: 16:31
 *  学校管理配置控制
 */

namespace App\Http\Controllers\Admin\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    protected $redirectTo = '/admin/sys/school';

    private $_validateRule = [
        'school' => 'required|unique:users_school,school|max:50',
    ];

    public function index()
    {
        $data = School::paginate();
        $title = trans('app.学校列表');
        return view('admin.sys.school', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加学校');
        return view('admin.sys.school-edit', compact('title'));
    }

    public function edit($id)
    {
        $school = School::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.学校')]);
        return view('admin.sys.school-edit', compact('title', 'school'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        School::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('app.学校')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $school = School::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'school' => 'required|max:50|unique:users_school,school,' . $school->school.',school_id',
        ]));

        $school->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.学校')]), 'success');
        return redirect($this->redirectTo);
    }

}