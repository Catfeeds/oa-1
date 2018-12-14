<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/12
 * Time: 20:04
 */

namespace App\Http\Controllers\StaffManage;

use App\Components\Helper\DataHelper;
use App\Http\Components\Helpers\OperateLogHelper;
use App\Http\Components\ScopeStaff\EntryScope;
use App\Http\Controllers\Attendance\AttController;
use App\Models\Role;
use App\Models\Sys\Firm;
use App\Models\StaffManage\Entry;
use App\Models\Sys\Dept;
use App\Models\Sys\Ethnic;
use App\Models\Sys\Job;
use App\Models\Sys\OperateLog;
use App\Models\Sys\School;
use App\Models\UserExt;
use App\User;
use EasyWeChat\Kernel\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Mail\Message;

class EntryController extends AttController
{
    protected $scopeClass = EntryScope::class;

    const APP_KEY = '343ad1e40ce3cf142873fb2668f2577f'; //验证密钥
    const EXPIRED_TIME = 11117200; //过期时间 秒

    private $_validateRule = [
        'name' => 'required|max:32|min:2',
        'mobile' => 'required|phone_number|max:11',
        'entry_time' => 'required|date',
        'nature_id' => 'required|integer',
        'hire_id' => 'required|integer',
        'firm_id' => 'required|integer',
        'dept_id' => 'required|integer',
        'job_id' => 'required|integer',
        'job_name' => 'required',
        'leader_id' => 'required|integer',
        'tutor_id' => 'required|integer',
        'friend_id' => 'nullable|integer',
        'copy_user' => 'nullable|array',
        'role_id'  => 'required|array',
        'sex' => 'required|in:' . UserExt::SEX_BOY . ',' . UserExt::SEX_GIRL,
    ];

    private $_validateRuleExt = [
        'entry.card_id' => 'required|identitycards|max:20',
        'entry.card_address' => 'required|max:100',
        'entry.ethnic_id' => 'required|max:32',
        'entry.birthplace' => 'required|max:20',
        'entry.political_id' => 'required|max:20',
        'entry.census' => 'required|max:20',
        'entry.family_num' => 'required',
        'entry.firm_call' => 'required',
        'entry.birthday' => 'required',
        'entry.birthday_type' => 'required',
        'entry.marital_status' => 'required|integer',
        'entry.live_address' => 'required|max:100',
        'entry.urgent_name' => 'required|max:20',
        'entry.urgent_bind' => 'required|max:20',
        'entry.urgent_tel' => 'required|max:11',
        'entry.education_id' => 'required|integer',
        'entry.school_id' => 'required|integer',
        'entry.graduation_time' => 'required|date',
        'entry.specialty' => 'required|max:20',
        'entry.degree' => 'required|max:20',
        'entry.used_email' => 'required',
    ];

    public function index()
    {
        $scope = $this->scope;
        $scope->block = 'staff-manage.entry.scope';

        $data = Entry::whereRaw($scope->getWhere())
            ->orderBy('entry_time', 'desc')
            ->paginate();
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $title = trans('staff.员工待入职列表');
        return view('staff-manage.entry.index', compact('title', 'data', 'job', 'dept', 'scope'));
    }

    public function create()
    {
        $userIds = [];
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $users = User::getUsernameAliasList();
        $firm = Firm::getFirmList();
        $ethnic = Ethnic::getEthnicList();
        $roleList = Role::getRoleTextList();
        $title = trans('staff.添加待入职');
        $btnDesc = trans('app.提交');

        $maxUsername = self::getMaxUserName()[0];
        $username = sprintf('sy%04d', (int)str_replace('sy', '', $maxUsername) + 1);

        return view('staff-manage.entry.edit', compact('title', 'btnDesc', 'roleList', 'ethnic', 'users', 'job', 'dept', 'firm', 'userIds', 'username', 'maxUsername'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);
        $data = $request->all();

        if(empty($data['username'])) {
            return redirect()->back()->withInput()->withErrors(['username' => '请输入工号']);
        }

        $check = Entry::where(['username' => $data['username']])->whereNotIn('status', [Entry::REVIEW_REFUSE])->first();

        if(!empty($check->entry_id)) {
            return redirect()->back()->withInput()->withErrors(['username' => '工号已存在']);
        }

        if(empty($data['username'])) {
            return redirect()->back()->withInput()->withErrors(['username' => '请输入工号']);
        }

        $data['creater_id'] = \Auth::user()->user_id;
        $data['copy_user'] = !empty($data['copy_user']) ? json_encode($data['copy_user']) : NUll;
        $data['remember_token'] = Str::random(60);
        $data['send_time'] = date('Y-m-d H:i:s', time());
        $data['role_id'] = !empty($data['role_id']) ? json_encode($data['role_id']) : NUll;
        $data['entry_time'] = $data['entry_time'] . ' 09:00:00';

        $entry = Entry::create($data);

        //员工列表
        $users = User::getUsernameList();
        $sendUser  = [];
        if(!empty($entry->copy_user)) {
            $copyUser = json_decode($entry->copy_user, true);
            foreach ($copyUser as $ck => $cv) {
                $sendUser[] = $users[$cv] ?? '';
            }
        }
        $tutorId = !empty($entry->tutor_id) ? [$users[$entry->tutor_id]] : [];
        $friendId = !empty($entry->friend_id) ? [$users[$entry->friend_id]] : [];
        $sendUser = array_unique(array_merge($sendUser , $tutorId ,$friendId));

        if($sendUser) {
            self::sendWXEntryContent($entry, implode('|', $sendUser), '入职', '新员工待入职通知');
        }

        flash(trans('app.添加成功', ['value' => trans('staff.待入职人员')]), 'success');

        return redirect()->route('entry.list');
    }

    public function sendWXEntryContent($entry, $sendUser, $type, $notice)
    {
        $content =  '【'.$type.'】'.$notice.'
入职人：'.$entry->name.'
性别：'.UserExt::$sex[$entry->sex].'
所属部门：'.Dept::getDeptList()[$entry->dept_id].'
岗位类型：'.Job::getJobList()[$entry->job_id].'
岗位名称：'.$entry->job_name.'
入职时间：'.$entry->entry_time;

        //企业微信通知审核人员
        OperateLogHelper::sendWXMsg($sendUser, $content);
    }

    public function edit($id)
    {
        $entry = Entry::findOrFail($id);
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $users = User::getUsernameAliasAndDeptList();
        $firm = Firm::getFirmList();
        $ethnic = Ethnic::getEthnicList();
        $roleList = Role::getRoleTextList();
        $userIds = json_decode($entry->copy_user);

        $maxUsername = self::getMaxUserName()[0] ?? '';
        $username = '';
        $operateLog = OperateLog::where(['type_id' => OperateLog::ENTRY, 'info_id' => $entry->entry_id])->get()->toArray();

        $btnDesc = trans('app.修改');

        $title = trans('app.编辑', ['value' => trans('staff.待入职人员')]);
        return view('staff-manage.entry.edit', compact('title', 'operateLog', 'btnDesc', 'ethnic', 'roleList', 'users', 'job', 'dept', 'firm', 'entry', 'userIds', 'username', 'maxUsername'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, array_merge($this->_validateRule, [
            'email' => 'required|email|unique:users_entry,email,'. $id .',entry_id|max:32',
            'username' => 'required|unique:users_entry,username,'. $id .',entry_id|max:20',
        ]));

        $data = $request->all();

        $data['role_id'] =  !empty($data['role_id']) ? json_encode($data['role_id']) : NULL;
        $data['copy_user'] = !empty($data['copy_user']) ?  json_encode($data['copy_user']) : NULL;
        $data['entry_time'] = $data['entry_time'] . ' 09:00:00';
        $entry = Entry::findOrFail($id);
        $entryOld = $entry->toArray();

        $entry->update($data);
        $entryChanges = $entry->getChanges();

        //编辑内容有调整时操作
        if(!empty($entryChanges)) {
            $firm = Firm::getFirmList();
            $dept = Dept::getDeptList();
            $job = Job::getJobList();
            $changeFiled = [
                'entry_time' => sprintf('入职时间由:%s->变更为:%s', $entryOld['entry_time'], $entryChanges['entry_time'] ?? ''),
                'nature_id' => sprintf('工作性质由:%s->变更为:%s',  Entry::$nature[$entryOld['nature_id']] ?? '未知信息', Entry::$nature[$entryChanges['nature_id'] ?? ''] ?? ''),
                'hire_id' => sprintf('招聘类型由:%s->变更为:%s',  Entry::$hireTYpe[$entryOld['hire_id']] ?? '未知信息', Entry::$hireTYpe[$entryChanges['hire_id'] ?? ''] ?? ''),
                'firm_id' => sprintf('所属公司由:%s->变更为:%s',  $firm[$entryOld['firm_id']] ?? '', $firm[$entryChanges['firm_id'] ?? ''] ?? ''),
                'dept_id' => sprintf('部门由:%s->变更为:%s',  $dept[$entryOld['dept_id']] ?? '', $dept[$entryChanges['dept_id'] ?? ''] ?? ''),
                'job_id' => sprintf('岗位类型由:%s->变更为:%s',  $job[$entryOld['job_id']] ?? '', $job[$entryChanges['job_id'] ?? ''] ?? ''),
                'job_name' => sprintf('岗位名称由:%s->变更为:%s',  $entryOld['job_name'], $entryChanges['job_name'] ?? ''),
            ];

            $msg = [];
            foreach ($entryChanges as $k => $v) {
                if(in_array($k, array_keys($changeFiled))) $msg [] = $changeFiled[$k] ?? '';
            }

            if(!empty($msg)) {
                OperateLogHelper::createOperateLog(OperateLog::ENTRY, $entry->entry_id, '调整岗位信息',  implode(';', $msg));
                //员工列表
                $users = User::getUsernameList();
                //抄送人员, 导师，基友企业微信通知
                $sendUser = [];
                if(!empty($entry->copy_user)) {
                    $copyUser = json_decode($entry->copy_user, true);
                    foreach ($copyUser as $ck => $cv) {
                        $sendUser[] = $users[$cv] ?? '';
                    }
                }
                $tutorId = !empty($entry->tutor_id) ? [$users[$entry->tutor_id]] : [];
                $friendId = !empty($entry->friend_id) ? [$users[$entry->friend_id]] : [];
                $sendUser = array_unique(array_merge($sendUser , $tutorId ,$friendId));

                if($sendUser) {
                    self::sendWXEntryContent($entry, implode('|', $sendUser), '入职调整', '新员工待入职通知');
                }
            }
        }

        flash(trans('app.编辑成功', ['value' => trans('staff.待入职人员')]), 'success');

        return redirect()->route('entry.list');
    }

    public function editInfo($id)
    {
        $entry = Entry::findOrFail($id);
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $users = User::getUsernameAliasList();
        $firm = Firm::getFirmList();
        $ethnic = Ethnic::getEthnicList();
        $roleList = Role::getRoleTextList();
        $userIds = json_decode($entry->copy_user);
        $maxUsername = self::getMaxUserName()[0];
        $workHistory = json_decode($entry->work_history, true);
        $familyNum = json_decode($entry->family_num, true);
        $school = School::getSchoolList();

        $title = trans('app.编辑', ['value' => trans('staff.入职信息')]);
        return view('staff-manage.entry.edit-info', compact('title', 'workHistory', 'familyNum', 'school', 'ethnic', 'roleList', 'users', 'job', 'dept', 'firm', 'entry', 'userIds', 'maxUsername'));
    }

    /**
     * 更新员工入职信息
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateInfo(Request $request, $id)
    {
        $this->validate($request, array_merge($this->_validateRule, $this->_validateRuleExt, [
            'email' => 'required|email|unique:users_entry,email,'. $id .',entry_id|max:32',
        ]));

        $data = $request->all();
        $entryArr = $data['entry'];
        unset($data['entry']);

        $data['role_id'] = json_encode($data['role_id']);
        $data['copy_user'] = json_encode($data['copy_user']);

        //家庭成员
        $familyArr = [];
        foreach ($entryArr['family_num'] as $fk => $fv) {
            if(empty($fv['name']) || empty($fv['age']) || empty($fv['relation']) || empty($fv['position']) || empty($fv['phone'])) continue;

            $familyArr[] = $fv;
        }
        $entryArr['family_num'] = json_encode($familyArr);
        //工作经历
        $workArr = [];
        foreach ($entryArr['work_history'] as $wk => $wv) {
            if(empty($wv['time']) || empty($wv['deadline']) || empty($wv['work_place']) || empty($wv['position']) || empty($wv['income']) || empty($wv['boss']) || empty($wv['phone'])) continue;

            $workArr[] = $wv;
        }
        $entryArr['work_history'] = json_encode($workArr);

        $entry = Entry::findOrFail($id);

        $entry->update($data + $entryArr);
        flash(trans('app.编辑成功', ['value' => trans('staff.待入职人员')]), 'success');

        return redirect()->route('entry.showInfo', ['id' => $entry->entry_id]);
    }

    /**
     * 生成入职信息链接到员工邮箱
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSendInfo($id)
    {
        $entry = Entry::findOrFail($id);

        $rememberToken = $entry->remember_token;

        if($entry->status === Entry::FILL_END) {
            $rememberToken =  Str::random(60);
            $entry->update(['status' => Entry::FILL_IN, 'send_time' => date('Y-m-d H:i:s', time()), 'remember_token' => $rememberToken]);
        }

        $url = sprintf(url('/') . '/entry/fill/%s/%s', $entry->remember_token, md5($rememberToken . self::APP_KEY));

        //邮箱发送信息
        $mailContent = '亲爱的  '.$entry->name.' 同学：</br>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp欢迎加入诗悦网络！</br>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp为方便您尽快融入我们，请您务必点击下方链接完成个人入职信息录入，预祝您工作顺利！</br>
网页地址:'.$url;

        try {
            \Mail::send('emails.entry', ['content' => $mailContent, 'entry' => $entry], function (Message $m) use ($entry) {
                $m->to($entry->email)->subject('诗悦OA系统-入职信息填写邮件');
            });
        } catch (\Swift_TransportException $e) {
            flash(trans('staff.发送员工入职邮件失败'), 'danger');
            \Log::error('发送员工入职邮件失败:' . $e->getMessage());

            //企业微信通知信息
            $content = '【'.$entry->name.'】 发送填写完入职资料通知失败
如多次发送失败，可复制链接提供给入职员工
链接地址:'.$url;
            $userId = \Auth::user()->username;
            OperateLogHelper::sendWXMsg($userId, $content);

            return redirect()->route('entry.list');
        }

        $entry->update(['status' => Entry::HAS_SEND, 'send_time' => date('Y-m-d H:i:s', time())]);

        flash(trans('staff.发送员工入职邮件成功'), 'success');
        return redirect()->route('entry.list');
    }

    /**
     * 员工入职信息填写页面
     * @param $token
     * @param $sign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fillInfo($token, $sign)
    {
        //sign验证
        $entryS = self::sign($token, $sign);
        if(empty($entryS)) {
            flash('错误的请求!', 'danger');
            return view('staff-manage.entry.error');
        }

        $entryS->update(['status' => Entry::FILL_IN]);

        $cache = (object)json_decode(Redis::get($entryS->entry_id . '_entry_save'), true);

        $entry = $entryS;
        //优先缓存为主
        if(!empty($cache->birthday)) {
            $entry = $cache;
        }

        $school = School::getSchoolList();
        $users = User::getUsernameAliasList();
        $dept = Dept::getDeptList();
        $ethnic = Ethnic::getEthnicList();
        $title = trans('staff.填写入职资料');
        return view('staff-manage.entry.fill', compact('title', 'ethnic', 'users', 'school', 'entry', 'entryS', 'dept', 'sign', 'cache'));
    }

    /**
     * 删除入职信息
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function del($id)
    {
        try {
            Entry::findOrFail($id)->delete();
        } catch (\Exception $e) {
            flash(trans('staff.删除入职信息失败'), 'danger');
            return redirect()->route('entry.list');
        }

        flash(trans('staff.删除入职信息成功'), 'success');
        return redirect()->route('entry.list');
    }

    /**
     * 员工填写资料
     * @param Request $request
     * @param $token
     * @param $sign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fill(Request $request, $token, $sign)
    {

        $this->validate($request, $this->_validateRuleExt);
        //sign验证
        $res = self::sign($token, $sign);
        if(empty($res)) {
            flash('错误的请求!', 'danger');
            return view('staff-manage.entry.error');
        };

        $data = $request->all()['entry'];

        $data['status'] = Entry::FILL_END;
        //家庭成员
        $familyArr = [];
        foreach ($data['family_num'] as $fk => $fv) {
            if(empty($fv['name']) || empty($fv['age']) || empty($fv['relation']) || empty($fv['position']) || empty($fv['phone'])) continue;

            $familyArr[] = $fv;
        }
        $data['family_num'] = json_encode($familyArr);
        //工作经历
        $workArr = [];
        foreach ($data['work_history'] as $wk => $wv) {
            if(empty($wv['time']) || empty($wv['deadline']) || empty($wv['work_place']) || empty($wv['position']) || empty($wv['income']) || empty($wv['boss']) || empty($wv['phone'])) continue;

            $workArr[] = $wv;
        }
        $data['work_history'] = json_encode($workArr);


        $entry = Entry::findOrFail($res->entry_id);

        $entry->update($data);

        //企业微信通知管理员
        $msg = '【'.$entry->name.'】 填写完入职资料
                请前往确认: [<a href = "'.url('/').'/staff/entry">点我前往</a>]';
        $userId = User::getUserAliasToId($entry->creater_id);
        OperateLogHelper::sendWXMsg($userId->username, $msg);

        flash('恭喜你完成了个人资料填写，请等待管理员审核。', 'success');
        return view('staff-manage.entry.error');
    }

    public function save(Request $request)
    {

        $data = $request->all()['entry'] ?? [];
        if(empty($data)) {
            return response()->json(['status' => -1, 'msg' => '保存失败']);
        };

        $entry = Entry::findOrFail((int)$data['fill_id']);

        if(empty($entry->entry_id)) {
            return response()->json(['status' => -1, 'msg' => '保存失败']);
        };

        //sign验证
        $res = self::sign($entry->remember_token, $data['token']);
        if(empty($res)) {
            return response()->json(['status' => -1, 'msg' => '保存失败']);
        };

        $userRedsKey = sprintf('%d_%s', $entry->entry_id, 'entry_save');
        $userRedsValue = json_encode($data);

        Redis::set($userRedsKey, $userRedsValue, 'EX', 36000);

        return response()->json(['status' => 1, 'msg' => '保存成功']);
    }

    //信息状态验证
    public function sign($token, $sign)
    {
        //sign验证
        if ($sign != md5($token . self::APP_KEY)) {
            return '';
        }

        $entry = Entry::where(['remember_token' => $token])->first();

        if (empty($entry->entry_id) || in_array($entry->status, [Entry::FILL_END, Entry::REVIEW_PASS, Entry::REVIEW_REFUSE]) || time() >= (strtotime($entry->send_time) + self::EXPIRED_TIME)) {
            return '';
        }

        return $entry;
    }

    /**
     * 拒绝入职
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refuse($id)
    {
        try {
           $entry = Entry::findOrFail($id);
           $entry->update(['status' => Entry::REVIEW_REFUSE]);
        } catch (\Exception $e) {
            flash(trans('staff.放弃入职信息失败'), 'danger');
            return redirect()->route('entry.list');
        }

        flash(trans('staff.放弃入职信息成功'), 'success');
        return redirect()->route('entry.list');
    }

    /**
     * 确认入职
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pass($id)
    {
        //事务开启
        DB::beginTransaction();
        try {
            $entry = Entry::findOrFail($id);

            if(empty($entry->entry_id) || $entry->status === Entry::REVIEW_PASS) {
                return response()->json(['status' => -1, 'msg' => '员工已办理入职']);
            }

            $users = User::where(['email' => $entry->email])->first();
            if(!empty($users->email) ) {
                return response()->json(['status' => -2, 'msg' => '员工邮箱已存在，请重新分配']);
            }

            $pwd = DataHelper::randString(10);

            $roleId = json_decode($entry->role_id, true);

            $roleIds = [];

            if (!empty($roleId) && is_array($roleId)) {
                $ids = [];
                foreach ($roleId as $d => $v) {
                    $ids['id_' . $v] = $v;
                }
                $roleIds = json_encode($ids);
            }

            $userData = [
                'username' => $entry->username,
                'alias' => $entry->name,
                'email' => $entry->used_email,
                'mobile' => $entry->mobile,
                'password' => bcrypt($pwd),
                'remember_token' => Str::random(60),
                'dept_id' => $entry->dept_id,
                'job_id' => $entry->job_id,
                'is_leader' => 0,
                'is_mobile' => 0,
                'role_id' => $roleIds,
                'creater_id' => \Auth::user()->user_id,
            ];

            $user = User::create($userData);

            $roleId = json_decode($user->role_id, true);
            if (!empty($roleId) && is_array($roleId)) {
                $role = Role::whereIn('id', array_values($roleId))->get();
                foreach ($role as $k => $r)
                    if (!$user->hasRole($r->name)) {
                        // 设置权限角色
                        $user->attachRole($r->id);
                    }
            }

            UserExt::create($entry->toArray() + ['user_id' => $user->user_id]);
            $entry->update(['status' => Entry::REVIEW_PASS, 'review_id' => \Auth::user()->user_id]);

            //保存明文密钥有效期，用于发送用户帐号密钥到邮箱
            if (!empty($pwd)) {
                $userRedsKey = sprintf('%d_%s', $user->user_id, $user->username);;
                $userRedsValue = base64_encode($pwd);
                Redis::set(md5($userRedsKey), $userRedsValue, 'EX', 36000);
            }

        } catch (\Exception $e) {
            //事务回滚
            DB::rollBack();
            return response()->json(['status' => -3, 'msg' => '办理员工入职失败']);
        }
        //事务提交
        DB::commit();

        //企业微信通知管理员
        //$msg = '【'.$user->alias.'】 办理入职成功，前往邮箱查看OA系统分配给你的帐号和密码';
        $msg = '【'.$user->alias.'】 办理入职成功，可登陆系统查看个人资料是否正确
            帐号: ' .$user->username . ' 密码 : '. $pwd . '  登录地址: '.url('/') . '
            请妥善保管帐号密码，登录系统,修改密码';
        //调试先默认通知给自己
        /*           $userId = \Auth::user()->username;
                   OperateLogHelper::sendWXMsg($userId, $msg);*/

        //邮箱发送帐号密码
        \Mail::send('emails.entry', ['content' => $msg, 'entry' => $entry], function (Message $m) use ($entry) {
            $m->to($entry->email)->subject('诗悦OA系统-帐号信息');
        });

        return response()->json(['status' => 1, 'msg' => '办理入职成功']);
    }

    /**
     * 设置入职帐号分配
     * @return string
     */
    public function setUserName()
    {
        $maxId = User::orderBy('username', 'desc')->first()->toArray();
        $username = sprintf('sy%04d', (int)str_replace('sy', '', $maxId['username']) + 1);

        return $username;
    }

    /***
     * @return array
     */
    public function getMaxUserName()
    {
        $userMaxId = User::orderBy('username', 'desc')->get(['username'])->pluck('username')->toArray();
        $entryMaxId = Entry::whereNotIn('status', [Entry::REVIEW_REFUSE])->orderBy('username', 'desc')->get(['username'])->pluck('username')->toArray();

        $usernameS = array_merge($userMaxId, $entryMaxId);
        arsort($usernameS);
        $usernameS = array_values($usernameS);
        return $usernameS;
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showInfo($id)
    {
        $entry = Entry::findOrFail($id);
        $school = School::getSchoolList();
        $users = User::getUsernameAliasList();
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $firm = Firm::getFirmList();
        $ethnic = Ethnic::getEthnicList();
        $userIds = json_decode($entry->copy_user);
        $roleList = Role::getRoleTextList();
        $title = trans('staff.入职信息确认');

        return view('staff-manage.entry.info', compact('title', 'roleList','users', 'school', 'entry', 'job', 'dept', 'firm', 'userIds', 'ethnic'));
    }
}