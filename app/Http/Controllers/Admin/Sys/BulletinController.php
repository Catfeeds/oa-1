<?php

namespace App\Http\Controllers\Admin\Sys;

use App\Models\Sys\Bulletin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BulletinController extends Controller
{
    public function index()
    {
        $title = '公告栏';
        return view('admin.sys.bulletin', compact('title'));
    }

    public function edit($id)
    {
        $title = '公告栏编辑';
        return view('admin.sys.bulletin-edit', compact('title'));
    }

    public function create()
    {
        $title = '公告栏创建';
        return view('admin.sys.bulletin-edit', compact('title'));
    }

    public function store(Request $request)
    {
        $bltContent = $request->except('_token');
        $bltContent['send_user'] = \Auth::user()->alias;
        $bltContent['content'] = base64_encode($bltContent['content']);
        Bulletin::create($bltContent);
        return redirect()->back();
    }
}
