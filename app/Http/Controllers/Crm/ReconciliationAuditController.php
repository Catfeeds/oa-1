<?php

namespace App\Http\Controllers\Crm;

use App\Http\Components\Helpers\CrmHelper;
use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Difference;
use App\Models\Crm\EditLog;
use App\Models\Crm\ExchangeRate;
use App\Models\Crm\Principal;
use App\Models\Crm\Product;
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
            switch ($v['job_id']) {
                case Principal::OPS:
                    $job[$v['product_id']][1] = 1;
                    $job[$v['product_id']][6] = 6;
                    break;
                case Principal::OPD:
                    $job[$v['product_id']][2] = 2;
                    break;
                case Principal::FAC:
                    $job[$v['product_id']][3] = 3;
                    break;
                case Principal::TREASURER:
                    $job[$v['product_id']][4] = 4;
                    $job[$v['product_id']][8] = 8;
                    break;
                case Principal::FRC:
                    $job[$v['product_id']][5] = 5;
                    break;
                case Principal::FSR:
                    $job[$v['product_id']][7] = 7;
                    $job[$v['product_id']][8] = 8;
                    break;
            }
        }
        $products = Product::getList($limitProduct);
        $pid = \Request::get('product_id', key($products));
        if (!in_array($pid, array_keys($products))) {
            return redirect()->back()->withInput();
        }

        $review = array_intersect_key(Reconciliation::REVIEW_TYPE, $job[$pid]);
        $source = \Request::get('source', key($review));

        $columns = $this->header($source);

        if (!in_array($source, array_keys($review))) {
            return redirect()->back()->withInput();
        }
        $data = Reconciliation::where(['product_id' => $pid, 'billing_cycle' => date('Y-m', strtotime($scope->startTimestamp))])->get(['review_type'])->keyBy('review_type')->toArray();
        $status = 1;
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
        $where = '';
        if (in_array($source, [Reconciliation::FRC, Reconciliation::OOR])) {
            $where = ' and a.review_type = ' . $source;
        }
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
                a.operation_divide_other,
                a.operation_divide_rmb,
                a.accrual_adjustment,
                a.accrual_rmb_adjustment,
                a.accrual_type,
                a.accrual_remark,
                a.accrual_user_name,
                a.accrual_time,
                a.accrual_water_other,
                a.accrual_water_rmb,
                a.accrual_divide_other,
                a.accrual_divide_rmb,
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
            LEFT JOIN cmr_reconciliation_proportion AS p ON (
                a.product_id = p.product_id
                AND a.backstage_channel = p.backstage_channel
            )
            WHERE a.product_id = {$pid} AND a.billing_cycle = '{$billing_cycle}'{$where} AND {$scope->getWhere()}
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
            if (in_array($source, [Reconciliation::TREASURER, Reconciliation::FRC, Reconciliation::OOR])) {
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
            $url['review'] = route('reconciliationAudit.review', ['status' => $v['review_type'] + 1, 'pid' => $pid, 'source' => $source, 'id[]' => $v['id']]);
            $url['refuse'] = route('reconciliationAudit.review', ['status' => $v['review_type'] - 1, 'pid' => $pid, 'source' => $source, 'id[]' => $v['id']]);
            $url['revision'] = route('reconciliationAudit.revision', ['id' => $v['id']]);
            switch (true) {
                case in_array($source, [Reconciliation::UNRD, Reconciliation::OPS]):
                    $tmp2['backstage_water_other'] = $v['backstage_water_other'];
                    $tmp2['backstage_water_rmb'] = $v['backstage_water_rmb'];
                    $tmp2['operation_adjustment'] = $v['operation_adjustment'];
                    $tmp2['operation_rmb_adjustment'] = is_numeric($v['operation_rmb_adjustment']) ? $v['operation_rmb_adjustment'] : array_sum(json_decode($v['operation_rmb_adjustment']));
                    $tmp2['operation_type'] = is_numeric($v['operation_type']) ? ($diff[$v['operation_type']] ?? '--') : sprintf('<a class="eye" data-url="%s">%s</a>', route('reconciliationAudit.detail', ['id' => $v['id'], 'source' => Reconciliation::OPERATION]), $diff[$this->pop($v['operation_type'])]);
                    $tmp2['operation_remark'] = $v['operation_remark'];
                    $tmp2['operation_user_name'] = $v['operation_user_name'];
                    $tmp2['operation_time'] = $v['operation_time'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['review_type'] = $this->url($url, $v, $source);
                    break;
                case in_array($source, [Reconciliation::OPD, Reconciliation::FAC]):
                    $tmp2['channel_rate'] = CrmHelper::percentage($v['channel_rate']);
                    $tmp2['first_division'] = CrmHelper::percentage($v['first_division']);
                    $tmp2['second_division'] = CrmHelper::percentage($v['second_division']);
                    $tmp2['second_division_condition'] = $v['second_division_condition'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['accrual_adjustment'] = is_numeric($v['accrual_adjustment']) ? $v['accrual_adjustment'] : array_sum(json_decode($v['accrual_adjustment']));
                    $tmp2['accrual_rmb_adjustment'] = $v['accrual_rmb_adjustment'];
                    $tmp2['accrual_type'] = is_numeric($v['accrual_type']) ? ($diff[$v['accrual_type']] ?? '--') : sprintf('<a class="eye" data-url="%s">%s</a>', route('reconciliationAudit.detail', ['id' => $v['id'], 'source' => Reconciliation::ACCRUAL]), $diff[$this->pop($v['accrual_type'])]);
                    $tmp2['accrual_remark'] = $v['accrual_remark'];
                    $tmp2['accrual_user_name'] = $v['accrual_user_name'];
                    $tmp2['accrual_time'] = $v['accrual_time'];
                    $tmp2['accrual_water_other'] = $v['accrual_water_other'];
                    $tmp2['accrual_water_rmb'] = $v['accrual_water_rmb'];
                    $tmp2['accrual_divide_other'] = $v['accrual_divide_other'];
                    $tmp2['accrual_divide_rmb'] = $v['accrual_divide_rmb'];
                    $tmp2['review_type'] = $this->url($url, $v, $source);
                    break;
                case in_array($source, [Reconciliation::TREASURER, Reconciliation::FRC, Reconciliation::OOR]):
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
                    $tmp2['accrual_divide_other'] = $v['accrual_divide_other'];
                    $tmp2['accrual_divide_rmb'] = $v['accrual_divide_rmb'];
                    $tmp2['reconciliation_adjustment'] = is_numeric($v['reconciliation_adjustment']) ? $v['reconciliation_adjustment'] : array_sum(json_decode($v['reconciliation_adjustment']));
                    $tmp2['reconciliation_rmb_adjustment'] = $v['reconciliation_rmb_adjustment'];
                    $tmp2['reconciliation_type'] = is_numeric($v['reconciliation_adjustment']) ? ($diff[$v['reconciliation_type']] ?? '--') : sprintf('<a class="eye" data-url="%s">%s</a>', route('reconciliationAudit.detail', ['id' => $v['id'], 'source' => Reconciliation::RECONCILIATION]), $diff[$this->pop($v['reconciliation_type'])]);
                    $tmp2['reconciliation_remark'] = $v['reconciliation_remark'];
                    $tmp2['reconciliation_user_name'] = $v['reconciliation_user_name'];
                    $tmp2['reconciliation_time'] = $v['reconciliation_time'];
                    $tmp2['reconciliation_water_other'] = $v['reconciliation_water_other'];
                    $tmp2['reconciliation_water_rmb'] = $v['reconciliation_water_rmb'];
                    $tmp2['reconciliation_divide_other'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['reconciliation_water_other']);
                    $tmp2['reconciliation_divide_rmb'] = CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['reconciliation_water_rmb']);
                    $tmp2['review_type'] = $this->url($url, $v, $source);
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
                    $tmp2['operation_adjustment'] = is_numeric($v['operation_rmb_adjustment']) ? $v['operation_rmb_adjustment'] : array_sum(json_decode($v['operation_rmb_adjustment']));
                    $tmp2['operation_rmb_adjustment'] = $v['operation_rmb_adjustment'];
                    $tmp2['operation_type'] = is_numeric($v['operation_type']) ? ($diff[$v['operation_type']] ?? '--') : sprintf('<a class="eye" data-url="%s">%s</a>', route('reconciliationAudit.detail', ['id' => $v['id'], 'source' => Reconciliation::OPERATION]), $diff[$this->pop($v['operation_type'])]);
                    $tmp2['operation_remark'] = $v['operation_remark'];
                    $tmp2['operation_user_name'] = $v['operation_user_name'];
                    $tmp2['operation_time'] = $v['operation_time'];
                    $tmp2['operation_water_other'] = $v['operation_water_other'];
                    $tmp2['operation_water_rmb'] = $v['operation_water_rmb'];
                    $tmp2['operation_divide_other'] = $v['operation_divide_other'];
                    $tmp2['operation_divide_rmb'] = $v['operation_divide_rmb'];
                    $tmp2['accrual_adjustment'] = is_numeric($v['accrual_adjustment']) ? $v['accrual_adjustment'] : array_sum(json_decode($v['accrual_adjustment']));
                    $tmp2['accrual_rmb_adjustment'] = $v['accrual_rmb_adjustment'];
                    $tmp2['accrual_type'] = is_numeric($v['accrual_type']) ? ($diff[$v['accrual_type']] ?? '--') : sprintf('<a class="eye" data-url="%s">%s</a>', route('reconciliationAudit.detail', ['id' => $v['id'], 'source' => Reconciliation::ACCRUAL]), $diff[$this->pop($v['accrual_type'])]);
                    $tmp2['accrual_remark'] = $v['accrual_remark'];
                    $tmp2['accrual_user_name'] = $v['accrual_user_name'];
                    $tmp2['accrual_time'] = $v['accrual_time'];
                    $tmp2['accrual_water_other'] = $v['accrual_water_other'];
                    $tmp2['accrual_water_rmb'] = $v['accrual_water_rmb'];
                    $tmp2['accrual_divide_other'] = $v['accrual_divide_other'];
                    $tmp2['accrual_divide_rmb'] = $v['accrual_divide_rmb'];
                    $tmp2['reconciliation_adjustment'] = is_numeric($v['reconciliation_adjustment']) ? $v['reconciliation_adjustment'] : array_sum(json_decode($v['reconciliation_adjustment']));
                    $tmp2['reconciliation_rmb_adjustment'] = $v['reconciliation_rmb_adjustment'];
                    $tmp2['reconciliation_type'] = is_numeric($v['reconciliation_adjustment']) ? ($diff[$v['reconciliation_type']] ?? '--') : sprintf('<a class="eye" data-url="%s">%s</a>', route('reconciliationAudit.detail', ['id' => $v['id'], 'source' => Reconciliation::RECONCILIATION]), $diff[$this->pop($v['reconciliation_type'])]);
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
        $title = trans('app.编辑', ['value' => trans('crm.对账审核')]);
        return view('crm.reconciliation-audit.edit', compact('title', 'data'));
    }

    public function update(Request $request, $id, $source)
    {
        $time = date('Y-m-d H:i:s', time());
        $data = Reconciliation::findOrFail($id);
        $rate = ExchangeRate::getList($data->billing_cycle);
        $tmp = $this->transformName($source, $data, $request, $time, $rate[$data->reconciliation_currency]);
        $data->update($tmp);
        EditLog::create(['user_name' => \Auth::user()->alias, 'time' => $time,
            'billing_cycle_start' => $data['billing_cycle_start'], 'billing_cycle_end' => $data['billing_cycle_end'], 'client' => $data['client'],
            'backstage_channel' => $data['backstage_channel'], 'product_id' => $data['product_id'], 'rec_id' => $data['id'], 'adjustment' => json_encode($request['adjustment'])
            , 'type' => json_encode($request['type']), 'remark' => $request['remark']]);

        flash(trans('app.编辑成功', ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->route('reconciliationAudit', ['source' => $source, 'product_id' => $data['product_id']]);
    }

    public function transformName($source, $data, $request, $time, $rate)
    {
        if (empty($request['adjustment'])) {
            $request['adjustment'] = 0;
            $request['type'] = 0;
        }
        if ($time) {
            switch ($source) {
                case $source == Reconciliation::UNRD:
                    $tmp['operation_adjustment'] = json_encode($request['adjustment']);
                    $tmp['operation_rmb_adjustment'] = (int)array_sum($request['adjustment']) * $rate;
                    $tmp['operation_type'] = json_encode($request['type']);
                    $tmp['operation_remark'] = $request['remark'];
                    $tmp['operation_user_name'] = \Auth::user()->alias;
                    $tmp['operation_time'] = $time;
                    $tmp['operation_water_other'] = $data['backstage_water_other'] + (int)array_sum($request['adjustment']);
                    $tmp['operation_water_rmb'] = $data['backstage_water_rmb'] + $tmp['operation_rmb_adjustment'];
                    $tmp['operation_divide_other'] = CrmHelper::dividedInto($data['channel_rate'], $data['first_division'], $data['second_division'], $data['second_division_condition'], $tmp['operation_water_other']);
                    $tmp['operation_divide_rmb'] = CrmHelper::dividedInto($data['channel_rate'], $data['first_division'], $data['second_division'], $data['second_division_condition'], $tmp['operation_water_rmb']);
                    break;
                case $source == Reconciliation::OPD:
                    $tmp['accrual_adjustment'] = json_encode($request['adjustment']);
                    $tmp['accrual_rmb_adjustment'] = (int)array_sum($request['adjustment']) * $rate;
                    $tmp['accrual_type'] = json_encode($request['type']);
                    $tmp['accrual_remark'] = $request['remark'];
                    $tmp['accrual_user_name'] = \Auth::user()->alias;
                    $tmp['accrual_time'] = $time;
                    $tmp['accrual_water_other'] = $data['operation_water_other'] + (int)array_sum($request['adjustment']);
                    $tmp['accrual_water_rmb'] = $data['operation_water_rmb'] + $tmp['accrual_rmb_adjustment'];
                    $tmp['accrual_divide_other'] = CrmHelper::dividedInto($data['channel_rate'], $data['first_division'], $data['second_division'], $data['second_division_condition'], $tmp['accrual_water_other']);
                    $tmp['accrual_divide_rmb'] = CrmHelper::dividedInto($data['channel_rate'], $data['first_division'], $data['second_division'], $data['second_division_condition'], $tmp['accrual_water_rmb']);
                    break;
                case $source == Reconciliation::TREASURER:
                    $tmp['reconciliation_adjustment'] = json_encode($request['adjustment']);
                    $tmp['reconciliation_rmb_adjustment'] = (int)array_sum($request['adjustment']) * $rate;
                    $tmp['reconciliation_type'] = json_encode($request['type']);
                    $tmp['reconciliation_remark'] = $request['remark'];
                    $tmp['reconciliation_user_name'] = \Auth::user()->alias;
                    $tmp['reconciliation_time'] = $time;
                    $tmp['reconciliation_water_other'] = $data['accrual_water_other'] + (int)array_sum($request['adjustment']);
                    $tmp['reconciliation_water_rmb'] = $data['accrual_water_rmb'] + $tmp['reconciliation_rmb_adjustment'];
                    break;
            }
        } else {
            switch ($source) {
                case $source == Reconciliation::UNRD:
                    $tmp['adjustment'] = $data['operation_adjustment'];
                    $tmp['type'] = $data['operation_type'];
                    $tmp['remark'] = $data['operation_remark'];
                    break;
                case $source == Reconciliation::OPD:
                    $tmp['adjustment'] = $data['accrual_adjustment'];
                    $tmp['type'] = $data['accrual_type'];
                    $tmp['remark'] = $data['accrual_remark'];
                    break;
                case $source == Reconciliation::TREASURER:
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
            foreach ($data as $v) {
                $update = ['review_type' => $status];
                if ($status == Reconciliation::FRC) {
                    if ($v['reconciliation_water_other'] == 0 && $v['reconciliation_type'] == 0) {
                        $update += [
                            'reconciliation_water_other' => $v['accrual_water_other'],
                            'reconciliation_water_rmb' => $v['accrual_water_rmb']
                        ];
                    }
                }
                $v->update($update);
                unset($update);
            }
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
                    $update = ['review_type' => (int)$status];
                    switch (true) {
                        case $status == Reconciliation::OPS:
                            if ($v['operation_water_other'] == 0 && $v['operation_type'] == 0) {
                                $update += [
                                    'operation_water_other' => $v['backstage_water_other'],
                                    'operation_water_rmb' => $v['backstage_water_rmb'],
                                    'operation_divide_other' => CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['backstage_water_other']),
                                    'operation_divide_rmb' => CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['backstage_water_rmb']),
                                ];
                            }
                            break;
                        case $status == Reconciliation::FAC:
                            if ($v['accrual_water_other'] == 0 && $v['accrual_type'] == 0) {
                                $update += [
                                    'accrual_water_other' => $v['operation_water_other'],
                                    'accrual_water_rmb' => $v['operation_water_rmb'],
                                    'accrual_divide_other' => CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['operation_water_other']),
                                    'accrual_divide_rmb' => CrmHelper::dividedInto($v['channel_rate'], $v['first_division'], $v['second_division'], $v['second_division_condition'], $v['operation_water_rmb']),
                                ];
                            }
                            break;
                    }
                    $v->update($update);
                    unset($update);
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
            case Reconciliation::UNRD:
                $water = '运营流水';
                switch ($review) {
                    case Reconciliation::OPS:
                        $key = Principal::OPD;
                        $message = '提交审核';
                        break;
                }
                break;
            case Reconciliation::OPS:
                $water = '运营流水';
                switch ($review) {
                    case Reconciliation::OPD:
                        $key = Principal::FAC;
                        $message = '通过审核';
                        break;
                    case Reconciliation::UNRD:
                        $key = Principal::OPS;
                        $message = '拒绝审核';
                        break;
                }
                break;
            case Reconciliation::OPD:
                $water = '计提流水';
                switch ($review) {
                    case Reconciliation::FAC:
                        $key = Principal::TREASURER;
                        $message = '提交审核';
                        break;
                    case Reconciliation::OPS:
                        $key = Principal::OPD;
                        $message = '拒绝审核';
                        break;
                }
                break;
            case Reconciliation::FAC:
                $water = '计提流水';
                switch ($review) {
                    case Reconciliation::TREASURER:
                        $key = Principal::FRC;
                        $message = '通过审核';
                        break;
                    case Reconciliation::OPD:
                        $key = Principal::FAC;
                        $message = '拒绝审核';
                        break;
                }
                break;
            case Reconciliation::TREASURER:
                $water = '对账流水';
                switch ($review) {
                    case Reconciliation::FRC:
                        $key = Principal::OPS;
                        $message = '提交审核';
                        break;
                }
                break;
            case Reconciliation::FRC:
                $water = '对账流水';
                switch ($review) {
                    case Reconciliation::OOR:
                        $key = Principal::FSR;
                        $message = '通过复核';
                        break;
                    case Reconciliation::TREASURER:
                        $key = Principal::FRC;
                        $message = '拒绝复核';
                        break;
                }
                break;
            case Reconciliation::OOR:
                $water = '对账流水';
                switch ($review) {
                    case Reconciliation::FSR:
                        $key = Principal::FSR;
                        $message = '完成审核';
                        break;
                    case Reconciliation::FRC:
                        $key = Principal::OPS;
                        $message = '拒绝审核';
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
            case in_array($source, [Reconciliation::UNRD, Reconciliation::OPS]):
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '信期类型', '信期', '对账币', '人民币',
                    '调整', '转化rmb调整', '调整类型', '调整备注', '调整人', '调整时间', '对账币', '人民币', '操作'];
                break;
            case in_array($source, [Reconciliation::OPD, Reconciliation::FAC]):
                $header = ['序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '信期类型', '信期', '渠道费率', '一级分成', '二级分成', '二级分成条件', '对账币', '人民币',
                    '调整', '转化rmb调整', '类型', '备注', '调整人', '调整时间', '对账币', '人民币', '对账币-费率分成', '人民币-费率分成', '操作'];
                break;
            case in_array($source, [Reconciliation::TREASURER, Reconciliation::FRC, Reconciliation::OOR]):
                $header = ['#', '序号', '结算周期', '收入类型', '我方', '客户', '游戏', '上线名称', '业务线',
                    '地区', '对账币', '系统', '分成类型', '诗悦后台渠道', '统一渠道名称', '信期类型', '信期', '开票状态', '开票号', '开票时间', '开票人', '回款状态', '回款时间', '回款确认人',
                    '渠道费率', '一级分成', '二级分成', '二级分成条件', '对账币', '人民币', '对账币-费率分成', '人民币-费率分成',
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

    public function url($url, $data, $source)
    {
        $post = Principal::where(['principal_id' => \Auth::user()->user_id])->get(['product_id', 'job_id'])->toArray();
        $limitPost = [];
        foreach ($post as $v) {
            $limitPost[] = $v['job_id'];
        }
        $p1 = \Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.edit']);
        $p2 = \Entrust::can(['crm-all', 'reconciliation-all', 'reconciliation-reconciliationAudit', 'reconciliation-reconciliationAudit.review']);
        $tmp = '';
        if ($source == Reconciliation::UNRD && $p1 && in_array(Principal::OPS, $limitPost) && $data['review_type'] == Reconciliation::UNRD) {
            $tmp .= '<a href="' . $url['edit'] . '" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>';
        }
        if ($source == Reconciliation::OPD && $p1 && in_array(Principal::FAC, $limitPost) && $data['review_type'] == Reconciliation::OPD) {
            $tmp .= '<a href="' . $url['edit'] . '" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>';
        }
        if ($source == Reconciliation::TREASURER && $p1 && in_array(Principal::FRC, $limitPost) && $data['review_type'] == Reconciliation::TREASURER) {
            $tmp .= '<a href="' . $url['edit'] . '" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>
<a href="' . $url['revision'] . '" class="generate"> <i class="fa fa-edit fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="调整流水"></i> </a>
<a href="' . $url['review'] . '" target="_self"> <i class="fa fa-level-up fa-lg confirmation" data-toggle="tooltip" data-placement="top" title="" data-original-title="提交审核" data-confirm="确认提交审核?"></i> </a>';
        }
        if ($source == Reconciliation::FRC && $p2 && in_array(Principal::OPS, $limitPost) && $data['review_type'] == Reconciliation::FRC) {
            $tmp .= '<a href="' . $url['review'] . '" target="_self"> <i class="fa fa-level-up fa-lg confirmation" data-toggle="tooltip" data-placement="top" title="" data-original-title="复核" data-confirm="确认复核?"></i> </a>
<a href="' . $url['refuse'] . '" target="_self"> <i class="fa fa-level-down fa-lg confirmation" data-toggle="tooltip" data-placement="top" title="" data-original-title="拒绝" data-confirm="确认拒绝?"></i> </a>';
        }
        if ($source == Reconciliation::OOR && $p2 && in_array(Principal::FSR, $limitPost) && $data['review_type'] == Reconciliation::OOR) {
            $tmp .= '<a href="' . $url['review'] . '" target="_self"> <i class="fa fa-check fa-lg confirmation" data-toggle="tooltip" data-placement="top" title="" data-original-title="审核" data-confirm="确认提交审核?"></i> </a>
<a href="' . $url['refuse'] . '" target="_self"> <i class="fa fa-times fa-lg confirmation" data-toggle="tooltip" data-placement="top" title="" data-original-title="拒绝" data-confirm="确认拒绝?"></i> </a>';
        }
        return $tmp;
    }

    public function invoice(Request $request)
    {
        $data = Reconciliation::findOrFail($request->id);
        $message = [];
        foreach ($data as $v) {
            if ($v['review_type'] == Reconciliation::FSR) {
                $v->update(['billing_num' => $request->billing_num, 'billing_time' => $request->billing_time, 'billing_type' => Reconciliation::YES, 'billing_user' => \Auth::user()->alias]);
            } else {
                $message[] = sprintf('%s_%s_%s_%d_暂无审核', $v['billing_cycle'], $v['client'], $v['backstage_channel'], $v['product_id']);
            }
        }
        if (!empty($message)) {
            flash(trans(implode(',', $message), ['value' => trans('crm.对账审核')]), 'danger');
            return redirect()->back()->withInput();
        } else {
            flash(trans('crm.开票成功', ['value' => trans('crm.对账审核')]), 'success');
            return redirect()->back()->withInput();
        }

    }

    public function payback(Request $request)
    {
        $data = Reconciliation::findOrFail($request->id);
        $message = [];
        foreach ($data as $v) {
            if ($v['billing_type'] == Reconciliation::YES) {
                $v->update(['payback_time' => $request->payback_time, 'payback_type' => Reconciliation::YES, 'payback_user' => \Auth::user()->alias]);
            } else {
                $message[] = sprintf('%s_%s_%s_%d_暂无开票', $v['billing_cycle'], $v['client'], $v['backstage_channel'], $v['product_id']);
            }
        }
        if (!empty($message)) {
            flash(trans(implode(',', $message), ['value' => trans('crm.对账审核')]), 'danger');
            return redirect()->back()->withInput();
        } else {
            flash(trans('crm.回款成功', ['value' => trans('crm.对账审核')]), 'success');
            return redirect()->back()->withInput();
        }
    }

    public function revision(Request $request)
    {
        $data = Reconciliation::findOrFail($request->id);
        $tmp = $data->toArray();
        $rate = ExchangeRate::getList($data->billing_cycle);
        $createData = $update = [];
        $create = [
            'billing_cycle',
            'product_id',
            'billing_cycle_start',
            'income_type',
            'billing_cycle_end',
            'company',
            'client',
            'game_name',
            'online_name',
            'business_line',
            'area',
            'reconciliation_currency',
            'os',
            'divided_type',
            'backstage_channel',
            'unified_channel',
            'period_name',
            'period',
            'review_type'
        ];
        $water = [
            'backstage_water_other',
            'backstage_water_rmb',
            'operation_water_other',
            'operation_water_rmb',
            'operation_divide_other',
            'operation_divide_rmb',
            'accrual_water_other',
            'accrual_water_rmb',
            'accrual_divide_other',
            'accrual_divide_rmb',
            'reconciliation_water_other',
            'reconciliation_water_rmb'
        ];
        if ($tmp['reconciliation_water_other'] == 0 && $tmp['reconciliation_type'] == 0) {
            $tmp['reconciliation_water_other'] = $data['accrual_water_other'];
            $tmp['reconciliation_water_rmb'] = $data['accrual_water_rmb'];
        }
        $merge = array_merge($create, $water);
        foreach ($merge as $v) {
            if (in_array($v, $water)) {
                if (strstr($v, 'other')) {
                    $createData[$v] = $tmp[$v] - $request->num;
                    $update[$v] = (int)$request->num;
                } else {
                    $num = $request->num * $rate[$data->reconciliation_currency];
                    $createData[$v] = $tmp[$v] - $num;
                    $update[$v] = $num;
                }
            } else {
                $createData[$v] = $tmp[$v];
            }
        }
        Reconciliation::create($createData);
        $data->update($update);
        flash(trans('app.调整成功', ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->route('reconciliationAudit', ['source' => Reconciliation::TREASURER, 'product_id' => $data['product_id'], 'scope[startDate]' => $data['billing_cycle_start'], 'scope[endDate]' => $data['billing_cycle_end']]);
    }

    public function wipe(Request $request)
    {
        $data = Reconciliation::findOrFail($request->id);

        foreach ($data as $v) {
            $v->update(['reconciliation_adjustment' => sprintf('-%s', $v['accrual_water_other']), 'reconciliation_rmb_adjustment' => sprintf('-%s', $v['accrual_water_rmb']),
                'reconciliation_type' => 11, 'reconciliation_water_other'  => 0, 'reconciliation_water_rmb' => 0
            , 'reconciliation_user_name' => \Auth::user()->alias, 'reconciliation_time' => date('Y-m-d H:i:s', time()), 'reconciliation_remark' => '一键抹零']);
        }
        flash(trans('crm.抹零成功', ['value' => trans('crm.对账审核')]), 'success');
        return redirect()->back()->withInput();
    }

    public function pop($type)
    {
        $array = json_decode($type, true);
        return array_pop($array);
    }

    public function detail($id, $source)
    {
        switch (true) {
            case $source == Reconciliation::OPERATION:
                $adjustment = 'operation_adjustment';
                $type = 'operation_type';
                break;
            case $source == Reconciliation::ACCRUAL:
                $adjustment = 'accrual_adjustment';
                $type = 'accrual_type';
                break;
            case $source == Reconciliation::RECONCILIATION:
                $adjustment = 'reconciliation_adjustment';
                $type = 'reconciliation_type';
                break;
        }
        $sql = "
            SELECT 
                {$type},
                {$adjustment}
            FROM cmr_reconciliation AS a 
            WHERE a.id = {$id}
            GROUP BY {$type}
        ";
        $ret = \DB::select($sql);
        $diff = Difference::getList();

        $data = [];
        $type = json_decode($ret[0]->reconciliation_type, true);
        $adjustment = json_decode($ret[0]->reconciliation_adjustment, true);
        foreach ($type as $k => $v) {
            $data[$k]['type'] = $diff[$v];
            $data[$k]['adjustment'] = $adjustment[$k];
        }

        return $data;
    }
}
