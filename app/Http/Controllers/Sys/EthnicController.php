<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/11/29
 * Time: 17:14
 * I民族配置信息控制器
 */

namespace App\Http\Controllers\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\Ethnic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EthnicController extends Controller
{
    private $_validateRule = [
        'ethnic' => 'required|unique:sys_users_ethnic,ethnic|max:50',
    ];

    public function index()
    {
        $form['ethnic'] = \Request::get('ethnic');
        $data = Ethnic::where('ethnic', 'LIKE', "%{$form['ethnic']}%")->orderBy('sort', 'asc')->paginate();
        $title = trans('staff.民族列表');
        return view('admin.sys.ethnic', compact('title', 'data', 'form'));
    }

    public function create()
    {
        $title = trans('app.添加', ['value' => trans('staff.民族')]);
        return view('admin.sys.ethnic-edit', compact('title'));
    }

    public function edit($id)
    {
        $ethnic = Ethnic::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('staff.民族配置')]);
        return view('admin.sys.ethnic-edit', compact('title', 'ethnic'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        Ethnic::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('staff.民族')]), 'success');

        return redirect()->route('ethnic');
    }

    public function update(Request $request, $id)
    {
        $ethnic = Ethnic::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'ethnic' => 'required|max:50|unique:sys_users_ethnic,ethnic,' . $ethnic->ethnic_id.',ethnic_id',
        ]));

        $ethnic->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('staff.民族配置')]), 'success');
        return redirect()->route('ethnic');
    }

    public function del($id)
    {
        $ethnic = Ethnic::with('usersExt', 'entry')->where(['ethnic_id' => $id])->first();
        if(empty($ethnic->ethnic_id)) {
            flash('删除失败,无效的数据ID!', 'danger');
            return redirect()->route('ethnic');
        }

        if(!empty($ethnic->usersExt->toArray()) || !empty($ethnic->entry->toArray())) {
            flash('删除失败,['.$ethnic->ethnic. ']还有在使用中!', 'danger');
            return redirect()->route('ethnic');
        }

        DB::beginTransaction();
        try{
            Ethnic::where(['ethnic_id' => $ethnic->ethnic_id])->delete();
        } catch (\Exception $ex) {
            DB::rollBack();
            flash('删除失败!', 'danger');
            return redirect()->route('ethnic');
        }
        DB::commit();

        flash(trans('app.删除成功', ['value' => trans('staff.民族配置')]), 'success');
        return redirect()->route('ethnic');
    }

}