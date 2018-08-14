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

}
