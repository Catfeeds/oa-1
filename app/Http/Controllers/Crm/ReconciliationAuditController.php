<?php

namespace App\Http\Controllers\Crm;

use App\Http\Components\Helpers\CrmHelper;
use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Difference;
use App\Models\Crm\EditLog;
use App\Models\Crm\Principal;
use App\Models\Crm\Product;
use App\Models\Crm\Proportion;
use App\Models\Crm\Reconciliation;
use App\Http\Components\ScopeCrm\Reconciliation as Scope;
use App\User;
use Illuminate\Http\Request;
use App\Http\Components\Helpers\QywxHelper;

class ReconciliationAuditController extends Controller
{
    protected $scopeClass = Scope::class;

    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'crm.reconciliation-audit.scope';
        $post = Principal::where(['principal_id' => \Auth::user()->user_id])->get(['product_id', 'job_id'])->toArray();
        $limitProduct = [0];
        $limitPost = $job = [];
        foreach ($post as $v) {
            $limitProduct[] = $v['product_id'];
            $limitPost[] = $v['job_id'];
            switch (true) {
                case in_array($v['job_id'], [1, 2]):
                    $job[$v['product_id']][1] = 1;
                    break;
                case in_array($v['job_id'], [3, 4]):
                    $job[$v['product_id']][1] = 1;
                    $job[$v['product_id']][2] = 2;
                    break;
                case in_array($v['job_id'], [5, 6]):
                    $job[$v['product_id']][1] = 1;
                    $job[$v['product_id']][2] = 2;
                    $job[$v['product_id']][3] = 3;
                    $job[$v['product_id']][4] = 4;
                    break;
            }
        }
        $products = Product::getList($limitProduct);
        $pid = \Request::get('product_id', key($products));

        if (!in_array($pid, array_keys($products))) {
            return redirect()->back()->withInput();
        }

        $review = array_intersect_key(Reconciliation::REVIEW, $job[$pid]);
        $source = \Request::get('source', key($review));

        if (!in_array($source, array_keys($review))) {
            return redirect()->back()->withInput();
        }
        switch (true) {
            case $source == Reconciliation::OPERATION:
                $header = ['结算周期开始', '结算周期结束', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '后台流水（对账币）', '后台流水（人民币）',
                    '渠道费率', '一级分成比例', '二级分成比例', '二级分成条件', '运营调整', '运营调整类型', '运营调整备注', '运营调整人', '调整时间', '运营流水（对账币）', '运营流水（人民币）', '运营流水（对账币-费率分成）', '运营流水（人民币-费率分成）', '操作'];
                break;
            case $source == Reconciliation::ACCRUAL:
                $header = ['结算周期开始', '结算周期结束', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '运营流水（对账币）', '运营流水（人民币）',
                    '渠道费率', '一级分成比例', '二级分成比例', '二级分成条件', '对账调整', '对账调整类型', '对账调整备注', '对账调整人', '调整时间', '对账流水（对账币）', '对账流水（人民币）', '运营流水（对账币-费率分成）', '运营流水（人民币-费率分成）', '操作'];
                break;
            case $source == Reconciliation::RECONCILIATION:
                $header = ['结算周期开始', '结算周期结束', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '一级分成类型', '诗悦后台渠道', '统一渠道名称', '对账流水（对账币）', '对账流水（人民币）',
                    '渠道费率', '一级分成比例', '二级分成比例', '二级分成条件', '计提调整', '计提调整类型', '计提调整备注', '计提调整人', '调整时间', '计提流水（对账币）', '计提流水（人民币）', '运营流水（对账币-费率分成）', '运营流水（人民币-费率分成）', '操作'];
                break;
            default:
                $header = ['结算周期开始', '结算周期结束', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '渠道费率', '一级分成比例', '二级分成比例', '二级分成条件', '后台流水（对账币）', '后台流水（人民币）',
                    '后台流水（对账币-费率分成）', '后台流水（人民币-费率分成）', '运营调整', '运营调整类型', '运营调整备注', '运营调整人', '调整时间',
                    '运营流水（对账币）', '运营流水（人民币）', '运营流水（对账币-费率分成）', '运营流水（人民币-费率分成）', '对账调整', '对账调整类型', '对账调整备注', '对账调整人', '调整时间',
                    '对账流水（对账币）', '对账流水（人民币）', '计提流水（对账币-费率分成）', '计提流水（人民币-费率分成）', '计提调整', '计提调整类型', '计提调整备注', '计提调整人', '调整时间',
                    '计提流水（对账币）', '计提流水（人民币）', '计提流水（对账币-费率分成）', '计提流水（人民币-费率分成）'];
        }

        $data = Reconciliation::where(['product_id' => $pid])
            ->whereBetween('billing_cycle_start', [$scope->startTimestamp, $scope->endTimestamp])
            ->orderBy('id', 'desc')
            ->get()->toArray();
        $status = 1;
        foreach ($data as $k => $v) {
            $proportion = Proportion::where(['product_id' => $pid, 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel'], 'billing_cycle' => $v['billing_cycle_end']])->first();
            $data[$k]['channel_rate'] = $proportion->channel_rate ? $proportion->channel_rate : 0;
            $data[$k]['first_division'] = $proportion->first_division ? $proportion->first_division : 0;
            $data[$k]['second_division'] = $proportion->second_division ? $proportion->second_division : 0;
            $data[$k]['second_division_condition'] = $proportion->second_division_condition ? $proportion->second_division_condition : 0;
            $status = $data[$k]['review_type'];
        }
        $title = trans('crm.对账审核');

        return view('crm.reconciliation-audit.index', compact('title', 'scope', 'review', 'source', 'products', 'pid', 'header', 'data', 'status', 'limitPost'));
    }

    public function edit($id, $source)
    {
        $tmp = Reconciliation::findOrFail($id);
        $data = $this->transformName($source, $tmp, '', '');
        $type = CrmHelper::addEmptyToArray('差异类型', Difference::getList($tmp['product_id']));
        $title = trans('app.编辑', ['value' => trans('crm.对账审核')]);
        return view('crm.reconciliation-audit.edit', compact('title', 'data', 'type'));
    }

    public function update(Request $request, $id, $source)
    {
        $time = date('Y-m-d H:i:s', time());
        $data = Reconciliation::findOrFail($id);
        $tmp = $this->transformName($source, $data, $request, $time);
        $data->update($tmp);
        EditLog::create(array_merge($request->all(), ['user_name' => \Auth::user()->alias, 'time' => $time,
            'billing_cycle_start' => $data['billing_cycle_start'], 'billing_cycle_end' => $data['billing_cycle_end'], 'client' => $data['client'],
            'backstage_channel' => $data['backstage_channel'], 'product_id' => $data['product_id']]));

        flash(trans('app.编辑成功', ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->route('reconciliationAudit', ['source' => $source, 'product_id' => $data['product_id']]);
    }

    public function transformName($source, $data, $request, $time)
    {
        if ($time) {
            switch ($source) {
                case Reconciliation::OPERATION:
                    $tmp['operation_adjustment'] = $request['adjustment'];
                    $tmp['operation_type'] = $request['type'];
                    $tmp['operation_remark'] = $request['remark'];
                    $tmp['operation_user_name'] = \Auth::user()->alias;
                    $tmp['operation_time'] = $time;
                    $tmp['operation_water_other'] = $data['backstage_water_other'] + $request['adjustment'];
                    $tmp['operation_water_rmb'] = $data['backstage_water_rmb'] + $request['adjustment'];
                    break;
                case Reconciliation::ACCRUAL:
                    $tmp['accrual_adjustment'] = $request['adjustment'];
                    $tmp['accrual_type'] = $request['type'];
                    $tmp['accrual_remark'] = $request['remark'];
                    $tmp['accrual_user_name'] = \Auth::user()->alias;
                    $tmp['accrual_time'] = $time;
                    $tmp['accrual_water_other'] = $data['operation_water_other'] + $request['adjustment'];
                    $tmp['accrual_water_rmb'] = $data['operation_water_rmb'] + $request['adjustment'];
                    break;
                case Reconciliation::RECONCILIATION:
                    $tmp['reconciliation_adjustment'] = $request['adjustment'];
                    $tmp['reconciliation_type'] = $request['type'];
                    $tmp['reconciliation_remark'] = $request['remark'];
                    $tmp['reconciliation_user_name'] = \Auth::user()->alias;
                    $tmp['reconciliation_time'] = $time;
                    $tmp['reconciliation_water_other'] = $data['accrual_water_other'] + $request['adjustment'];
                    $tmp['reconciliation_water_rmb'] = $data['accrual_water_rmb'] + $request['adjustment'];
                    break;
            }
        } else {
            switch ($source) {
                case Reconciliation::OPERATION:
                    $tmp['adjustment'] = $data['operation_adjustment'];
                    $tmp['type'] = $data['operation_type'];
                    $tmp['remark'] = $data['operation_remark'];
                    break;
                case Reconciliation::ACCRUAL:
                    $tmp['adjustment'] = $data['accrual_adjustment'];
                    $tmp['type'] = $data['accrual_type'];
                    $tmp['remark'] = $data['accrual_remark'];
                    break;
                case Reconciliation::RECONCILIATION:
                    $tmp['adjustment'] = $data['reconciliation_adjustment'];
                    $tmp['type'] = $data['reconciliation_type'];
                    $tmp['remark'] = $data['reconciliation_remark'];
                    break;
            }
        }

        return $tmp;
    }

    public function review($status)
    {
        switch ($status) {
            case 1:
            case 2:
                $type = 1;
                break;
            case 3:
                $type = -1;
                break;
        }
        $scope = $this->scope;
        $pid = \Request::get('pid');
        $source = \Request::get('source');
        $data = Reconciliation::where(['product_id' => $pid])
            ->whereBetween('billing_cycle_start', [$scope->startTimestamp, $scope->endTimestamp])
            ->get();
        foreach ($data as $v) {
            $review = $v['review_type'] + $type;
            if ($review < 0) {
                $review = $v['review_type'];
            }
            $v->update(['review_type' => $review]);

        }
        $this->push($pid, $source, $review);
        flash(trans('app.审核', ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->route('reconciliationAudit', ['source' => $source, 'product_id' => $pid]);
    }

    public function push($pid, $source, $review)
    {
        $scope = $this->scope;
        $job = Principal::where(['product_id' => $pid])->get(['job_id', 'principal_id'])->pluck('principal_id', 'job_id')->toArray();
        $user = User::get(['user_id', 'username'])->pluck('username', 'user_id')->toArray();
        $product = Product::getList();
        switch ($source) {
            case Reconciliation::OPERATION:
                switch ($review) {
                    case Reconciliation::UNRD:
                        $key = Principal::OPS;
                        $message = '被拒绝';
                        break;
                    case Reconciliation::OPS:
                        $key = Principal::OPD;
                        $message = '提交审核';
                        break;
                    case Reconciliation::OPD:
                        $key = Principal::FAC;
                        $message = '通过审核';
                        break;
                }
                break;
            case Reconciliation::ACCRUAL:
                switch ($review) {
                    case Reconciliation::OPS:
                        $key = Principal::OPD;
                        $message = '被拒绝';
                        break;
                    case Reconciliation::OPD:
                        $key = Principal::FAC;
                        $message = '被拒绝';
                        break;
                    case Reconciliation::FAC:
                        $key = Principal::TREASURER;
                        $message = '提交审核';
                        break;
                    case Reconciliation::TREASURER:
                        $key = Principal::FRC;
                        $message = '通过审核';
                        break;
                }
                break;
            case Reconciliation::RECONCILIATION:
                switch ($review) {
                    case Reconciliation::FAC:
                        $key = Principal::TREASURER;
                        $message = '被拒绝';
                        break;
                    case Reconciliation::TREASURER:
                        $key = Principal::FRC;
                        $message = '被拒绝';
                        break;
                    case Reconciliation::FRC:
                        $key = Principal::FSR;
                        $message = '提交审核';
                        break;
                }
                break;
        }
        $username = isset($user[$job[$key]]) ? $user[$job[$key]] : 'sy0256';
        QywxHelper::push($username, sprintf('你好！%s,%s%s月的流水审计已%s，请及时处理:%s', $username, $product[$pid],
            date('m', strtotime($scope->startTimestamp)), $message, route('reconciliationAudit',
                ['source' => $source, 'product_id' => $pid])), time());
    }

    public function download()
    {
        $scope = $this->scope;
        $pid = \Request::get('pid');
        $header = ['结算周期开始', '结算周期结束', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
            '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '渠道费率', '一级分成比例', '二级分成比例', '二级分成条件', '后台流水（对账币）', '后台流水（人民币）',
            '后台流水（对账币-费率分成）', '后台流水（人民币-费率分成）', '运营调整', '运营调整类型', '运营调整备注', '运营调整人', '调整时间',
            '运营流水（对账币）', '运营流水（人民币）', '运营流水（对账币-费率分成）', '运营流水（人民币-费率分成）', '对账调整', '对账调整类型', '对账调整备注', '对账调整人', '调整时间',
            '对账流水（对账币）', '对账流水（人民币）', '计提流水（对账币-费率分成）', '计提流水（人民币-费率分成）', '计提调整', '计提调整类型', '计提调整备注', '计提调整人', '调整时间',
            '计提流水（对账币）', '计提流水（人民币）', '计提流水（对账币-费率分成）', '计提流水（人民币-费率分成）'];
        $file = storage_path(sprintf('data/export/%s.csv', date('YmdHis') . uniqid()));
        fopen($file, "w");
        $data = Reconciliation::where(['product_id' => $pid])
            ->whereBetween('billing_cycle_start', [$scope->startTimestamp, $scope->endTimestamp])
            ->orderBy('id', 'desc')
            ->get()->toArray();

        foreach ($data as $v){
            $proportion = Proportion::where(['product_id' => $pid, 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel'], 'billing_cycle' => $v['billing_cycle_end']])->first();
            $tmp = [
                $v['billing_cycle_start'],
                $v['billing_cycle_end'],
                $v['income_type'],
                $v['company'],
                $v['client'],
                $v['game_name'],
                $v['online_name'],
                $v['business_line'],
                $v['area'],
                $v['reconciliation_currency'],
                $v['os'],
                $v['divided_type'],
                $v['backstage_channel'],
                $v['unified_channel'],
                CrmHelper::percentage($proportion->channel_rate),
                CrmHelper::percentage($proportion->first_division),
                CrmHelper::percentage($proportion->second_division),
                $proportion->second_division_condition,
                $v['backstage_water_other'],
                $v['backstage_water_rmb'],
                CrmHelper::dividedInto($proportion->channel_rate,$proportion->first_division,$proportion->second_division,$proportion->second_division_condition,$v['backstage_water_other']),
                CrmHelper::dividedInto($proportion->channel_rate,$proportion->first_division,$proportion->second_division,$proportion->second_division_condition,$v['backstage_water_rmb']),
                $v['operation_adjustment'],
                $v['operation_type'],
                $v['operation_remark'],
                $v['operation_user_name'],
                $v['operation_time'],
                $v['operation_water_other'],
                $v['operation_water_rmb'],
                CrmHelper::dividedInto($proportion->channel_rate,$proportion->first_division,$proportion->second_division,$proportion->second_division_condition,$v['operation_water_other']),
                CrmHelper::dividedInto($proportion->channel_rate,$proportion->first_division,$proportion->second_division,$proportion->second_division_condition,$v['operation_water_rmb']),
                $v['accrual_adjustment'],
                $v['accrual_type'],
                $v['accrual_remark'],
                $v['accrual_user_name'],
                $v['accrual_time'],
                $v['accrual_water_other'],
                $v['accrual_water_rmb'],
                CrmHelper::dividedInto($proportion->channel_rate,$proportion->first_division,$proportion->second_division,$proportion->second_division_condition,$v['accrual_water_other']),
                CrmHelper::dividedInto($proportion->channel_rate,$proportion->first_division,$proportion->second_division,$proportion->second_division_condition,$v['accrual_water_rmb']),
                $v['reconciliation_adjustment'],
                $v['reconciliation_type'],
                $v['reconciliation_remark'],
                $v['reconciliation_user_name'],
                $v['reconciliation_time'],
                $v['reconciliation_water_other'],
                $v['reconciliation_water_rmb'],
                CrmHelper::dividedInto($proportion->channel_rate,$proportion->first_division,$proportion->second_division,$proportion->second_division_condition,$v['reconciliation_water_other']),
                CrmHelper::dividedInto($proportion->channel_rate,$proportion->first_division,$proportion->second_division,$proportion->second_division_condition,$v['reconciliation_water_rmb']),
            ];
            $this->file_prepend(implode("\t", $tmp), $file);
        }

        $this->file_prepend(implode("\t", $header), $file);
        return response()->download($file)->deleteFileAfterSend(true);
    }

    public function file_prepend($string, $filename)
    {
        $fileContent = file_get_contents($filename);
        file_put_contents($filename, $string . "\n" . $fileContent);
    }
}
