<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Principal;
use App\Models\Crm\Product;
use App\Components\Helper\GeneralScope as Scope;
use Illuminate\Http\Request;

class ReconciliationProductController extends Controller
{
    protected $scopeClass = Scope::class;

    public function index()
    {
        $scope = $this->scope;
        $data = Product::paginate();
        $title = trans('crm.游戏列表');

        return view('crm.reconciliation-product.index', compact('title', 'scope', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加', ['value' => trans('crm.游戏列表')]);
        return view('crm.reconciliation-product.edit', compact('title'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['product_id' => 'int|required|unique:cmr_product,product_id']);
        Product::create($request->all());

        foreach (Principal::JOB as $k => $v){
            Principal::create(['product_id' => $request->product_id, 'job_id' => $k]);
        }

        flash(trans('app.添加成功', ['value' => trans('crm.游戏列表')]), 'success');
        return redirect()->route('reconciliationProduct');
    }

    public function edit($id)
    {
        $data = Product::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('crm.游戏列表')]);
        return view('crm.reconciliation-product.edit', compact('title', 'data'));
    }

    public function update(Request $request, $id)
    {
        $data = Product::findOrFail($id);
        $data->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('crm.游戏列表')]), 'success');
        return redirect()->route('reconciliationProduct');
    }

    /*    public function download()
    {
        $scope = $this->scope;
        $pid = \Request::get('pid');
        $source = \Request::get('source');
        $header = $this->header($source);
        $file = storage_path(sprintf('data/export/%s.csv', date('YmdHis') . uniqid()));
        fopen($file, "w");
        $data = Reconciliation::where(['product_id' => $pid])
            ->whereBetween('billing_cycle_start', [$scope->startTimestamp, $scope->endTimestamp])
            ->orderBy('id', 'desc')
            ->get()->toArray();

        foreach ($data as $k => $v) {
            $proportion = Proportion::where(['product_id' => $pid, 'client' => $v['client'], 'backstage_channel' => $v['backstage_channel'], 'billing_cycle' => $v['billing_cycle_end']])->first();
            $tmp = [
                $k + 1,
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
                CrmHelper::dividedInto($proportion->channel_rate, $proportion->first_division, $proportion->second_division, $proportion->second_division_condition, $v['backstage_water_other']),
                CrmHelper::dividedInto($proportion->channel_rate, $proportion->first_division, $proportion->second_division, $proportion->second_division_condition, $v['backstage_water_rmb']),
                $v['operation_adjustment'],
                $v['operation_type'],
                $v['operation_remark'],
                $v['operation_user_name'],
                $v['operation_time'],
                $v['operation_water_other'],
                $v['operation_water_rmb'],
                CrmHelper::dividedInto($proportion->channel_rate, $proportion->first_division, $proportion->second_division, $proportion->second_division_condition, $v['operation_water_other']),
                CrmHelper::dividedInto($proportion->channel_rate, $proportion->first_division, $proportion->second_division, $proportion->second_division_condition, $v['operation_water_rmb']),
                $v['accrual_adjustment'],
                $v['accrual_type'],
                $v['accrual_remark'],
                $v['accrual_user_name'],
                $v['accrual_time'],
                $v['accrual_water_other'],
                $v['accrual_water_rmb'],
                CrmHelper::dividedInto($proportion->channel_rate, $proportion->first_division, $proportion->second_division, $proportion->second_division_condition, $v['accrual_water_other']),
                CrmHelper::dividedInto($proportion->channel_rate, $proportion->first_division, $proportion->second_division, $proportion->second_division_condition, $v['accrual_water_rmb']),
                $v['reconciliation_adjustment'],
                $v['reconciliation_type'],
                $v['reconciliation_remark'],
                $v['reconciliation_user_name'],
                $v['reconciliation_time'],
                $v['reconciliation_water_other'],
                $v['reconciliation_water_rmb'],
                CrmHelper::dividedInto($proportion->channel_rate, $proportion->first_division, $proportion->second_division, $proportion->second_division_condition, $v['reconciliation_water_other']),
                CrmHelper::dividedInto($proportion->channel_rate, $proportion->first_division, $proportion->second_division, $proportion->second_division_condition, $v['reconciliation_water_rmb']),
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
    }*/

}
