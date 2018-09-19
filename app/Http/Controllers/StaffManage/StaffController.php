<?php

/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/12
 * Time: 14:35
 *
 */
namespace App\Http\Controllers\StaffManage;

use App\Http\Components\ScopeStaff\StaffScope;
use App\Http\Controllers\Attendance\AttController;
use App\Models\StaffManage\Firm;
use App\Models\Sys\Dept;
use App\Models\Sys\Job;
use App\Models\Sys\School;
use App\Models\UserExt;
use App\User;
use Illuminate\Http\Request;

class StaffController extends AttController
{
    private $_validateRule = [
        'alias' => 'required|max:32|min:2',
        'mobile' => 'required|phone_number|max:11',
        'email' => 'required|unique:users,email|max:32',
        'entry_time' => 'required|date',
        'nature_id' => 'required|integer',
        'hire_id' => 'required|integer',
        'firm_id' => 'required|integer',
        'dept_id' => 'required|integer',
        'job_id' => 'required|integer',
        'job_name' => 'required',
        'leader_id' => 'required|integer',
        'tutor_id' => 'required|integer',
        'friend_id' => 'required|integer',
        'sex' => 'required|in:' . UserExt::SEX_BOY . ',' . UserExt::SEX_GIRL,
        'card_id' => 'required|max:20',
        'card_address' => 'required|max:100',
        'ethnic' => 'required|max:32',
        'birthplace' => 'required|max:20',
        'political' => 'required|max:20',
        'census' => 'required|max:20',
        'family_num' => 'required|integer',
        'marital_status' => 'required|integer',
        'blood_type' => 'required|integer',
        'genus_id' => 'required|integer',
        'constellation_id' => 'required|integer',
        'height' => 'required|max:3',
        'weight' => 'required|max:3',
        'qq' => 'required|max:20',
        'live_address' => 'required|max:100',
        'urgent_name' => 'required|max:20',
        'urgent_bind' => 'required|max:20',
        'urgent_tel' => 'required|max:11',
        'education_id' => 'required|integer',
        'school_id' => 'required|integer',
        'graduation_time' => 'required|date',
        'specialty' => 'required|max:20',
        'degree' => 'required|max:20',
    ];

    protected $scopeClass = StaffScope::class;
    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'staff-manage.staff.scope';

        $data = User::leftJoin('users_ext', 'users.user_id', '=', 'users_ext.user_id')
            ->whereRaw($scope->getWhere())
            ->paginate(50);
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $title = trans('staff.员工列表');
        return view('staff-manage.staff.index', compact('title', 'data',  'job', 'dept', 'scope'));
    }

    public function edit($id)
    {
        $user = User::with('userExt')->findOrFail($id);
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $school = School::getSchoolList();
        $firm = Firm::getFirmList();
        $users = User::getUsernameAliasList();
        $title = trans('staff.员工信息');
        $userIds = [];

        return view('staff-manage.staff.edit-ext', compact('title', 'user', 'job', 'dept', 'userExt', 'school', 'firm', 'users', 'userIds'));
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, $this->_validateRule);

        $data = $request->all();
        $user = User::with('userExt')->findOrFail($id);

        $user->update($data);
        $user->userExt->update($data);

        flash(trans('app.编辑成功', ['value' => trans('staff.员工信息')]), 'success');

        return redirect()->route('staff.list');
    }

}