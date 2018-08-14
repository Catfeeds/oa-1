<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Difference;
use App\Models\Crm\Product;
use App\Components\Helper\GeneralScope as Scope;
use Illuminate\Http\Request;

class ReconciliationDifferenceTypeController extends Controller
{
    protected $scopeClass = Scope::class;

    public function index()
    {
        $scope = $this->scope;
        $scope->disableDates();
        $product = Product::getList();
        $pid = \Request::get('pid', key($product));
        $data = Difference::where(['product_id' => $pid])->paginate();
        $title = trans('crm.差异类管理');

        return view('crm.reconciliation-difference-type.index', compact('title', 'scope', 'data', 'pid', 'product'));
    }

    public function create($pid)
    {
        $title = trans('app.添加', ['value' => trans('crm.差异类管理')]);
        return view('crm.reconciliation-difference-type.edit', compact('title'));
    }

    public function store(Request $request, $pid)
    {
        $this->validate($request, ['type_name' => "required|unique:cmr_reconciliation_difference_type,type_name,null,id,product_id,{$pid}",]);
        Difference::create(array_merge($request->all(), ['product_id' => $pid]));

        flash(trans('app.添加成功', ['value' => trans('crm.差异类管理')]), 'success');
        return redirect()->route('reconciliationDifferenceType', ['pid' => $pid]);
    }

    public function edit($id, $pid)
    {
        $data = Difference::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('crm.差异类管理')]);
        return view('crm.reconciliation-difference-type.edit', compact('title', 'data'));
    }

    public function update(Request $request, $id, $pid)
    {
        $this->validate($request, ['type_name' => "required|unique:cmr_reconciliation_difference_type,type_name,{$id},id,product_id,{$pid}",]);
        $data = Difference::findOrFail($id);
        $data->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('crm.差异类管理')]), 'success');
        return redirect()->route('reconciliationDifferenceType', ['pid' => $pid]);
    }

}
