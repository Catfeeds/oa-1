<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/1
 * Time: 16:31
 *  学校管理配置控制
 */

namespace App\Http\Controllers\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    protected $redirectTo = '/sys/school';

    private $_validateRule = [
        'school' => 'required|unique:users_school,school|max:50',
    ];

    public function index()
    {
        $form['school'] = \Request::get('school');
        $data = School::where('school', 'LIKE', "%{$form['school']}%")->paginate();
        $title = trans('app.学校列表');
        return view('admin.sys.school', compact('title', 'data', 'form'));
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

    public function del($id)
    {
        $school = School::with('usersExt', 'entry')->where(['school_id' => $id])->first();
        if(empty($school->school_id)) {
            flash('删除失败,无效的数据ID!', 'danger');
            return redirect($this->redirectTo);
        }

        if(!empty($school->usersExt->toArray()) || !empty($school->entry->toArray())) {
            flash('删除失败,['.$school->school. ']还有在使用中!', 'danger');
            return redirect($this->redirectTo);
        }

        DB::beginTransaction();
        try{
            School::where(['school_id' => $school->school_id])->delete();
        } catch (\Exception $ex) {
            DB::rollBack();
            flash('删除失败!', 'danger');
            return redirect($this->redirectTo);
        }
        DB::commit();

        flash(trans('app.删除成功', ['value' => trans('app.学校')]), 'success');
        return redirect($this->redirectTo);
    }

}