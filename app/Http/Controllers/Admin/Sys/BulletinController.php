<?php

namespace App\Http\Controllers\Admin\Sys;

use App\Models\Sys\Bulletin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BulletinController extends Controller
{
    private $_validateRule = [
        'valid_time' => 'required|numeric',
        'weight'     => 'required|numeric|min:0|max:1024',
        'content'    => 'required',
    ];

    public function index()
    {
        $title = '公告栏';
        $data = Bulletin::orderBy(\DB::raw('created_at + valid_time * 24 * 3600'), 'desc')
            ->orderBy('weight', 'desc')
            ->paginate(30, ['id', 'send_user', 'valid_time', 'title', 'weight', 'created_at']);
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
}
