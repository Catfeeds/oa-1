<?php

namespace App\Http\Controllers\Crm;

use App\Http\Components\Helpers\CrmHelper;
use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Product;
use App\Http\Components\ScopeCrm\Reconciliation as Scope;
use Illuminate\Http\Request;
use DB;

class ReconciliationScheduleController extends Controller
{
    protected $scopeClass = Scope::class;
    protected $date = [
        1 => '小于1个月',
        2 => '1-2月',
        3 => '2-3月',
        4 => '3-6月',
        5 => '6个月以上',
    ];

    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'crm.reconciliation-audit.scope';
        $products = Product::getList();
        $pid = \Request::get('product_id', key($products));
        $header = ['客户名称', '信期类型', '信期(月)', '对账流水', '已对账流水', '未对账流水', '是否逾期', '开票完成率', '已开票流水', '已收款', '待收款', '明细'];

        $title = trans('crm.对账进度跟踪');

        return view('crm.reconciliation-schedule.index', compact('title', 'scope', 'products', 'pid', 'header'));
    }

    public function data()
    {
        $pid = \Request::get('product_id');
        $scope = $this->scope;
        $billing_cycle_start = date('Y-m', strtotime($scope->startTimestamp));
        $billing_cycle_end = date('Y-m', strtotime($scope->endTimestamp));
        $sql = "
                    SELECT 
                        a.client AS client,
                        a.period_name AS period_name,
                        MAX(a.period) AS period,
                        SUM(IF(review_type < 8,reconciliation_water_rmb,0)) AS first_rmb,
                        SUM(IF(review_type = 8,reconciliation_water_rmb,0)) AS second_rmb,
                        COUNT(*) AS total,
                        COUNT(IF(billing_type = 2,1,NULL)) AS num,
                        SUM(IF(billing_type = 2,reconciliation_water_rmb,0)) AS invoices_rmb,
                        SUM(IF(payback_type = 2,reconciliation_water_rmb,0)) AS payback_rmb
                    FROM cmr_reconciliation AS a 
                    WHERE a.product_id = {$pid} AND a.billing_cycle BETWEEN '{$billing_cycle_start}' AND '{$billing_cycle_end}'
                    GROUP BY client,period_name
                ";
        $ret = \DB::select($sql);
        $data = [];
        foreach ($ret as $k => $v) {
            $v = (array)$v;
            $data[$k]['client'] = $v['client'];
            $data[$k]['period_name'] = $v['period_name'];
            $data[$k]['period'] = $v['period'];
            $data[$k]['rmb'] = $v['first_rmb'] + $v['second_rmb'];
            $data[$k]['second_rmb'] = $v['second_rmb'];
            $data[$k]['first_rmb'] = $v['first_rmb'];
            $data[$k]['overdue'] = $v['first_rmb'] == 0 ? '否' : '是';
            $data[$k]['billing_rate'] = $v['num'] ? CrmHelper::percentage($v['num'] / $v['total']) : '0%';
            $data[$k]['invoices_rmb'] = $v['invoices_rmb'];
            $data[$k]['payback_rmb'] = $v['payback_rmb'];
            $data[$k]['not_payback_rmb'] = $v['invoices_rmb'] - $v['payback_rmb'];
            $data[$k]['detail'] = '<a class="fa fa-eye fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="查看差异明细" 
                data-url="' . route('reconciliationSchedule.detail', ['client' => $v['client'], 'period_name' => $v['period_name'], 'product_id' => $pid, 'billing_cycle_start' => $billing_cycle_start, 'billing_cycle_end' => $billing_cycle_end]) . '"></a>';
            $data[$k] = array_values($data[$k]);
        }
        return $this->response($data);
    }

    public function detail(Request $request)
    {
        $end = explode('-', $request->billing_cycle_end);

        $case = '';
        $case .= "WHEN billing_cycle = '{$this->processDate($end[0], $end[1], 0)}' THEN 1 \n";
        $case .= "WHEN billing_cycle = '{$this->processDate($end[0], $end[1], 1)}' THEN 2 \n";
        $case .= "WHEN billing_cycle = '{$this->processDate($end[0], $end[1], 2)}' THEN 3 \n";
        $case .= "WHEN billing_cycle <= '{$this->processDate($end[0], $end[1], 3)}' AND billing_cycle >= '{$this->processDate($end[0], $end[1], 5)}' THEN 4 \n";
        $case .= "WHEN billing_cycle < '{$this->processDate($end[0], $end[1], 5)}' THEN 5 \n";
        $sql ="
            SELECT                
                CASE
                    {$case}
                END AS typ,
                MAX(a.period) AS period,
                SUM(IF(review_type < 8,reconciliation_water_rmb,0)) AS first_rmb,
                SUM(IF(review_type = 8,reconciliation_water_rmb,0)) AS second_rmb,
                COUNT(*) AS total,
                COUNT(IF(billing_type = 2,1,NULL)) AS num,
                SUM(IF(billing_type = 2,reconciliation_water_rmb,0)) AS invoices_rmb,
                SUM(IF(payback_type = 2,reconciliation_water_rmb,0)) AS payback_rmb
            FROM cmr_reconciliation AS a 
            WHERE a.product_id = {$request->product_id} AND a.billing_cycle BETWEEN '{$request->billing_cycle_start}' AND '{$request->billing_cycle_end}'
            AND a.client = '{$request->client}' AND a.period_name = '{$request->period_name}'
            GROUP BY typ
        ";
        $ret = \DB::select($sql);
        $data = [];
        foreach ($ret as $k => $v) {
            $v = (array)$v;
            switch (true){
                case $v['typ'] == 5:
                    $term = true;
                    break;
                case $v['typ'] == 4 && $v['period'] < 6:
                    $term = true;
                    break;
                case $v['typ'] > $v['period']:
                    $term = true;
                    break;
                default:
                    $term = false;
                    break;
            }
            $data[$k]['typ'] = $this->date[$v['typ']];
            $data[$k]['rmb'] = $v['first_rmb'] + $v['second_rmb'];
            $data[$k]['second_rmb'] = $v['second_rmb'];
            $data[$k]['first_rmb'] = $v['first_rmb'];
            $data[$k]['overdue'] = $v['first_rmb'] != 0 && $term ? '是' : '否';
            $data[$k]['billing_rate'] = $v['num'] ? CrmHelper::percentage($v['num'] / $v['total']) : '0%';
            $data[$k]['invoices_rmb'] = $v['invoices_rmb'];
            $data[$k]['payback_rmb'] = $v['payback_rmb'];
            $data[$k]['not_payback_rmb'] = $v['invoices_rmb'] - $v['payback_rmb'];
        }
        return $data;
    }

    public function processDate($year, $month, $num)
    {
        $month = $month - $num;
        if ($month < 0) {
            return sprintf('%d-%02d', $year - 1, 12 + $month);
        } else {
            return sprintf('%d-%02d', $year, $month);
        }
    }
}
