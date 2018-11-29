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
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    protected $redirectTo = '/admin/sys/job';

    private $_validateRule = [
        'job' => 'required|unique:users_job,job|max:50',
    ];

    public function index()
    {
        $form['job'] = \Request::get('job');
        $data = Job::where('job', 'LIKE', "%{$form['job']}%")->paginate();
        $title = trans('app.岗位列表');
        return view('admin.sys.job', compact('title', 'data', 'form'));
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

    public function del($id)
    {
        $job = Job::with('users', 'entry')->where(['job_id' => $id])->first();
        if(empty($job->job_id)) {
            flash('删除失败,无效的数据ID!', 'danger');
            return redirect($this->redirectTo);
        }

        if(!empty($job->users->toArray()) || !empty($job->entry->toArray())) {
            flash('删除失败,['.$job->job. ']还有在使用中!', 'danger');
            return redirect($this->redirectTo);
        }

        DB::beginTransaction();
        try{
            Job::where(['job_id' => $job->job_id])->delete();
        } catch (\Exception $ex) {
            DB::rollBack();
            flash('删除失败!', 'danger');
            return redirect($this->redirectTo);
        }
        DB::commit();

        flash(trans('app.删除成功', ['value' => trans('app.岗位')]), 'success');
        return redirect($this->redirectTo);
    }
}