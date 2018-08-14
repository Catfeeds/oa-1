<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Proportion;
use App\Models\Crm\Product;
use App\Components\Helper\GeneralScope as Scope;
use Illuminate\Http\Request;

class ReconciliationProportionController extends Controller
{
    protected $scopeClass = Scope::class;

    public function index()
    {
        $scope = $this->scope;
        $product = Product::getList();
        $pid = \Request::get('pid', key($product));
        $data = Proportion::where(['product_id' => $pid])->whereBetween('billing_cycle', [$scope->startTimestamp, $scope->endTimestamp])->paginate(100);
        $title = trans('crm.分成比例管理');

        return view('crm.reconciliation-proportion.index', compact('title', 'scope', 'data', 'pid', 'product'));
    }

    public function edit($id, $pid)
    {
        $data = Proportion::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('crm.分成比例管理')]);
        return view('crm.reconciliation-proportion.edit', compact('title', 'data'));
    }

    public function update(Request $request, $id, $pid)
    {
        $this->validate($request, [
            'channel_rate' => 'int|required',
            'first_division' => 'int|required',
            'second_division' => 'int|required',
        ]);
        foreach ($request->all() as $k => $v){
            if (in_array($k, ['channel_rate','first_division','second_division'])){
                $request[$k] = $request[$k]/100;
            }
        }
        $data = Proportion::findOrFail($id);
        $data->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('crm.分成比例管理')]), 'success');
        return redirect()->route('reconciliationProportion', ['pid' => $pid]);
    }

}
