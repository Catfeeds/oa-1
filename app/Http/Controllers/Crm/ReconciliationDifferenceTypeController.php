<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Difference;
use App\Components\Helper\GeneralScope as Scope;
use Illuminate\Http\Request;

class ReconciliationDifferenceTypeController extends Controller
{
    protected $scopeClass = Scope::class;

    public function index()
    {
        $scope = $this->scope;
        $scope->disableDates();
        $data = Difference::paginate();
        $title = trans('crm.差异类管理');

        return view('crm.reconciliation-difference-type.index', compact('title', 'scope', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加', ['value' => trans('crm.差异类管理')]);
        return view('crm.reconciliation-difference-type.edit', compact('title'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['type_name' => "required|unique:cmr_reconciliation_difference_type,type_name,null,id",]);
        Difference::create($request->all());

        flash(trans('app.添加成功', ['value' => trans('crm.差异类管理')]), 'success');
        return redirect()->route('reconciliationDifferenceType');
    }

    public function edit($id)
    {
        $data = Difference::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('crm.差异类管理')]);
        return view('crm.reconciliation-difference-type.edit', compact('title', 'data'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, ['type_name' => "required|unique:cmr_reconciliation_difference_type,type_name,{$id},id",]);
        $data = Difference::findOrFail($id);
        $data->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('crm.差异类管理')]), 'success');
        return redirect()->route('reconciliationDifferenceType');
    }

}
