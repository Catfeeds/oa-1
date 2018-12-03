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
use Illuminate\Support\Facades\DB;

class DeptController extends Controller
{
    protected $redirectTo = '/admin/sys/dept';

    private $_validateRule = [
        'dept' => 'required|unique:users_dept,dept|max:50',
    ];

    public function index()
    {
        $form['dept'] = \Request::get('dept');
        $data = Dept::where('dept', 'LIKE', "%{$form['dept']}%")
            ->whereNull('parent_id')
            ->paginate();
        $parent = Dept::whereNotNull('parent_id')
            ->get(['dept_id', 'parent_id'])
            ->pluck('parent_id', 'dept_id')
            ->toArray();

        $title = trans('app.部门列表');
        return view('admin.sys.dept', compact('title', 'data', 'form', 'parent'));
    }

    public function create()
    {
        $title = trans('att.添加部门');
        return view('admin.sys.dept-edit', compact('title'));
    }

    public function edit($id)
    {
        $dept = Dept::findOrFail($id);

        $parent = Dept::where(['parent_id' => $dept->dept_id])->get();

        $title = trans('app.编辑', ['value' => trans('app.部门')]);
        return view('admin.sys.dept-edit', compact('title', 'dept', 'parent'));
    }

    public function store(Request $request)
    {

        $this->validate($request, $this->_validateRule);
        $p = $request->all();

        $data = [
            'dept' => $p['dept'],
        ];

        DB::beginTransaction();
        try{
            $dept = Dept::create($data);

            if(!empty($p['child']) && is_array($p['child'])) {
                foreach ($p['child'] as $k => $v) {
                    $child = [
                        'dept' => $v,
                        'parent_id' => $dept->dept_id
                    ];
                    Dept::create($child);
                }
            }
        } catch (\Exception $ex){
            DB::rollBack();
            flash(trans('app.添加失败', ['value' => trans('app.部门')]), 'danger');
            return redirect($this->redirectTo);
        }

        DB::commit();

        flash(trans('app.添加成功', ['value' => trans('app.部门')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $p = $request->all();

        $dept = Dept::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'dept' => 'required|max:50|unique:users_dept,dept,' . $dept->dept_id.',dept_id',
        ]));

        $data = [
            'dept' => $p['dept'],
        ];

        DB::beginTransaction();
        try{
            $dept->update($data);

            if(!empty($p['child']) && is_array($p['child'])) {
                foreach ($p['child'] as $k => $v) {
                    $child = [
                        'dept' => $v,
                        'parent_id' => $dept->dept_id
                    ];
                    $check = Dept::where(['dept_id' => $k, 'parent_id' => $dept->dept_id ])->first();
                    if(!empty($check->dept_id)) {
                        $check->update($child);
                    } else {
                        Dept::create($child);
                    }
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            trans('app.添加失败', ['value' => trans('app.部门')], 'danger');
            return redirect($this->redirectTo);
        }

        DB::commit();

        flash(trans('app.编辑成功', ['value' => trans('app.部门')]), 'success');
        return redirect($this->redirectTo);
    }

    public function getChild(Request $request)
    {
        $id = $request->get('id');
        $data = [];

        $dept = Dept::where(['dept_id' => $id])->first();

        if(empty($dept->dept_id)) return response()->json(['data' => $data]);

        $child = Dept::where(['parent_id' => $dept->dept_id])->get()->toArray();

        if(!empty($child)) {
            foreach ($child as $k => $v) {
                $data[$k]['dept'] = $v['dept'];
                $data[$k]['created_at'] = $v['created_at'];
            }
        }

        return response()->json(['data' => $data, 'title' => $dept->dept]);
    }

    public function del($id)
    {
        $dept = Dept::with('users', 'entry')->where(['dept_id' => $id])->first()->toArray();

        if(empty($dept['dept_id'])) {
            flash('删除失败,无效的数据ID!', 'danger');
            return redirect($this->redirectTo);
        }

        if(!empty($dept['users']) || !empty($dept['entry'])) {
            flash('删除失败,['. $dept['dept']. ']还有在使用中!', 'danger');
            return redirect($this->redirectTo);
        }

        $child = Dept::with('users', 'entry')->where(['parent_id' => $id])->first();

        if(!empty($child['users']) && !empty($child['entry']) ) {
            flash('删除失败,['. $child['dept'] . ']还有在使用中!', 'danger');
            return redirect($this->redirectTo);
        }

        DB::beginTransaction();
        try{
            Dept::where(['dept_id' => $dept['dept_id']])->orWhere(['parent_id' => $dept['dept_id']])->delete();
        } catch (\Exception $ex) {
            DB::rollBack();
            flash('删除失败!', 'danger');
            return redirect($this->redirectTo);
        }
        DB::commit();

        flash(trans('app.删除成功', ['value' => trans('app.部门')]), 'success');
        return redirect($this->redirectTo);
    }

}