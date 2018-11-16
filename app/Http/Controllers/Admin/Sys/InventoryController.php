<?php

namespace App\Http\Controllers\Admin\Sys;

use App\Components\Helper\FileHelper;
use App\Models\Sys\Inventory;
use EasyWeChat\Kernel\Exceptions\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public $_validate = [
        'inv_remain' => 'required|numeric|min:0',
    ];

    public function inventoryList()
    {
        $title = '资质库存列表';
        $data = Inventory::get();
        return view('admin.sys.inventory-list', compact('title', 'data'));
    }

    public function create()
    {
        $title = '添加资质借用';
        return view('admin.sys.inventory-edit', compact('title'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validate);
        $data = $request->except('_token');
        Inventory::create($data);
        flash('添加成功', 'success');
        return redirect()->route('inventory.list');
    }

    public function edit(Inventory $id)
    {
        $title = '修改资质借用';
        $data = $id;
        return view('admin.sys.inventory-edit', compact('title', 'data'));
    }

    public function update(Inventory $id, Request $request)
    {
        $this->validate($request, $this->_validate);
        $inventory = $id;
        $data = $request->except('_token');
        $inventory->update($data);
        flash('修改成功', 'success');
        return redirect()->route('inventory.list');
    }

    public function upload()
    {
        $title = '批量添加';
        return view('admin.sys.inventory-upload', compact('title'));
    }

    public function excel(Request $request)
    {
        $filePath = $fileName = '';

        if ($request->hasFile('upload') && $request->file('upload')->isValid()) {
            $time = date('Ymd', time());
            $uploadPath = 'app/inventory/' . $time;
            $fileName = 'inventory' . '_' . time() . rand(100000, 999999);
            $fileName = FileHelper::uploadExcel($request->file('upload'), $fileName, $uploadPath);
            $filePath = $uploadPath . '/' . $fileName;
        }
        $this->generate(storage_path($filePath)) ? flash('生成成功', 'success') : flash('生成失败，生成文件有误，无法解析!', 'danger');
        return redirect()->route('inventory.list');
    }

    public function generate($filePath)
    {
        \DB::beginTransaction();
        try {
            \Excel::load($filePath, function ($reader) {
                $reader = $reader->getSheet(0);
                $reader = $reader->toarray();
                $data = [];
                foreach ($reader as $key => $value) {
                    if ($key === 0) continue;
                    list($d['type'], $d['name'], $d['inv_remain'], $d['company'], $d['content'], $d['description']) = $value;
                    $d['is_show'] = $value[6] === '是';
                    $d['is_annex'] = $value[7] === '是';
                    $d['created_at'] = $d['updated_at'] = date('Y-m-d H:i:s');
                    $data[$key] = $d;
                }
                \DB::table('material_inventory')->insert($data);
            });
        }catch (\Exception $exception) {
            \DB::rollBack();
            return false;
        }
        \DB::commit();
        return true;
    }
}
