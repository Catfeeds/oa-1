<?php

namespace App\Http\Controllers;

use App\Models\Version;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    private $_validateRule = [
        'title' => 'required|max:32',
        'content' => 'required',
    ];

    public function index()
    {
       $data = Version::orderBy('created_at', 'desc')->paginate();
        $title = trans('app.版本列表');
        return view('version.index', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加', ['value' => trans('app.版本')]);
        return view('version.edit', compact('title'));

    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        Version::create($request->all());

        flash(trans('app.添加成功', ['value' => trans('app.版本')]), 'success');

        return redirect()->route('version');
    }

    public function edit($id)
    {
        $ver = Version::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.版本')]);
        return view('version.edit', compact('title', 'ver'));
    }

    public function update(Request $request, $id)
    {
        $item = Version::findOrFail($id);
        $this->validate($request, $this->_validateRule);
        $item->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.版本')]), 'success');
        return redirect()->route('version');
    }
}
