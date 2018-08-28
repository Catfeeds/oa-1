<?php

namespace App\Http\Controllers\Crm;

use App\Http\Components\Helpers\CrmHelper;
use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Difference;
use App\Models\Crm\Principal;
use App\Models\Crm\Product;
use App\Models\Crm\Reconciliation;
use App\Http\Components\ScopeCrm\Reconciliation as Scope;
use Illuminate\Http\Request;
use DB;

class ReconciliationPoolController extends Controller
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

        return view('crm.reconciliation-pool.index', compact('title', 'scope', 'review', 'source', 'products', 'pid', 'header', 'status', 'limitPost', 'columns'));
    }

    public function data()
    {
        $pid = \Request::get('product_id');
        $source = \Request::get('source');
        $scope = $this->scope;
        $billing_cycle = date('Y-m', strtotime($scope->startTimestamp));
        switch (true) {
            case $source == Reconciliation::OPERATION:
                $sql = "
                    SELECT 
                        a.client AS client,
                        a.period_name AS period_name,
                        SUM(backstage_water_rmb) AS first_rmb,
                        SUM(operation_water_rmb) AS second_rmb
                    FROM cmr_reconciliation AS a 
                    WHERE a.product_id = {$pid} AND a.billing_cycle = '{$billing_cycle}'
                    GROUP BY client,period_name
                ";
                $ret = \DB::select($sql);
                break;
            case $source == Reconciliation::ACCRUAL:
                $sql = "
                    SELECT 
                        a.client AS client,
                        a.period_name AS period_name,
                        SUM(operation_water_rmb) AS first_rmb,
                        SUM(accrual_water_rmb) AS second_rmb
                    FROM cmr_reconciliation AS a 
                    WHERE a.product_id = {$pid} AND a.billing_cycle = '{$billing_cycle}'
                    GROUP BY client,period_name
                ";
                $ret = \DB::select($sql);
                break;
            case $source == Reconciliation::RECONCILIATION:
                $sql = "
                    SELECT 
                        a.client AS client,
                        a.period_name AS period_name,
                        SUM(accrual_water_rmb) AS accrual_water_rmb,
                        SUM(IF(review_type < 8,reconciliation_water_rmb,0)) AS first_rmb,
                        SUM(IF(review_type = 8,reconciliation_water_rmb,0)) AS second_rmb,
                        COUNT(*) AS total,
                        COUNT(IF(billing_type = 2,1,NULL)) AS num,
                        SUM(IF(billing_type = 2,reconciliation_water_rmb,0)) AS invoices_rmb,
                        SUM(IF(payback_type = 2,reconciliation_water_rmb,0)) AS payback_rmb
                    FROM cmr_reconciliation AS a 
                    WHERE a.product_id = {$pid} AND a.billing_cycle = '{$billing_cycle}'
                    GROUP BY client,period_name
                ";
                $ret = \DB::select($sql);
                break;
        }
        $data = [];
        foreach ($ret as $k => $v) {
            if ($source == Reconciliation::RECONCILIATION) {
                $v = (array)$v;
                $data[$k]['client'] = $v['client'];
                $data[$k]['period_name'] = $v['period_name'];
                $data[$k]['first_rmb'] = $v['first_rmb'];
                $data[$k]['second_rmb'] = $v['second_rmb'];
                $data[$k]['diff_amount'] = $v['accrual_water_rmb'] - $v['first_rmb'] - $v['second_rmb'];
                $data[$k]['diff_rate'] = $data[$k]['diff_amount'] ? CrmHelper::percentage($data[$k]['diff_amount'] / ( $v['accrual_water_rmb'] - $v['first_rmb'])) : '0%';
                $data[$k]['re_rate'] = $v['first_rmb'] != 0 ? CrmHelper::percentage(1 - $v['first_rmb']/$v['accrual_water_rmb']) : '100%';
                $data[$k]['billing_rate'] = $v['num'] ? CrmHelper::percentage($v['num']/$v['total']) : '0%';
                $data[$k]['invoices_rmb'] = $v['invoices_rmb'];
                $data[$k]['payback_rmb'] = $v['payback_rmb'];
                $data[$k]['not_payback_rmb'] = $v['invoices_rmb'] - $v['payback_rmb'];
                $data[$k]['detail'] = '<a class="fa fa-eye fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="查看差异明细" 
                data-url="' . route('reconciliationPool.detail', ['client' => $v['client'], 'period_name' => $v['period_name'], 'product_id' => $pid, 'billing_cycle' => $billing_cycle, 'source' => $source]) . '"></a>';
                $data[$k] = array_values($data[$k]);
            } else {
                $v = (array)$v;
                $v['diff_amount'] = $v['first_rmb'] - $v['second_rmb'];
                $v['diff_rate'] = $v['diff_amount'] ? CrmHelper::percentage($v['diff_amount'] / $v['first_rmb']) : '0%';
                $v['detail'] = '<a class="fa fa-eye fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="查看差异明细" 
                data-url="' . route('reconciliationPool.detail', ['client' => $v['client'], 'period_name' => $v['period_name'], 'product_id' => $pid, 'billing_cycle' => $billing_cycle, 'source' => $source]) . '"></a>';
                $data[] = array_values($v);
            }
        }
        return $this->response($data);
    }

    public function header($source)
    {
        switch (true) {
            case $source == Reconciliation::OPERATION:
                $header = ['客户名称', '信期类型', '后台流水', '运营流水', '差异额', '差异率', '明细'];
                break;
            case $source == Reconciliation::ACCRUAL:
                $header = ['客户名称', '信期类型', '后台流水', '运营流水', '差异额', '差异率', '明细'];
                break;
            case $source == Reconciliation::RECONCILIATION:
                $header = ['客户名称', '信期类型', '未对账流水', '已对账流水', '对账差异额', '差异率', '对账率', '开票完成率', '已开票流水', '已收款', '待收款', '明细'];
                break;
        }

        return $header;
    }

    public function detail(Request $request)
    {
        switch (true){
            case $request->source == Reconciliation::OPERATION:
                $sql = "
                    SELECT 
                        operation_type,
                        SUM(operation_rmb_adjustment) AS operation_rmb_adjustment
                    FROM cmr_reconciliation AS a 
                    WHERE a.product_id = {$request->product_id} AND a.billing_cycle = '{$request->billing_cycle}'
                    AND a.client = '{$request->client}' AND a.period_name = '{$request->period_name}'
                    GROUP BY operation_type
                ";
                $ret = \DB::select($sql);
                break;
            case $request->source == Reconciliation::ACCRUAL:
                $sql = "
                    SELECT 
                        accrual_type,
                        SUM(accrual_rmb_adjustment) AS accrual_rmb_adjustment
                    FROM cmr_reconciliation AS a 
                    WHERE a.product_id = {$request->product_id} AND a.billing_cycle = '{$request->billing_cycle}'
                    AND a.client = '{$request->client}' AND a.period_name = '{$request->period_name}'
                    GROUP BY accrual_type
                ";
                $ret = \DB::select($sql);
                break;
            case $request->source == Reconciliation::RECONCILIATION:
                $sql = "
                    SELECT 
                        reconciliation_type,
                        SUM(reconciliation_rmb_adjustment) AS reconciliation_rmb_adjustment
                    FROM cmr_reconciliation AS a 
                    WHERE a.product_id = {$request->product_id} AND a.billing_cycle = '{$request->billing_cycle}'
                    AND a.client = '{$request->client}' AND a.period_name = '{$request->period_name}'
                    GROUP BY reconciliation_rmb_adjustment
                ";
                $ret = \DB::select($sql);
                break;
        }
        $diff = Difference::getList();
        $sum = array_sum(array_column($ret, 'operation_rmb_adjustment'));
        $data = [];
        foreach ($ret as $k => $v){
            $v = (array)$v;
            if (isset($diff[$v['operation_type']])){
                $data[$k]['type'] = $diff[$v['operation_type']];
                $data[$k]['adjustment'] = sprintf('%s|%s',$v['operation_rmb_adjustment'], CrmHelper::percentage($v['operation_rmb_adjustment']/$sum));
            }
        }

        return $data;
    }
}
