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
use App\Models\StaffManage\Firm;
use App\Models\StaffManage\Entry;
use App\Models\Sys\Dept;
use App\Models\Sys\Job;
use App\Models\Sys\School;
use App\Models\UserExt;
use App\User;
use EasyWeChat\Kernel\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class EntryController extends AttController
{
    protected $scopeClass = EntryScope::class;

    const APP_KEY = '343ad1e40ce3cf142873fb2668f2577f'; //验证密钥
    const EXPIRED_TIME = 1117200; //过期时间 秒

    private $_validateRule = [
        'name' => 'required|max:32|min:2',
        'mobile' => 'required|phone_number|max:11',
        'email' => 'required|email|unique:users,email|max:32',
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
        'copy_users' => 'required|array',
        'sex' => 'required|in:' . UserExt::SEX_BOY . ',' . UserExt::SEX_GIRL,
    ];

    private $_validateRuleExt = [
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
        $title = trans('staff.添加待入职');

        return view('staff-manage.entry.edit', compact('title', 'users', 'job', 'dept', 'firm', 'userIds'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->_validateRule);
        $data = $request->all();

        $data['creater_id'] = \Auth::user()->user_id;
        $data['copy_user'] = json_encode($data['copy_users']);
        $data['remember_token'] = Str::random(60);
        $data['send_time'] = date('Y-m-d H:i:s', time());

        Entry::create($data);
        flash(trans('app.添加成功', ['value' => trans('staff.待入职人员')]), 'success');

        return redirect()->route('entry.list');
    }

    public function edit($id)
    {
        $entry = Entry::findOrFail($id);
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $users = User::getUsernameAliasList();
        $firm = Firm::getFirmList();

        $userIds = json_decode($entry->copy_user);
        $title = trans('app.编辑', ['value' => trans('staff.待入职人员')]);
        return view('staff-manage.entry.edit', compact('title', 'users', 'job', 'dept', 'firm', 'entry', 'userIds'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, $this->_validateRule);

        $data = $request->all();

        $entry = Entry::findOrFail($id);

        $entry->update($data);
        flash(trans('app.编辑成功', ['value' => trans('staff.待入职人员')]), 'success');

        return redirect()->route('entry.list');
    }

    /**
     * 生成入职信息链接到员工邮箱
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSendInfo($id)
    {
        $entry = Entry::findOrFail($id);

        //邮箱发送信息
/*        try {
            \Mail::send('emails.entry', ['content' => $content, 'entry' => $entry], function (Message $m) use ($entry) {
                $m->to($entry->email)->subject('诗悦OA系统-入职信息填写邮件');
            });
        } catch (\Swift_TransportException $e) {
            flash(trans('staff.发送员工入职邮件失败'), 'danger');
            \Log::error('发送员工入职邮件失败:' . $e->getMessage());
            return redirect()->route('entry.list');
        }*/
        $rememberToken = $entry->remember_token;

        if($entry->status === Entry::FILL_END) {
            $rememberToken =  Str::random(60);
            $entry->update(['status' => Entry::FILL_IN, 'send_time' => date('Y-m-d H:i:s', time()), 'remember_token' => $rememberToken]);
        }

        $url = sprintf(url('/') . '/entry/fill/%s/%s', $entry->remember_token, md5($rememberToken . self::APP_KEY));

        //$content = '请用google浏览器打开链接，个人入职信息填写请再2个小时之内完成，否则请联系人事: ' . $url;
        //企业微信通知信息
        $content = '【'.$entry->name.'】 填写完入职资料通知
                请用google浏览器打开链接，个人入职信息填写请再2个小时之内完成，否则过期无效,有疑问请联系人事
                链接地址: [<a href = "'.$url.'">点我前往</a>]';

        $userId = \Auth::user()->username;
        OperateLogHelper::sendWXMsg($userId, $content);

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
        $entry = self::sign($token, $sign);
        if(empty($entry)) {
            $message = trans('错误的请求');
            return view('staff-manage.entry.error', compact('message'));
        }

        $entry->update(['status' => Entry::FILL_IN]);

        $school = School::getSchoolList();
        $users = User::getUsernameAliasList();
        $dept = Dept::getDeptList();
        $title = trans('staff.填写入职资料');
        return view('staff-manage.entry.fill', compact('title', 'users', 'school', 'entry', 'dept'));
    }

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
            $message = trans('错误的请求');
            return view('staff-manage.entry.error', compact('message'));
        };

        $data = $request->all();

        $data['status'] = Entry::FILL_END;
        $entry = Entry::findOrFail($res->entry_id);

        $entry->update($data);

        //企业微信通知管理员
        $msg = '【'.$entry->name.'】 填写完入职资料
                请前往确认: [<a href = "'.url('/').'/staff/entry">点我前往</a>]';
        $userId = User::getUserAliasToId($entry->creater_id);
        OperateLogHelper::sendWXMsg($userId->username, $msg);

        $message = trans('资料填写完成');
        return view('staff-manage.entry.error', compact('message'));
    }

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
            $userData = [
                'username' => self::setUserName(),
                'alias' => $entry->name,
                'email' => $entry->email,
                'mobile' => $entry->mobile,
                'password' => bcrypt($pwd),
                'remember_token' => Str::random(60),
                'dept_id' => $entry->dept_id,
                'job_id' => $entry->job_id,
                'is_leader' => 0,
                'is_mobile' => 0,
                'creater_id' => \Auth::user()->user_id,
            ];

            $user = User::create($userData);

            UserExt::create($entry->toArray() + ['user_id' => $user->user_id]);
            $entry->update(['status' => Entry::REVIEW_PASS, 'review_id' => \Auth::user()->user_id]);


            //保存明文密钥有效期，用于发送用户帐号密钥到邮箱
            if (!empty($pwd)) {
                $userRedsKey = sprintf('%d_%s', $user->user_id, $user->username);;
                $userRedsValue = base64_encode($pwd);
                Redis::set(md5($userRedsKey), $userRedsValue, 'EX', 36000);
            }

            //企业微信通知管理员
            //$msg = '【'.$user->alias.'】 办理入职成功，前往邮箱查看OA系统分配给你的帐号和密码';
            $msg = '【'.$user->alias.'】 办理入职成功，可登陆系统查看个人资料是否正确
            帐号: ' .$user->username . ' 密码 : '. $pwd . '  登录地址: '.url('/') . '
            请妥善保管帐号密码，登录系统,修改密码';
            //调试先默认通知给自己
            $userId = \Auth::user()->username;
            OperateLogHelper::sendWXMsg($userId, $msg);

/*          //邮箱发送帐号密码
            $content = '帐号: ' .$user->username . ' 密码 : '. $pwd . ' 登录地址:'.url('/') . '请妥善保管帐号密码，登录系统,修改密码';

            \Mail::send('emails.entry', ['content' => $content, 'entry' => $entry], function (Message $m) use ($entry) {
                $m->to($entry->email)->subject('诗悦OA系统-帐号信息');
            });*/

        } catch (\Exception $e) {
            //事务回滚
            DB::rollBack();
            return response()->json(['status' => -3, 'msg' => '办理员工入职失败']);
        }
        //事务提交
        DB::commit();
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

    public function showInfo($id)
    {
        $entry = Entry::findOrFail($id);
        $school = School::getSchoolList();
        $users = User::getUsernameAliasList();
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $firm = Firm::getFirmList();
        $userIds = json_decode($entry->copy_user);
        $title = trans('staff.入职信息确认');

        return view('staff-manage.entry.info', compact('title', 'users', 'school', 'entry', 'job', 'dept', 'firm', 'userIds'));
    }
}