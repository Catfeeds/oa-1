<?php

namespace App\Http\Controllers\Crm;

use App\Http\Components\Helpers\CrmHelper;
use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Difference;
use App\Models\Crm\EditLog;
use App\Models\Crm\ExchangeRate;
use App\Models\Crm\Principal;
use App\Models\Crm\Product;
use App\Models\Crm\Proportion;
use App\Models\Crm\Reconciliation;
use App\Http\Components\ScopeCrm\Reconciliation as Scope;
use App\User;
use Illuminate\Http\Request;
use App\Http\Components\Helpers\OperateLogHelper;
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
                    $job[$v['product_id']][3] = 3;
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
                a.period_name,
                a.period,                
                a.billing_type,
                a.billing_num,
                a.billing_time,
                a.billing_user,
                a.payback_type,
                a.payback_time,
                a.payback_user,                
                a.review_type,
                a.backstage_water_other,
                a.backstage_water_rmb,
                a.operation_adjustment,
                a.operation_rmb_adjustment,
                a.operation_type,
                a.operation_remark,
                a.operation_user_name,
                a.operation_time,
                a.operation_water_other,
                a.operation_water_rmb,
                a.accrual_adjustment,
                a.accrual_rmb_adjustment,
                a.accrual_type,
                a.accrual_remark,
                a.accrual_user_name,
                a.accrual_time,
                a.accrual_water_other,
                a.accrual_water_rmb,
                a.reconciliation_adjustment,
                a.reconciliation_rmb_adjustment,
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
            if ($source == Reconciliation::RECONCILIATION) {
                $tmp2['id'] = $v['payback_type'] == 1 ? '<input type="checkbox" class="i-checks" name="id[]" value="' . $v['id'] . '">' : '--';
            }
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
            $tmp2['period_name'] = $v['period_name'];
            $tmp2['period'] = $v['period'];
            $url['edit'] = route('reconciliationAudit.edit', ['id' => $v['id'], 'source' => $source]);
            $url['review'] = route('reconciliationAudit.review', ['status' => $v['review_type'] + 1, 'pid' => $pid, 'source' => $source, 'id' => $v['id']]);
            $url['refuse'] = route('reconciliationAudit.review', ['status' => $v['review_type'] - 1, 'pid' => $pid, 'source' => $source, 'id' => $v['id']]);
            switch (true) {
                case $source == Reconciliation::OPERATION:
                    $tmp2['backstage_water_other'] = $v['backstage_water_other'];
                    $tmp2['backstage_water_rmb'] = $v['backstage_water_rmb'];
                    $tmp2['operation_adjustment'] = $v['operation_adjustment'];
                    $tmp2['operation_rmb_adjustment'] = $v['operation_rmb_adjustment'];
                    $tmp2['operation_type'] = $diff[$v['operation_type']] ?? '未知' . $v['operation_type'];
                    $tmp2['operation_remark'] = $v['operation_remark'];
                    $tmp2['operation_user_name'] = $v['operation_user_name'];
                    $tmp2['operation_time'] = $v['operation_time'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['review_type'] = $this->url($url, $v);
                    break;
                case $source == Reconciliation::ACCRUAL:
                    $tmp2['channel_rate'] = CrmHelper::percentage($v['channel_rate']);
                    $tmp2['first_division'] = CrmHelper::percentage($v['first_division']);
                    $tmp2['second_division'] = CrmHelper::percentage($v['second_division']);
                    $tmp2['second_division_condition'] = $v['second_division_condition'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['accrual_adjustment'] = $v['accrual_adjustment'];
                    $tmp2['accrual_rmb_adjustment'] = $v['accrual_rmb_adjustment'];
                    $tmp2['accrual_type'] = $diff[$v['accrual_type']] ?? '未知' . $v['accrual_type'];
                    $tmp2['accrual_remark'] = $v['accrual_remark'];
                    $tmp2['accrual_user_name'] = $v['accrual_user_name'];
                    $tmp2['accrual_time'] = $v['accrual_time'];
                    $tmp2['accrual_water_other'] = $v['accrual_water_other'];
                    $tmp2['accrual_water_rmb'] = $v['accrual_water_rmb'];
                    $tmp2['accrual_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['accrual_water_other']);
                    $tmp2['accrual_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['accrual_water_rmb']);
                    $tmp2['review_type'] = $this->url($url, $v);
                    break;
                case $source == Reconciliation::RECONCILIATION:
                    $tmp2['billing_type'] = $v['billing_type'] == Reconciliation::NO ? sprintf('<span style="color: red">%s</span>', Reconciliation::INVOICE[Reconciliation::NO]) : sprintf('<span style="color: green">%s</span>', Reconciliation::INVOICE[Reconciliation::YES]);
                    $tmp2['billing_num'] = $v['billing_num'];
                    $tmp2['billing_time'] = $v['billing_time'];
                    $tmp2['billing_user'] = $v['billing_user'];
                    $tmp2['payback_type'] = $v['payback_type'] == Reconciliation::NO ? sprintf('<span style="color: red">%s</span>', Reconciliation::PAYBACK[Reconciliation::NO]) : sprintf('<span style="color: green">%s</span>', Reconciliation::PAYBACK[Reconciliation::YES]);
                    $tmp2['payback_time'] = $v['payback_time'];
                    $tmp2['payback_user'] = $v['payback_user'];
                    $tmp2['channel_rate'] = CrmHelper::percentage($v['channel_rate']);
                    $tmp2['first_division'] = CrmHelper::percentage($v['first_division']);
                    $tmp2['second_division'] = CrmHelper::percentage($v['second_division']);
                    $tmp2['second_division_condition'] = $v['second_division_condition'];
                    $tmp2['accrual_water_other'] = $v['accrual_water_other'];
                    $tmp2['accrual_water_rmb'] = $v['accrual_water_rmb'];
                    $tmp2['reconciliation_adjustment'] = $v['reconciliation_adjustment'];
                    $tmp2['reconciliation_rmb_adjustment'] = $v['reconciliation_rmb_adjustment'];
                    $tmp2['reconciliation_type'] = $diff[$v['reconciliation_type']] ?? '未知' . $v['reconciliation_type'];
                    $tmp2['reconciliation_remark'] = $v['reconciliation_remark'];
                    $tmp2['reconciliation_user_name'] = $v['reconciliation_user_name'];
                    $tmp2['reconciliation_time'] = $v['reconciliation_time'];
                    $tmp2['reconciliation_water_other'] = $v['reconciliation_water_other'];
                    $tmp2['reconciliation_water_rmb'] = $v['reconciliation_water_rmb'];
                    $tmp2['reconciliation_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['reconciliation_water_other']);
                    $tmp2['reconciliation_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['reconciliation_water_rmb']);
                    $tmp2['review_type'] = $this->url($url, $v);
                    break;
                default:
                    $tmp2['channel_rate'] = CrmHelper::percentage($v['channel_rate']);
                    $tmp2['first_division'] = CrmHelper::percentage($v['first_division']);
                    $tmp2['second_division'] = CrmHelper::percentage($v['second_division']);
                    $tmp2['second_division_condition'] = $v['second_division_condition'];
                    $tmp2['backstage_water_other'] = $v['backstage_water_other'];
                    $tmp2['backstage_water_rmb'] = $v['backstage_water_rmb'];
                    $tmp2['backstage_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['backstage_water_other']);
                    $tmp2['backstage_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['backstage_water_rmb']);
                    $tmp2['operation_adjustment'] = $v['operation_adjustment'];
                    $tmp2['operation_rmb_adjustment'] = $v['operation_rmb_adjustment'];
                    $tmp2['operation_type'] = $diff[$v['operation_type']] ?? '未知' . $v['operation_type'];
                    $tmp2['operation_remark'] = $v['operation_remark'];
                    $tmp2['operation_user_name'] = $v['operation_user_name'];
                    $tmp2['operation_time'] = $v['operation_time'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['operation_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['operation_water_other']);
                    $tmp2['operation_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['operation_water_rmb']);
                    $tmp2['accrual_adjustment'] = $v['accrual_adjustment'];
                    $tmp2['accrual_rmb_adjustment'] = $v['accrual_rmb_adjustment'];
                    $tmp2['accrual_type'] = $diff[$v['accrual_type']] ?? '未知' . $v['accrual_type'];
                    $tmp2['accrual_remark'] = $v['accrual_remark'];
                    $tmp2['accrual_user_name'] = $v['accrual_user_name'];
                    $tmp2['accrual_time'] = $v['accrual_time'];
                    $tmp2['accrual_water_other'] = $v['accrual_water_other'];
                    $tmp2['accrual_water_rmb'] = $v['accrual_water_rmb'];
                    $tmp2['accrual_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['accrual_water_other']);
                    $tmp2['accrual_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['accrual_water_rmb']);
                    $tmp2['reconciliation_adjustment'] = $v['reconciliation_adjustment'];
                    $tmp2['reconciliation_rmb_adjustment'] = $v['reconciliation_rmb_adjustment'];
                    $tmp2['reconciliation_type'] = $diff[$v['reconciliation_type']] ?? '未知' . $v['reconciliation_type'];
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
                if (in_array($key, $water)) {
                    if (!isset($tmp3[$key])) {
                        $tmp3[$key] = 0;
                    }
                    $tmp3[$key] += $value;
                } else {
                    $tmp3[$key] = '--';
                }
                $tmp3['num'] = 0;
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
        $rate = ExchangeRate::getList($tmp->billing_cycle);
        if (!$rate) {
            flash('汇率暂未填写或填写不完整！请通知相关负责人填写！', 'danger');
            return redirect()->back()->withInput();
        }
        $data = $this->transformName($source, $tmp, '', '', '');
        $type = CrmHelper::addEmptyToArray('差异类型', Difference::getList());
        $title = trans('app.编辑', ['value' => trans('crm.对账审核')]);
        return view('crm.reconciliation-audit.edit', compact('title', 'data', 'type'));
    }

    public function update(Request $request, $id, $source)
    {
        $time = date('Y-m-d H:i:s', time());
        $data = Reconciliation::findOrFail($id);
        $rate = ExchangeRate::getList($data->billing_cycle);
        $tmp = $this->transformName($source, $data, $request, $time, $rate[$data->reconciliation_currency]);
        $data->update($tmp);
        EditLog::create(array_merge($request->all(), ['user_name' => \Auth::user()->alias, 'time' => $time,
            'billing_cycle_start' => $data['billing_cycle_start'], 'billing_cycle_end' => $data['billing_cycle_end'], 'client' => $data['client'],
            'backstage_channel' => $data['backstage_channel'], 'product_id' => $data['product_id'], 'rec_id' => $data['id']]));

        flash(trans('app.编辑成功', ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->route('reconciliationAudit', ['source' => $source, 'product_id' => $data['product_id'], 'scope[startDate]' => $data['billing_cycle_start'], 'scope[endDate]' => $data['billing_cycle_end']]);
    }

    public function transformName($source, $data, $request, $time, $rate)
    {
        if ($time) {
            switch ($source) {
                case Reconciliation::OPERATION:
                    $tmp['operation_adjustment'] = $request['adjustment'];
                    $tmp['operation_rmb_adjustment'] = $request['adjustment'] * $rate;
                    $tmp['operation_type'] = $request['type'];
                    $tmp['operation_remark'] = $request['remark'];
                    $tmp['operation_user_name'] = \Auth::user()->alias;
                    $tmp['operation_time'] = $time;
                    $tmp['operation_water_other'] = $data['backstage_water_other'] + $request['adjustment'];
                    $tmp['operation_water_rmb'] = $data['backstage_water_rmb'] + $tmp['operation_rmb_adjustment'];
                    break;
                case Reconciliation::ACCRUAL:
                    $tmp['accrual_adjustment'] = $request['adjustment'];
                    $tmp['accrual_rmb_adjustment'] = $request['adjustment'] * $rate;
                    $tmp['accrual_type'] = $request['type'];
                    $tmp['accrual_remark'] = $request['remark'];
                    $tmp['accrual_user_name'] = \Auth::user()->alias;
                    $tmp['accrual_time'] = $time;
                    $tmp['accrual_water_other'] = $data['operation_water_other'] + $request['adjustment'];
                    $tmp['accrual_water_rmb'] = $data['operation_water_rmb'] + $tmp['accrual_rmb_adjustment'];
                    break;
                case Reconciliation::RECONCILIATION:
                    $tmp['reconciliation_adjustment'] = $request['adjustment'];
                    $tmp['reconciliation_rmb_adjustment'] = $request['adjustment'] * $rate;
                    $tmp['reconciliation_type'] = $request['type'];
                    $tmp['reconciliation_remark'] = $request['remark'];
                    $tmp['reconciliation_user_name'] = \Auth::user()->alias;
                    $tmp['reconciliation_time'] = $time;
                    $tmp['reconciliation_water_other'] = $data['accrual_water_other'] + $request['adjustment'];
                    $tmp['reconciliation_water_rmb'] = $data['accrual_water_rmb'] + $tmp['reconciliation_rmb_adjustment'];
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
        $id = \Request::get('id');
        $source = \Request::get('source');
        $reason = \Request::get('reason');
        if ($id) {
            $data = Reconciliation::findOrFail($id);
            $data->update(['review_type' => $status]);
            $this->push($pid, $source, $status, $reason);
            flash(trans('crm.审核', ['value' => trans('crm.对账审核')]), 'success');
            return redirect()->route('reconciliationAudit', ['source' => $source, 'product_id' => $pid, 'scope[startDate]' => $scope->startTimestamp, 'scope[endDate]' => $scope->endTimestamp]);
        } else {
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
                    case Reconciliation::FRC:
                        $key = Principal::OPS;
                        $message = '复核';
                        break;
                    case Reconciliation::TREASURER:
                        $key = Principal::FRC;
                        $message = '拒绝';
                        break;
                    case Reconciliation::OOR:
                        $key = Principal::FSR;
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
            OperateLogHelper::sendWXMsg($username, sprintf('你好！%s,%s%s月的%s审计已%s%s，请及时处理:%s', $username, $product[$pid],
                date('m', strtotime($scope->startTimestamp)), $water, $message, $reason, route('reconciliationAudit',
                    ['source' => $source, 'product_id' => $pid])));
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

    }

    public function header($source)
    {
        switch (true) {
            case $source == Reconciliation::OPERATION:
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '信期类型', '信期', '对账币', '人民币',
                    '调整', '转化rmb调整', '调整类型', '调整备注', '调整人', '调整时间', '对账币', '人民币', '操作'];
                break;
            case $source == Reconciliation::ACCRUAL:
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '信期类型', '信期', '渠道费率', '一级分成', '二级分成', '二级分成条件', '对账币', '人民币',
                    '调整', '转化rmb调整', '类型', '备注', '调整人', '调整时间', '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '操作'];
                break;
            case $source == Reconciliation::RECONCILIATION:
                $header = ['#', '序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '信期类型', '信期', '开票状态', '开票号', '开票时间', '开票人', '回款状态', '回款时间', '回款确认人',
                    '渠道费率', '一级分成', '二级分成', '二级分成条件', '对账币', '人民币',
                    '调整', '转化rmb调整', '类型', '备注', '调整人', '调整时间', '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '操作'];
                break;
            default:
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '信期类型', '信期', '渠道费率', '一级分成', '二级分成', '二级分成条件', '对账币', '人民币',
                    '对账币-费率分成', '人民币-费率分成', '调整', '转化rmb调整', '类型', '备注', '调整人', '调整时间',
                    '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '调整', '转化rmb调整', '类型', '备注', '调整人', '调整时间',
                    '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '调整', '转化rmb调整', '类型', '备注', '调整人', '调整时间',
                    '对账币', '人民币', '对账币-费率分成', '人民币-费率分成'];
        }

        return $header;
    }

    public function url($url, $data)
    {
        $post = Principal::where(['principal_id' => \Auth::user()->user_id])->get(['product_id', 'job_id'])->toArray();
        $limitPost = [];
        foreach ($post as $v) {
            $limitPost[] = $v['job_id'];
        }
        $p1 = \Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.edit']);
        $p2 = \Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.review']);
        $tmp = '';
        if (in_array($data['review_type'], [Reconciliation::UNRD, Reconciliation::OPD]) && $p1 && in_array($data['review_type'], $limitPost)) {
            $tmp .= '<a href="' . $url['edit'] . '" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>';
        }
        if (in_array($data['review_type'], [Reconciliation::TREASURER]) && $p2 && in_array($data['review_type'], $limitPost)) {
            $tmp .= '<a href="' . $url['edit'] . '" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>
<a href="' . $url['review'] . '" target="_self"> <i class="fa fa-level-up fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="审核"></i> </a>';
        }
        if (in_array($data['review_type'], [Reconciliation::FRC]) && $p2 && in_array(Principal::OPS, $limitPost)) {
            $tmp .= '<a href="' . $url['review'] . '" target="_self"> <i class="fa fa-level-up fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="审核"></i> </a>
<a href="' . $url['refuse'] . '" target="_self"> <i class="fa fa-level-down fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="拒绝"></i> </a>';
        }
        if (in_array($data['review_type'], [Reconciliation::OOR]) && $p2 && in_array(Principal::FSR, $limitPost)) {
            $tmp .= '<a href="' . $url['review'] . '" target="_self"> <i class="fa fa-check fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="审核"></i> </a>
<a href="' . $url['refuse'] . '" target="_self"> <i class="fa fa-times fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="拒绝"></i> </a>';
        }
        return $tmp;
    }

    public function invoice(Request $request)
    {
        $data = Reconciliation::findOrFail($request->id);
        $message = [];
        foreach ($data as $v) {
            if ($v['review_type'] == Reconciliation::FSR){
                $v->update(['billing_num' => $request->billing_num, 'billing_time' => $request->billing_time, 'billing_type' => Reconciliation::YES, 'billing_user' => \Auth::user()->alias]);
            }else{
                $message[] = sprintf('%s_%s_%s_%d_暂无审核', $v['billing_cycle'], $v['client'], $v['backstage_channel'], $v['product_id']);
            }
        }

        flash(trans('crm.开票成功'.implode(',', $message), ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->back()->withInput();
    }

    public function payback(Request $request)
    {
        $data = Reconciliation::findOrFail($request->id);
        $message = [];
        foreach ($data as $v) {
            if ($v['billing_type'] == Reconciliation::YES){
                $v->update(['payback_time' => $request->payback_time, 'payback_type' => Reconciliation::YES, 'payback_user' => \Auth::user()->alias]);
            }else{
                $message[] = sprintf('%s_%s_%s_%d_暂无开票', $v['billing_cycle'], $v['client'], $v['backstage_channel'], $v['product_id']);
            }
        }

        flash(trans('crm.回款成功'.implode(',', $message), ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->back()->withInput();
    }
}
