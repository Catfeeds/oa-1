<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Sys\Dept;
use App\Models\Sys\Job;
use App\Models\Sys\School;
use App\Models\UserExt;
use App\User;
use EasyWeChat\Kernel\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Illuminate\Mail\Message;

class UserController extends Controller
{
    protected $redirectTo = '/admin/user';

    private $_validateRule = [
        'alias' => 'required|max:32|min:2',
        'password' => 'required|min:8|alpha_num|confirmed',
        'password_confirmation' => 'min:6',
        'status' => 'required|in:' . User::STATUS_DISABLE . ',' . User::STATUS_ENABLE,
        'role_id' => 'required|numeric|exists:roles,id',
        'mobile' => 'nullable|phone_number',
    ];

    private $_validateRuleExt = [
        'incumbent_num' => 'nullable|numeric',
        'contract_years' => 'nullable|numeric',
        'contract_num' => 'nullable|numeric',
        'age' => 'nullable|numeric',
        'birthplace' => 'max:20',
        'census' => 'max:20',
        'card_id' => 'max:20',
        'card_address' => 'max:100',
        'phone' => 'max:11',
        'qq' => 'max:20',
        'live_address' => 'max:100',
        'urgent_name' => 'max:20',
        'urgent_tel' => 'max:11',
        'salary_card' => 'max:20',
        'sex' => 'in:' . User::STATUS_DISABLE . ',' . User::STATUS_ENABLE,
        'marital_status' => 'in:' . User::STATUS_DISABLE . ',' . User::STATUS_ENABLE,
        'firm_call' => 'in:' . User::STATUS_DISABLE . ',' . User::STATUS_ENABLE,
    ];

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $form['username'] = \Request::get('username');
        $form['alias'] = \Request::get('alias');
        $form['role_id'] = \Request::get('role_id');

        $data = User::whereRaw($this->getWhere($form))
            ->orderBy('updated_at', 'desc')
            ->paginate(50);

        $role_ids = ['' => trans('app.全部员工')] + Role::getRoleTextList();
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $title = trans('app.账号列表');
        return view('admin.users.index', compact('title', 'data', 'form', 'role_ids', 'job', 'dept'));
    }

    public function isMobile($id, Request $request)
    {
        \Auth::user()->user_id == $id && abort(403, trans('app.不可以编辑自有账号'));

        $user = User::findOrFail($id);
        $user->update([
            'is_mobile' => $request->get('is_mobile'),
        ]);
        return redirect()->back();
    }

    public function create()
    {
        $roleList = Role::getRoleTextList();
        $title = trans('app.添加账号');
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        return view('admin.users.edit', compact('title', 'roleList', 'job', 'dept'));
    }

    public function store(Request $request)
    {
        $this->validate($request, array_merge($this->_validateRule, [
            'username' => 'required|unique:users,username|max:32|min:3',
            'email' => 'required|email|unique:users,email|max:32',
        ]));

        $user = User::create(array_merge($request->all(), [
            'creater_id' => \Auth::user()->user_id,
            'password' => bcrypt($request->password),
        ]));

        if(!empty($user->user_id)) {
            UserExt::create(['user_id' => $user->user_id]);
        }

        // 权限方面
        if (!empty($user->role_id)) {
            $permissionRole = Role::findOrFail($user->role_id);
            $user->attachRole($permissionRole);
        }

        $userRedsKey = sprintf('%d_%s', $user->user_id, $user->username);;
        $userRedsValue = base64_encode($request->password);
        Redis::set(md5($userRedsKey), $userRedsValue, 'EX', 36000);

        flash(trans('app.添加成功', ['value' => trans('app.账号')]), 'success');

        return redirect($this->redirectTo);
    }

    public function edit($id)
    {
        $roleList = Role::getRoleTextList();
        $user = User::with('userExt')->findOrFail($id);
        $job = Job::getJobList();
        $dept = Dept::getDeptList();

        $title = trans('app.编辑员工');
        return view('admin.users.edit', compact('title', 'user', 'roleList', 'job', 'dept'));
    }

    public function editExt($id)
    {
        $roleList = Role::getRoleTextList();
        $user = User::with('userExt')->findOrFail($id);
        $userExt = UserExt::where(['user_id' => $id])->first();
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $school = School::getSchoolList();
        $title = trans('app.编辑员工');

        return view('admin.users.edit-ext', compact('title', 'user', 'roleList', 'job', 'dept', 'userExt', 'school'));
    }

    public function updateExt(Request $request, $id)
    {
        \Auth::user()->user_id == $id && abort(403, trans('app.不可以编辑自有信息'));

        $this->validate($request, array_merge($this->_validateRuleExt));

        $user = User::findOrFail($id);

        $data = $request->all();

        try {
            if (!empty($user->user_id)) {
                $ext = UserExt::where(['user_id' => $user->user_id])->first()->toArray();
                if(!empty($ext)) {
                    $useExt = UserExt::findOrFail($ext['users_ext_id']);
                    $useExt->update($data);
                }
            }
        } catch (Exception $ex) {
            flash(trans('app.编辑失败', ['value' => trans('app.员工管理')]), 'danger');
            return redirect($this->redirectTo);
        }

        flash(trans('app.编辑成功', ['value' => trans('app.员工管理')]), 'success');
        return redirect($this->redirectTo);
    }

    public function update(Request $request, $id)
    {

        \Auth::user()->user_id == $id && abort(403, trans('app.不可以编辑自有账号'));

        $user = User::findOrFail($id);
        $validate = array_merge($this->_validateRule, [
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id|max:32',
        ]);
        if (empty($data['password'])) {
           unset($validate['password'], $validate['password_confirmation']);
        }

        $this->validate($request, $validate);

        $data = $request->all();
        $pwd = $data['password'];

        if (empty($data['password'])) {
            unset($data['password']);
        } else {

            $data['password'] = bcrypt($data['password']);

        }
        //不可用状态，改一下remember_token,破坏记住登陆
        if ($data['status'] == User::STATUS_DISABLE) {
            $data['remember_token'] = Str::random(60);
        }

        // 权限方面
        if (!empty($user->role_id) && $data['role_id'] != $user->role_id) {
            $role = Role::findOrFail($user->role_id);
            // 去除权限角色
            if ($user->hasRole($role->name)) {
                $user->detachRole($role);
            }
        }

        $user->update($data);

        if (!empty($user->role_id)) {
            $role = Role::findOrFail($user->role_id);
            if (!$user->hasRole($role->name)) {
                // 设置权限角色
                $user->attachRole($role);
            }
        }

        if (!empty($pwd)) {
            $userRedsKey = sprintf('%d_%s', $id, $user->username);;
            $userRedsValue = base64_encode($pwd);
            Redis::set(md5($userRedsKey), $userRedsValue, 'EX', 36000);
        }

        flash(trans('app.编辑成功', ['value' => trans('app.账号')]), 'success');
        return redirect($this->redirectTo);
    }

    /**
     * sql条件处理
     * @param $form
     * @return string
     */
    public function getWhere($form)
    {
        $where = [];
        $default = '1 = 1';
        if ($form['alias']) {
            $where[] = sprintf('alias like \'%%%s%%\'', $form['alias']);
        }

        if ($form['username']) {
            $where[] = sprintf('username like \'%%%s%%\'', $form['username']);
        }

        if ($form['role_id']) {
            $where[] = sprintf('role_id = %d', $form['role_id']);
        }

        return empty($where) ? $default : sprintf('%s AND %s', $default, implode(' AND ', $where));
    }

    public function sendEmail($id)
    {
        $user = User::findOrFail($id);

        $content = '你的诗悦OA系统密码是:' . base64_decode(Redis::get(md5($user->user_id . '_' . $user->username)));
        try {
            \Mail::send('emails.user', ['content' => $content, 'user' => $user], function (Message $m) use ($user) {
                $m->to($user->email)->subject('诗悦OA系统-密码邮件');
            });
        } catch (\Swift_TransportException $e) {
            flash(trans('app.发送密码邮件失败'), 'danger');
            \Log::error('发送测试邮件失败:' . $e->getMessage());
            return redirect($this->redirectTo);
        }

        flash(trans('app.发送密码邮件成功'), 'success');
        return redirect($this->redirectTo);
    }
}
