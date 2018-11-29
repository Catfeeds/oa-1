<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/14
 * Time: 9:48
 */

namespace App\Http\Controllers\Admin\Sys;

use App\Models\StaffManage\Firm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirmController extends Controller
{
    private $_validateRule = [
        'firm' => 'required|unique:firm,firm|max:50',
    ];

    public function index()
    {
        $form['firm'] = \Request::get('firm');
        $data = Firm::where('firm', 'LIKE', "%{$form['firm']}%")->paginate();
        $title = trans('staff.公司列表');
        return view('admin.sys.firm', compact('title', 'data', 'form'));
    }

    public function create()
    {
        $title = trans('app.添加', ['value' => trans('staff.公司')]);
        return view('admin.sys.firm-edit', compact('title'));
    }

    public function edit($id)
    {
        $firm = Firm::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('staff.公司配置')]);
        return view('admin.sys.firm-edit', compact('title', 'firm'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        Firm::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('staff.公司')]), 'success');

        return redirect()->route('firm');
    }

    public function update(Request $request, $id)
    {
        $firm = Firm::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'firm' => 'required|max:50|unique:firm,firm,' . $firm->firm_id.',firm_id',
        ]));

        $firm->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('staff.公司配置')]), 'success');
        return redirect()->route('firm');
    }

    public function del($id)
    {
        $firm = Firm::with('usersExt', 'entry')->where(['firm_id' => $id])->first();
        if(empty($firm->firm_id)) {
            flash('删除失败,无效的数据ID!', 'danger');
            return redirect()->route('firm');
        }

        if(!empty($firm->usersExt->toArray()) || !empty($firm->entry->toArray())) {
            flash('删除失败,['.$firm->firm. ']还有在使用中!', 'danger');
            return redirect()->route('firm');
        }

        DB::beginTransaction();
        try{
            Firm::where(['firm_id' => $firm->firm_id])->delete();
        } catch (\Exception $ex) {
            DB::rollBack();
            flash('删除失败!', 'danger');
            return redirect()->route('firm');
        }
        DB::commit();

        flash(trans('app.删除成功', ['value' => trans('app.公司配置')]), 'success');
        return redirect()->route('firm');
    }

}