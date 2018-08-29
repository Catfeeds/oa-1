<?php

namespace App\Http\Controllers\Crm;

use App\Http\Components\Helpers\CrmHelper;
use App\Http\Controllers\Crm\CrmController AS Controller;
use App\Models\Crm\Proportion;
use App\Models\Crm\Product;
use App\Components\Helper\GeneralScope as Scope;
use Illuminate\Http\Request;
use Excel;

class ReconciliationProportionController extends Controller
{
    protected $scopeClass = Scope::class;

    public function index()
    {
        $scope = $this->scope;
        $product = Product::getList();
        $pid = \Request::get('pid', key($product));
        $columns = ['ID', '外键ID', '游戏名', '结算周期', '客户', '后台渠道', '渠道费率', '一级分成', '一级分成备注', '二级分成', '二级分成备注', '二级分成条件', '操作人', '录入时间', '更新时间', '操作'];
        $title = trans('crm.分成比例管理');

        return view('crm.reconciliation-proportion.index', compact('title', 'scope', 'columns', 'pid', 'product'));
    }

    public function data()
    {
        $scope = $this->scope;
        $product = Product::getList();
        $pid = \Request::get('pid', key($product));
        $data = Proportion::where(['product_id' => $pid])->whereBetween('billing_cycle', [$scope->startTimestamp, $scope->endTimestamp])->get()->toArray();
        foreach ($data as $k => $v) {
            unset($data[$k]);
            $v['product_id'] = $product[$v['product_id']] ?? '未知'.$v['product_id'];
            $v['channel_rate'] = CrmHelper::percentage($v['channel_rate']);
            $v['first_division'] = CrmHelper::percentage($v['first_division']);
            $v['second_division'] = CrmHelper::percentage($v['second_division']);
            $url = route('reconciliationProportion.edit', ['id' => $v['id'], 'pid' => $pid]);
            $data[$k] = array_values($v) + [15 => sprintf('<a href="%s" target="_self"> <i class="fa fa-cog fa-lg" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"></i> </a>', $url)];
        }
        return $this->response($data);
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
        foreach ($request->all() as $k => $v) {
            if (in_array($k, ['channel_rate', 'first_division', 'second_division'])) {
                $request[$k] = $request[$k] / 100;
            }
        }
        $data = Proportion::findOrFail($id);
        $data->update(array_merge($request->all(), ['user_name' => \Auth::user()->alias]));

        flash(trans('app.编辑成功', ['value' => trans('crm.分成比例管理')]), 'success');
        return redirect()->route('reconciliationProportion', ['pid' => $pid]);
    }

    public function batch(Request $request)
    {
        $pid = $request->get('pid');
        $title = '批量添加';
        return view('crm.reconciliation-proportion.batch', compact('pid', 'title'));
    }

    public function add(Request $request)
    {
        $product_id = $request->get('pid');
        $fileName = $request->file('excel')->getClientOriginalName();
        $request->excel->storeAs('', $fileName, 'public');

        $filePath = 'storage/app/public/';

        Excel::load($filePath . $fileName, function ($reader) use ($product_id) {
            try {
                $reader = $reader->getSheet(0);
                $reader = $reader->toarray();
                if ($reader) {
                    $data = [];
                    foreach ($reader as $key => $value) {
                        if ($value[0] == null || $key == 0) {
                            continue;
                        } else {
                            Proportion::findOrFail($value[0])->update(['ret_id' => $value[1],'channel_rate' => CrmHelper::percentage($value[6], true),
                                'first_division' => CrmHelper::percentage($value[7], true),'first_division_remark' => $value[8],'second_division' => CrmHelper::percentage($value[9], true),
                                'second_division_remark' => $value[10],'second_division_condition' => (int)str_replace(',', '', $value[11]),'user_name' => \Auth::user()->alias]);
                            $data[$key][] = \Auth::user()->alias;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('比例批量添加错误' . $e->getMessage());
            }

        });

        flash('添加成功', 'success');
        return redirect()->route('reconciliationProportion', ['pid' => $product_id]);
    }
}
