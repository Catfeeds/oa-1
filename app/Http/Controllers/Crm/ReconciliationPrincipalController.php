<?php

namespace App\Http\Controllers\Crm;

use App\Http\Components\Helpers\CrmHelper;
use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Principal;
use App\Models\Crm\Product;
use App\Http\Components\ScopeCrm\Principal as Scope;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReconciliationPrincipalController extends Controller
{
    protected $scopeClass = Scope::class;
    protected $_validateRule = [
        'ops' => 'int|required|min:1',
        'opd' => 'int|required|min:1',
        'fac' => 'int|required|min:1',
        'treasurer' => 'int|required|min:1',
        'frc' => 'int|required|min:1',
        'fsr' => 'int|required|min:1',
    ];

    public function index()
    {
        $scope = $this->scope;
        $scope->disableDates();
        $scope->block = 'crm.reconciliation-principal.scope';
        $columns = [
            ['title' => '游戏ID'],
            ['title' => '游戏名'],
            ['title' => '运营专员'],
            ['title' => '运营主管'],
            ['title' => '财务计提专员'],
            ['title' => '财务计提主管'],
            ['title' => '财务对账专员'],
            ['title' => '财务对账主管'],
            ['title' => '操作'],
        ];

        $products = CrmHelper::addEmptyToArray('请选择游戏', Product::getList());
        $title = '对账审核';

        return view('crm.reconciliation-principal.index', compact('title', 'scope', 'columns', 'products'));
    }

    public function data()
    {
        $scope = $this->scope;
        $sql = "
            SELECT
                product_id,
                job_id,
                principal_id
            FROM cmr_reconciliation_principal 
            WHERE {$scope->getWhere()}
        ";
        $ret = DB::select($sql);
        $product = Product::getList();
        $user = User::getAliasList();
        $tmp = $data = [];
        foreach ($ret as $k => $v) {
            $v = (array)$v;
            $tmp[$v['product_id']]['product_id'] = $v['product_id'];
            $tmp[$v['product_id']]['name'] = $product[$v['product_id']];
            $tmp[$v['product_id']] = array_merge($tmp[$v['product_id']], $this->allotJob($v['job_id'], $v['principal_id']));
        }
        foreach ($tmp as $v){
            foreach ($v as $key => $val){
                if (in_array($key, ['product_id', 'name'])){
                    continue;
                }
                $v[$key] = $user[$val] ?? '';
            }
            $data[] = array_values($v);
        }
        return $this->response($data);
    }

    public function edit()
    {
        $pid = \Request::get('pid');
        if (!isset($pid)) {
            flash(trans('crm.非法操作'), 'error');
            return redirect()->route('reconciliationPrincipal');
        }
        $sql = "
            SELECT
                product_id,
                job_id,
                principal_id
            FROM cmr_reconciliation_principal 
            WHERE product_id = {$pid}
        ";
        $ret = DB::select($sql);
        $data = [];
        foreach ($ret as $k => $v) {
            $v = (array)$v;
            $data['product_id'] = $v['product_id'];
            $data = array_merge($data, $this->allotJob($v['job_id'], $v['principal_id']));
        }
        $user = CrmHelper::addEmptyToArray('请选择负责人', User::getAliasList());
        $title = trans('app.编辑', ['value' => trans('crm.对账负责人管理')]);
        return view('crm.reconciliation-principal.edit', compact('title', 'data', 'user'));
    }

    public function update(Request $request)
    {
        $this->validate($request, $this->_validateRule);
        foreach (Principal::JOB as $k => $v){
            $sql = " update cmr_reconciliation_principal set principal_id = {$request[$v]} WHERE product_id = {$request['product_id']} AND job_id = {$k} ";
            DB::update($sql);
            unset($sql);
        }

        flash(trans('app.编辑成功', ['value' => trans('crm.对账负责人管理')]), 'success');
        return redirect()->route('reconciliationPrincipal');
    }

    public function allotJob($jobId, $principalId)
    {
        $data = [];
        switch (true){
            case $jobId == Principal::OPS :
                $data['ops'] = $principalId;
                break;
            case $jobId == Principal::OPD :
                $data['opd'] = $principalId;
                break;
            case $jobId == Principal::FAC :
                $data['fac'] = $principalId;
                break;
            case $jobId == Principal::TREASURER :
                $data['treasurer'] = $principalId;
                break;
            case $jobId == Principal::FRC :
                $data['frc'] = $principalId;
                break;
            case $jobId == Principal::FSR :
                $data['fsr'] = $principalId;
                break;
        }
        return $data;
}

}
