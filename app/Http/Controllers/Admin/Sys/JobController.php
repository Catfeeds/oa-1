<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/8/1
 * Time: 10:20
 * 岗位管理控制
 */

namespace App\Http\Controllers\Admin\Sys;

use App\Http\Controllers\Controller;
use App\Models\Sys\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected $redirectTo = '/admin/sys/job';

    private $_validateRule = [
        'job' => 'required|unique:users_job,job|max:50',
    ];

    public function index()
    {
        $data = Job::paginate();
        $title = trans('app.岗位列表');
        return view('admin.sys.job', compact('title', 'data'));
    }

    public function create()
    {
        $title = trans('app.添加岗位');
        return view('admin.sys.job-edit', compact('title'));
    }

    public function edit($id)
    {
        $job = Job::findOrFail($id);
        $title = trans('app.编辑', ['value' => trans('app.岗位')]);
        return view('admin.sys.job-edit', compact('title', 'job'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);

        Job::create($request->all());
        flash(trans('app.添加成功', ['value' => trans('app.岗位')]), 'success');

        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        $this->validate($request, array_merge($this->_validateRule, [
            'job' => 'required|max:50|unique:users_job,job,' . $job->job_id.',job_id',
        ]));

        $job->update($request->all());

        flash(trans('app.编辑成功', ['value' => trans('app.岗位')]), 'success');
        return redirect($this->redirectTo);
    }
}