<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/18
 * Time: 16:47
 * 上下班时间规则列表控制
 */

namespace App\Http\Controllers\Admin\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\PunchRules;
use App\Models\Sys\PunchRulesConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PunchRulesController extends Controller
{

    protected $redirectTo = '/admin/sys/punch-rules';

    private $_validateRule = [
        'name' => 'required|unique:punch_rules,name|max:32',
    ];

    public function index()
    {
        $data = PunchRules::paginate();
        $title = trans('app.上下班时间规则列表');
        return view('admin.sys.punch-rules', compact('title', 'data'));
    }

    public function create()
    {
        $key = 0;
        $title = trans('app.添加上下班时间规则');
        return view('admin.sys.punch-rules-create', compact('title', 'key'));
    }

    public function edit($id)
    {
        $copyDiv = [];
        $punchRules = PunchRules::with('config')->findOrFail($id)->toArray();

        $arr = [];
        foreach ($punchRules['config'] as $k => $v) {
            $key = $v['ready_time'] . $v['work_start_time'] . $v['work_end_time'];
            $copyDiv[] = $key;
            $arr[$key]['ready_time'] = $v['ready_time'];
            $arr[$key]['work_start_time'] = $v['work_start_time'];
            $arr[$key]['work_end_time'] = $v['work_end_time'];
            $arr[$key]['cfg'][] = [
                "rule_desc" => $v['rule_desc'],
                "late_type" => $v['late_type'],
                "start_gap" => $v['start_gap'],
                "end_gap" => $v['end_gap'],
                "ded_type" => $v['ded_type'],
                "holiday_id" => $v['holiday_id'],
                "ded_num" => $v['ded_num'],
            ];
        }
        $punchRules['config'] = $arr;
        $div = $copyDiv[0];
        $title = trans('app.编辑', ['value' => trans('app.上下班时间规则')]);

        return view('admin.sys.punch-rules-edit', compact('title', 'punchRules', 'div'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);
        $p = $request->all();

        try {
            DB::beginTransaction();
            $punchRules = [
                'punch_type_id' => $p['punch_type_id'],
                'name' => $p['name'],
            ];
            $rules = PunchRules::create($punchRules);
            foreach ($p['work'] as $k => $v) {
                if($v['cfg']) {
                    foreach ($v['cfg'] as $ck => $cv) {
                        $data = [
                            'punch_rules_id' => $rules->id,
                            'work_start_time' => $v['work_start_time'],
                            'work_end_time' => $v['work_end_time'],
                            'ready_time' => $v['ready_time'],
                            'rule_desc' => $cv['rule_desc'],
                            'late_type' => $cv['late_type'],
                            'start_gap' => $cv['start_gap'],
                            'end_gap' => $cv['end_gap'],
                            'ded_type' => $cv['ded_type'][$ck] ?? 1,
                            'holiday_id' => $cv['holiday_id'],
                            'ded_num' => $cv['ded_num'],
                        ];
                        PunchRulesConfig::create($data);
                    }
                }
            }
       }catch (\Exception $ex){
            DB::rollBack();
            flash(trans('app.添加失败', ['value' => trans('app.上下班时间规则')]), 'danger');
            return redirect($this->redirectTo);
        }

        DB::commit();
        flash(trans('app.添加成功', ['value' => trans('app.上下班时间规则')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $punchRules = PunchRules::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'name' => 'required|max:32|unique:punch_rules,name,' . $punchRules->id .',id',
        ]));
        $p = $request->all();
        $rules = [
            'punch_type_id' => $p['punch_type_id'],
            'name' => $p['name'],
        ];
        try{
            $punchRules->update($rules);
            PunchRulesConfig::where(['punch_rules_id' => $punchRules->id])->delete();
            foreach ($p['work'] as $k => $v) {
                if($v['cfg']) {
                    foreach ($v['cfg'] as $ck => $cv) {
                        $data = [
                            'punch_rules_id' => $punchRules->id,
                            'work_start_time' => $v['work_start_time'],
                            'work_end_time' => $v['work_end_time'],
                            'ready_time' => $v['ready_time'],
                            'rule_desc' => $cv['rule_desc'],
                            'late_type' => $cv['late_type'],
                            'start_gap' => $cv['start_gap'],
                            'end_gap' => $cv['end_gap'],
                            'ded_type' => $cv['ded_type'][$ck] ?? 1,
                            'holiday_id' => $cv['holiday_id'],
                            'ded_num' => $cv['ded_num'],
                        ];
                        PunchRulesConfig::create($data);
                    }
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            flash(trans('app.编辑失败', ['value' => trans('app.上下班时间规则')]), 'danger');
            return redirect($this->redirectTo);
        }

        DB::commit();
        flash(trans('app.编辑成功', ['value' => trans('app.上下班时间规则')]), 'success');
        return redirect($this->redirectTo);
    }
}