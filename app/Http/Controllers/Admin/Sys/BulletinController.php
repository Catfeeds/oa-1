<?php

namespace App\Http\Controllers\Admin\Sys;

use App\Models\Sys\Bulletin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BulletinController extends Controller
{
    private $_validateRule = [
        'content'    => 'required',
    ];

    public function index()
    {
        $title = '公告栏';
        $data = Bulletin::orderBy('created_at', 'desc')->paginate(30);
        return view('admin.sys.bulletin', compact('title', 'data'));
    }

    public function edit($id)
    {
        $title = '公告栏编辑';
        $data = Bulletin::find($id);
        return view('admin.sys.bulletin-edit', compact('title', 'data'));
    }

    public function create()
    {
        $title = '公告栏创建';
        return view('admin.sys.bulletin-edit', compact('title'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);
        $bltContent = $request->except('_token');
        $bltContent['send_user'] = \Auth::user()->alias;
        Bulletin::create($bltContent);
        flash('添加成功', 'success');
        return redirect()->back();
    }

    public function update($id, Request $request)
    {
        $this->validate($request, $this->_validateRule);
        $bltContent = $request->except('_token');
        $bltContent['send_user'] = \Auth::user()->alias;
        Bulletin::find($id)->update($bltContent) ? flash('修改成功', 'success') : flash('修改失败', 'danger');
        return redirect($request->url());
    }

    public function changeShow(Request $request) {
        try{
            Bulletin::find($request->input()['id'])->update(['show' => $request->input()['show']]);
        }catch (\Exception $exception) {
            return 'error';
        }
        return 'success';
    }
}
