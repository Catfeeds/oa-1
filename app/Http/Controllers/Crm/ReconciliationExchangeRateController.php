<?php

namespace App\Http\Controllers\Crm;

use App\Http\Components\Helpers\CrmHelper;
use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Http\Components\ScopeCrm\ExchangeRate as Scope;
use App\Models\Crm\ExchangeRate;
use App\Models\Crm\Reconciliation;
use Illuminate\Http\Request;

class ReconciliationExchangeRateController extends Controller
{
    protected $scopeClass = Scope::class;

    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'crm.reconciliation-exchange-rate.scope';
        $scope->disableDates();

        $billing = Reconciliation::getBilling();
        $currency = CrmHelper::addEmptyToArray('全部货币类型', config('currency.currency_type'));

        $data = ExchangeRate::whereRaw($scope->getWhere())->paginate();
        $title = trans('crm.货币汇率管理');

        return view('crm.reconciliation-exchange-rate.index', compact('title', 'scope', 'data', 'billing', 'currency'));
    }

    /*public function create()
    {
        $billing = Reconciliation::getBilling();
        $currency = config('currency.currency_type');

        $title = trans('app.添加', ['value' => trans('crm.货币汇率管理')]);
        return view('crm.reconciliation-exchange-rate.edit', compact('title', 'billing', 'currency'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['currency' => "required|unique:cmr_exchange_rate,currency,null,id,billing_cycle,{$request->billing_cycle}",]);
        ExchangeRate::create($request->all());

        flash(trans('app.添加成功', ['value' => trans('crm.货币汇率管理')]), 'success');
        return redirect()->route('reconciliationExchangeRate');
    }*/

    public function edit($id)
    {
        $billing = Reconciliation::getBilling();
        $currency = config('currency.currency_type');

        $data = ExchangeRate::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('crm.货币汇率管理')]);
        return view('crm.reconciliation-exchange-rate.edit', compact('title', 'data', 'billing', 'currency'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, ['currency' => "required|unique:cmr_exchange_rate,currency,{$id},id,billing_cycle,{$request->billing_cycle}",]);
        $data = ExchangeRate::findOrFail($id);
        $data->update(array_merge($request->all(), ['type' => ExchangeRate::EDITED]));

        flash(trans('app.编辑成功', ['value' => trans('crm.货币汇率管理')]), 'success');
        return redirect()->route('reconciliationExchangeRate');
    }

    public function conversion(Request $request)
    {
        $ret = Reconciliation::where(['billing_cycle' => $request->billing])->get();
        $rate = ExchangeRate::getList($request->billing);
        try{
            foreach ($ret as $v){
                $v->update(['backstage_water_rmb' => $v['backstage_water_other'] / $rate[$v['reconciliation_currency']]]);
            }
            return ['message' => '货币汇率转化成功！'];
        }catch (\Exception $e){
            \Log::error('货币汇率转化错误：'.$e->getMessage());
            return ['message' => '货币汇率转化错误!请通知管理员处理'];
        }
    }

}
