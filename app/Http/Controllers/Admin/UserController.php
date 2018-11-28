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
        'sex' => 'required|in:' . UserExt::SEX_BOY . ',' . UserExt::SEX_GIRL,
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

        $roleIds = ['' => trans('app.权限列表')] + Role::getRoleTextList();

        $roles = Role::getRoleTextList();

        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $title = trans('app.账号列表');
        return view('admin.users.index', compact('title', 'data', 'form', 'roleIds', 'job', 'dept', 'roles'));
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
        $user = '';
        return view('admin.users.edit', compact('title', 'roleList', 'job', 'dept', 'user'));
    }

    public function store(Request $request)
    {
        unset($this->_validateRule['sex']);
        $this->validate($request, array_merge($this->_validateRule, [
            'username' => 'required|unique:users,username|max:32|min:3',
            'email' => 'required|email|unique:users,email|max:32',
        ]));


        if(!empty($request->dept_id) && !empty($request->is_leader)) {
            $checkUser = User::where(['dept_id' => $request->dept_id, 'is_leader' => User::IS_LEADER_TRUE])->first();
            if (!empty($checkUser->user_id)) {
                return redirect()->back()->withInput()->withErrors(['is_leader' => '该部门已存在上级,一个部门只能设置一个上级']);
            }
        }

        $user = User::create(array_merge($request->all(), [
            'creater_id' => \Auth::user()->user_id,
            'password' => bcrypt($request->password),
        ]));

        if(!empty($user->user_id)) {
            UserExt::create(['user_id' => $user->user_id]);
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
        $data = $request->all();

        $user = User::findOrFail($id);
        $validate = array_merge($this->_validateRule, [
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id|max:32',
        ]);

        if (empty($data['password'])) {
            unset($validate['password'], $validate['password_confirmation']);
        }

        if (!empty($request->dept_id) && !empty($request->is_leader)) {

            $checkUser = User::where(['dept_id' => $request->dept_id, 'is_leader' => User::IS_LEADER_TRUE])
                ->where('user_id', '!=', $user->user_id)->first();
            if (!empty($checkUser->user_id)) {
                return redirect()->back()->withInput()->withErrors(['is_leader' => '该部门已存在上级,一个部门只能设置一个上级']);
            }
        }

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

        $roleId = json_decode($user->role_id, true);
        // 权限方面
        if (!empty($roleId)) {
            $role = Role::whereIn('id', array_values($roleId))->get();
            // 去除权限角色
            foreach ($role as $k => $r) {
                if ($user->hasRole($r->name)) {
                    // 设置权限角色
                    $user->detachRole($r->id);
                }
            }
        }
        $roleIds = [];
        if(!empty($data['role_id'])) {
            foreach ($data['role_id'] as $d => $v) {
                $roleIds['id_' . $v] = $v;
            }
            $data['role_id'] = json_encode($roleIds);
        }
        $user->update($data);

        $roleId = json_decode($user->role_id, true);
        if (!empty($roleId)) {
            $role = Role::whereIn('id', array_values($roleId))->get();
            foreach ($role as $k => $r)
            if (!$user->hasRole($r->name)) {
                // 设置权限角色
                $user->attachRole($r->id);
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
        //json格式mysql查询语句
        if ($form['role_id']) {
            $where[] = sprintf('JSON_EXTRACT(role_id, "$.id_%d") = "%d"', $form['role_id'], $form['role_id']);
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
            //\Log::error('发送测试邮件失败:' . $e->getMessage());
            return redirect($this->redirectTo);
        }

        flash(trans('app.发送密码邮件成功'), 'success');
        return redirect($this->redirectTo);
    }

    public function getInfoByCalendar(Request $request)
    {
//        echo $request->date;
        $birthdayUsers = User::whereHas('userExt', function ($query) use ($request) {
            $query->where(\DB::raw("DATE_FORMAT(born, '%m-%d')"), date('m-d', strtotime($request->date)));
        })->with('dept')->get()->toArray();

        $entryUsers = User::whereHas('userExt', function ($query) use ($request) {
            $query->where(\DB::raw("DATE_FORMAT(entry_time, '%Y-%m-%d')"), $request->date);
        })->with('dept')->get()->toArray();

        return json_encode(['birthday' => $birthdayUsers, 'entry' => $entryUsers]);
    }
}
