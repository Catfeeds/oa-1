<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/10/30
 * Time: 17:25
 */

namespace App\Http\Controllers\Admin\Sys;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Sys\ApprovalStep;
use App\Models\Sys\Dept;
use App\Models\Sys\HolidayConfig;
use App\Models\Sys\ReviewStepFlow;
use App\Models\Sys\ReviewStepFlowConfig;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewStepFlowController extends Controller
{
    protected $redirectTo = '/admin/sys/review-step-flow';

    private $_validateRule = [
        'step' => 'required',
        'dept_id' => 'required|numeric',
        'time_range_id' => 'required|numeric',
    ];

    public function index()
    {
        $data = ReviewStepFlow::paginate();
        $title = trans('app.审核流程配置列表');
        return view('admin.sys.review-step-flow', compact('title', 'data'));
    }

    public function create()
    {
        $roleList = Role::getRoleTextList();
        $userList = User::getUsernameAliasList();
        $title = trans('app.添加审核流程配置');
        return view('admin.sys.review-step-flow-create', compact('title', 'roleList', 'userList'));
    }

    public function edit($id)
    {
        $roleList = Role::getRoleTextList();
        $step = ReviewStepFlow::with('config')->findOrFail($id);
        $i = count($step->config);
        $userList = User::getUsernameAliasList();

        $title = trans('app.编辑', ['value' => trans('app.审核流程配置')]);
        return view('admin.sys.review-step-flow-edit', compact('title', 'step', 'roleList', 'userList', 'i'));
    }

    public function store(Request $request)
    {
        //$this->validate($request, $this->_validateRule);
        $p = $request->all();

        $data = [
            'apply_type_id' => $p['apply_type_id'],
            'child_id' => $p['child_id'] ?? NULL,
            'min_num' => $p['min_num'] ?? NULL,
            'max_num' => $p['max_num'] ?? NULL,
            'is_modify' => $p['is_modify'],
        ];

        DB::beginTransaction();
        try{
            $step = ReviewStepFlow::create($data);
            if(!empty($p['step'])) {
                foreach ($p['step'] as $k => $v) {

                    $config = [
                        'step_order_id' => $v['step_order_id'],
                        'step_id' => $step->step_id,
                    ];

                    $check = ReviewStepFlowConfig::where($config)->first();
                    if (!empty($check)) continue;

                    if((int)$v['assign_type'] === 0) {
                        $config['assign_type'] = $v['assign_type'];
                        $config['assign_uid'] = $v['assign_uid'];
                    }

                    if((int)$v['assign_type'] === 1) {
                        $config['assign_type'] = $v['assign_type'];
                        $config['group_type_id'] = $v['group_type_id'];
                        $config['assign_role_id'] = $v['assign_role_id'];
                    }
                    ReviewStepFlowConfig::create($config);
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            flash(trans('app.添加失败', ['value' => trans('app.审核流程配置')]), 'danger');
            return redirect($this->redirectTo);
        }

        DB::commit();
        flash(trans('app.添加成功', ['value' => trans('app.审核流程配置')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $p = $request->all();
        $step = ReviewStepFlow::findOrFail($id);
        $data = [
            'apply_type_id' => $p['apply_type_id'],
            'child_id' => $p['child_id'] ?? NULL,
            'min_num' => $p['min_num'] ?? NULL,
            'max_num' => $p['max_num'] ?? NULL,
            'is_modify' => $p['is_modify'],
        ];

        DB::beginTransaction();
        try{
            $step->update($data);
            ReviewStepFlowConfig::where(['step_id' => $step->step_id])->delete();
            if(!empty($p['step'])) {
                foreach ($p['step'] as $k => $v) {
                    $config = [
                        'step_order_id' => $v['step_order_id'],
                        'step_id' => $step->step_id,
                    ];
                    $check = ReviewStepFlowConfig::where($config)->first();
                    if (!empty($check->step_id)) continue;

                    if((int)$v['assign_type'] === 0) {
                        $config['assign_type'] = $v['assign_type'];
                        $config['assign_uid'] = $v['assign_uid'];
                    }

                    if((int)$v['assign_type'] === 1) {
                        $config['assign_type'] = $v['assign_type'];
                        $config['group_type_id'] = $v['group_type_id'];
                        $config['assign_role_id'] = $v['assign_role_id'];
                    }
                    ReviewStepFlowConfig::create($config);
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            flash(trans('app.编辑失败', ['value' => trans('app.审核流程配置')]), 'danger');
            return redirect($this->redirectTo);
        }

        DB::commit();


        flash(trans('app.编辑成功', ['value' => trans('app.审核流程配置')]), 'success');
        return redirect($this->redirectTo);
    }

    public function getHoliday(Request $request)
    {
        $p = $request->all();

        $holiday = HolidayConfig::where(['apply_type_id' => $p['id']])->get()->toArray();

        $res = [];
        if(!empty($holiday)) {
            foreach ($holiday as $k => $v) {
                $res[] = ['id' => $v['holiday_id'], 'text' => $v['holiday']];
            }
        }

        return response()->json(['status' => 1, 'data' => $res]);
    }

}