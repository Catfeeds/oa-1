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
use DB;

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
                    $job[$v['product_id']][2] = 2;
                    break;
                case in_array($v['job_id'], [5, 6]):
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

        $columns = $this->header($source);

        if (!in_array($source, array_keys($review))) {
            return redirect()->back()->withInput();
        }
        $data = Reconciliation::where(['product_id' => $pid, 'billing_cycle' => date('Y-m', strtotime($scope->startTimestamp))])->get(['review_type'])->keyBy('review_type')->toArray();

        if (!empty($data)) {
            $status = array_keys($data)[0];
        }

        $title = trans('crm.对账审核');

        return view('crm.reconciliation-audit.index', compact('title', 'scope', 'review', 'source', 'products', 'pid', 'header', 'status', 'limitPost', 'columns'));
    }

    public function data()
    {
        $permission = \Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.edit']);
        $pid = \Request::get('product_id');
        $source = \Request::get('source');
        $scope = $this->scope;
        $billing_cycle = date('Y-m', strtotime($scope->startTimestamp));
        $sql = "
            SELECT 
                a.id,
                a.billing_cycle,
                a.product_id,
                a.billing_cycle_start,
                a.income_type,
                a.billing_cycle_end,
                a.company,
                a.client,
                a.game_name,
                a.online_name,
                a.business_line,
                a.area,
                a.reconciliation_currency,
                a.os,
                a.divided_type,
                a.backstage_channel,
                a.unified_channel,
                a.review_type,
                a.backstage_water_other,
                a.backstage_water_rmb,
                a.operation_adjustment,
                a.operation_type,
                a.operation_remark,
                a.operation_user_name,
                a.operation_time,
                a.operation_water_other,
                a.operation_water_rmb,
                a.accrual_adjustment,
                a.accrual_type,
                a.accrual_remark,
                a.accrual_user_name,
                a.accrual_time,
                a.accrual_water_other,
                a.accrual_water_rmb,
                a.reconciliation_adjustment,
                a.reconciliation_type,
                a.reconciliation_remark,
                a.reconciliation_user_name,
                a.reconciliation_time,
                a.reconciliation_water_other,
                a.reconciliation_water_rmb,
                p.channel_rate,
                p.first_division,
                p.second_division,
                p.second_division_condition
            FROM cmr_reconciliation AS a 
            INNER JOIN cmr_reconciliation_proportion AS p ON (
                a.product_id = p.product_id
                AND a.id = p.rec_id
            )
            WHERE a.product_id = {$pid} AND a.billing_cycle = '{$billing_cycle}'
        ";
        $tmp = \DB::select($sql);
        $data = $tmp3 = $tmp2 = [];
        $water = [
            'backstage_water_other',
            'backstage_water_rmb',
            'backstage_divide_other',
            'backstage_divide_rmb',
            'operation_water_other',
            'operation_water_rmb',
            'operation_divide_other',
            'operation_divide_rmb',
            'accrual_water_other',
            'accrual_water_rmb',
            'accrual_divide_other',
            'accrual_divide_rmb',
            'reconciliation_water_other',
            'reconciliation_water_rmb',
            'reconciliation_divide_other',
            'reconciliation_divide_rmb',
        ];
        $diff = Difference::getList();
        foreach ($tmp as $k => $v) {
            $v = (array)$v;
            $tmp2['num'] = $k + 1;
            $tmp2['billing_cycle'] = $v['billing_cycle'];
            $tmp2['income_type'] = $v['income_type'];
            $tmp2['company'] = $v['company'];
            $tmp2['client'] = $v['client'];
            $tmp2['game_name'] = $v['game_name'];
            $tmp2['online_name'] = $v['online_name'];
            $tmp2['business_line'] = $v['business_line'];
            $tmp2['area'] = $v['area'];
            $tmp2['reconciliation_currency'] = $v['reconciliation_currency'];
            $tmp2['os'] = $v['os'];
            $tmp2['divided_type'] = $v['divided_type'];
            $tmp2['backstage_channel'] = $v['backstage_channel'];
            $tmp2['unified_channel'] = $v['unified_channel'];
            $url = route('reconciliationAudit.edit', ['id' => $v['id'], 'source' => $source]);
            switch (true) {
                case $source == Reconciliation::OPERATION:
                    $tmp2['backstage_water_other'] = $v['backstage_water_other'];
                    $tmp2['backstage_water_rmb'] = $v['backstage_water_rmb'];
                    $tmp2['operation_adjustment'] = $v['operation_adjustment'];
                    $tmp2['operation_type'] = $diff[$v['operation_type']] ?? '未知'.$v['operation_type'];
                    $tmp2['operation_remark'] = $v['operation_remark'];
                    $tmp2['operation_user_name'] = $v['operation_user_name'];
                    $tmp2['operation_time'] = $v['operation_time'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['review_type'] = $v['review_type'] == 1 && $permission ? '<a href="' . $url . '" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>' : '--';
                    break;
                case $source == Reconciliation::ACCRUAL:
                    $tmp2['channel_rate'] = $v['channel_rate'];
                    $tmp2['first_division'] = $v['first_division'];
                    $tmp2['second_division'] = $v['second_division'];
                    $tmp2['second_division_condition'] = $v['second_division_condition'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['accrual_adjustment'] = $v['accrual_adjustment'];
                    $tmp2['accrual_type'] = $diff[$v['accrual_type']] ?? '未知'.$v['accrual_type'];
                    $tmp2['accrual_remark'] = $v['accrual_remark'];
                    $tmp2['accrual_user_name'] = $v['accrual_user_name'];
                    $tmp2['accrual_time'] = $v['accrual_time'];
                    $tmp2['accrual_water_other'] = $v['accrual_water_other'];
                    $tmp2['accrual_water_rmb'] = $v['accrual_water_rmb'];
                    $tmp2['accrual_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['accrual_water_other']);
                    $tmp2['accrual_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['accrual_water_rmb']);
                    $tmp2['review_type'] = $v['review_type'] == 3 && $permission ? '<a href="' . $url . '" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>' : '--';
                    break;
                case $source == Reconciliation::RECONCILIATION:
                    $tmp2['channel_rate'] = $v['channel_rate'];
                    $tmp2['first_division'] = $v['first_division'];
                    $tmp2['second_division'] = $v['second_division'];
                    $tmp2['second_division_condition'] = $v['second_division_condition'];
                    $tmp2['accrual_water_other'] = $v['accrual_water_other'];
                    $tmp2['accrual_water_rmb'] = $v['accrual_water_rmb'];
                    $tmp2['reconciliation_adjustment'] = $v['reconciliation_adjustment'];
                    $tmp2['reconciliation_type'] = $diff[$v['reconciliation_type']] ?? '未知'.$v['reconciliation_type'];
                    $tmp2['reconciliation_remark'] = $v['reconciliation_remark'];
                    $tmp2['reconciliation_user_name'] = $v['reconciliation_user_name'];
                    $tmp2['reconciliation_time'] = $v['reconciliation_time'];
                    $tmp2['reconciliation_water_other'] = $v['reconciliation_water_other'];
                    $tmp2['reconciliation_water_rmb'] = $v['reconciliation_water_rmb'];
                    $tmp2['reconciliation_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['reconciliation_water_other']);
                    $tmp2['reconciliation_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['reconciliation_water_rmb']);
                    $tmp2['review_type'] = $v['review_type'] == 5 && $permission ? '<a href="' . $url . '" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>' : '--';
                    break;
                default:
                    $tmp2['channel_rate'] = $v['channel_rate'];
                    $tmp2['first_division'] = $v['first_division'];
                    $tmp2['second_division'] = $v['second_division'];
                    $tmp2['second_division_condition'] = $v['second_division_condition'];
                    $tmp2['backstage_water_other'] = $v['backstage_water_other'];
                    $tmp2['backstage_water_rmb'] = $v['backstage_water_rmb'];
                    $tmp2['backstage_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['backstage_water_other']);
                    $tmp2['backstage_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['backstage_water_rmb']);
                    $tmp2['operation_adjustment'] = $v['operation_adjustment'];
                    $tmp2['operation_type'] = $diff[$v['operation_type']] ?? '未知'.$v['operation_type'];
                    $tmp2['operation_remark'] = $v['operation_remark'];
                    $tmp2['operation_user_name'] = $v['operation_user_name'];
                    $tmp2['operation_time'] = $v['operation_time'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['operation_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['operation_water_other']);
                    $tmp2['operation_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['operation_water_rmb']);
                    $tmp2['accrual_adjustment'] = $v['accrual_adjustment'];
                    $tmp2['accrual_type'] = $diff[$v['accrual_type']] ?? '未知'.$v['accrual_type'];
                    $tmp2['accrual_remark'] = $v['accrual_remark'];
                    $tmp2['accrual_user_name'] = $v['accrual_user_name'];
                    $tmp2['accrual_time'] = $v['accrual_time'];
                    $tmp2['accrual_water_other'] = $v['accrual_water_other'];
                    $tmp2['accrual_water_rmb'] = $v['accrual_water_rmb'];
                    $tmp2['accrual_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['accrual_water_other']);
                    $tmp2['accrual_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['accrual_water_rmb']);
                    $tmp2['reconciliation_adjustment'] = $v['reconciliation_adjustment'];
                    $tmp2['reconciliation_type'] = $diff[$v['reconciliation_type']] ?? '未知'.$v['reconciliation_type'];
                    $tmp2['reconciliation_remark'] = $v['reconciliation_remark'];
                    $tmp2['reconciliation_user_name'] = $v['reconciliation_user_name'];
                    $tmp2['reconciliation_time'] = $v['reconciliation_time'];
                    $tmp2['reconciliation_water_other'] = $v['reconciliation_water_other'];
                    $tmp2['reconciliation_water_rmb'] = $v['reconciliation_water_rmb'];
                    $tmp2['reconciliation_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['reconciliation_water_other']);
                    $tmp2['reconciliation_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['reconciliation_water_rmb']);

                    break;
            }
            foreach ($tmp2 as $key => $value) {
                $tmp3['num'] = 0;
                if (in_array($key, $water)) {
                    if (!isset($tmp3[$key])) {
                        $tmp3[$key] = 0;
                    }
                    $tmp3[$key] += $value;
                } else {
                    $tmp3[$key] = '--';
                }
            }
            $data[] = array_values($tmp2);
        }
        if (!empty($tmp3)) {
            $data = array_merge([0 => array_values($tmp3)], $data);
        }
        return $this->response($data);
    }

    public function edit($id, $source)
    {
        $tmp = Reconciliation::findOrFail($id);
        $data = $this->transformName($source, $tmp, '', '');
        $type = CrmHelper::addEmptyToArray('差异类型', Difference::getList());
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
            'backstage_channel' => $data['backstage_channel'], 'product_id' => $data['product_id'], 'rec_id' => $data['id']]));

        flash(trans('app.编辑成功', ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->route('reconciliationAudit', ['source' => $source, 'product_id' => $data['product_id'], 'scope[startDate]' => $data['billing_cycle_start'], 'scope[endDate]' => $data['billing_cycle_end']]);
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
        $scope = $this->scope;
        $pid = \Request::get('pid');
        $source = \Request::get('source');
        $reason = \Request::get('reason');
        DB::beginTransaction();
        try {
            $data = Reconciliation::where(['product_id' => $pid])
                ->whereBetween('billing_cycle_start', [$scope->startTimestamp, $scope->endTimestamp])
                ->get();
            foreach ($data as $v) {
                $v->update(['review_type' => $status]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            flash(trans('crm.录入数据库失败：' . $e->getMessage()), 'danger');
            return redirect()->route('reconciliationAudit', ['source' => $source, 'product_id' => $pid, 'scope[startDate]' => $scope->startTimestamp, 'scope[endDate]' => $scope->endTimestamp]);
        }

        $this->push($pid, $source, $status, $reason);
        flash(trans('crm.审核', ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->route('reconciliationAudit', ['source' => $source, 'product_id' => $pid, 'scope[startDate]' => $scope->startTimestamp, 'scope[endDate]' => $scope->endTimestamp]);
    }

    public function push($pid, $source, $review, $reason)
    {
        $scope = $this->scope;
        $job = Principal::where(['product_id' => $pid])->get(['job_id', 'principal_id'])->pluck('principal_id', 'job_id')->toArray();
        $user = User::get(['user_id', 'username'])->pluck('username', 'user_id')->toArray();
        $product = Product::getList();
        $message = $water = '';
        switch ($source) {
            case Reconciliation::OPERATION:
                $water = '运营流水';
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
                $water = '计提流水';
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
                $water = '对账流水';
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
                    case Reconciliation::FSR:
                        $key = Principal::FRC;
                        $message = '通过审核';
                        break;
                }
                break;
            case Reconciliation::ALL:
                $water = '对账流水';
                switch ($review) {
                    case Reconciliation::FRC:
                        $key = Principal::FSR;
                        $message = '返结账';
                        break;
                }
                break;
        }
        if (!empty($reason)) {
            $reason = sprintf('，拒绝原因备注：%s', $reason);
        }
        try {
            $username = $user[$job[$key]];
            QywxHelper::push($username, sprintf('你好！%s,%s%s月的%s审计已%s%s，请及时处理:%s', $username, $product[$pid],
                date('m', strtotime($scope->startTimestamp)), $water, $message, $reason, route('reconciliationAudit',
                    ['source' => $source, 'product_id' => $pid])), time());
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

    }

    public function header($source)
    {
        switch (true) {
            case $source == Reconciliation::OPERATION:
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '对账币', '人民币',
                    '运营调整', '运营调整类型', '运营调整备注', '运营调整人', '调整时间', '对账币', '人民币', '操作'];
                break;
            case $source == Reconciliation::ACCRUAL:
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '渠道费率', '一级分成', '二级分成', '二级分成条件', '对账币', '人民币',
                    '调整', '类型', '备注', '调整人', '调整时间', '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '操作'];
                break;
            case $source == Reconciliation::RECONCILIATION:
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '渠道费率', '一级分成', '二级分成', '二级分成条件', '对账币', '人民币',
                    '调整', '类型', '备注', '调整人', '调整时间', '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '操作'];
                break;
            default:
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '渠道费率', '一级分成', '二级分成', '二级分成条件', '对账币', '人民币',
                    '对账币-费率分成', '人民币-费率分成', '调整', '类型', '备注', '调整人', '调整时间',
                    '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '调整', '类型', '备注', '调整人', '调整时间',
                    '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '调整', '类型', '备注', '调整人', '调整时间',
                    '对账币', '人民币', '对账币-费率分成', '人民币-费率分成'];
        }

        return $header;
    }
}
