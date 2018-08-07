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
use Auth;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Mail;
use Illuminate\Support\Facades\Redis;

class ProfileController extends Controller
{
    protected $redirectTo = '/admin/profile';

    private $_validateRuleExt = [
        'school_id' => 'required|numeric',
        'graduation_time' => 'required',
        'education_id' => 'required|numeric',
        'sex' => 'required|in:' . User::STATUS_DISABLE . ',' . User::STATUS_ENABLE,
        'constellation_id' => 'required|numeric',
        'blood_type' => 'required|numeric',
        'age' => 'required|numeric',
        'qq' => 'required|numeric',
        'card_id' => 'required|max:20',
        'card_address' => 'required|max:100',
        'born' => 'required',
        'birthplace' => 'required|max:20',
        'marital_status' => 'required|in:' . User::STATUS_DISABLE . ',' . User::STATUS_ENABLE,
        'urgent_tel' => 'required|max:11',
        'family_num' => 'required|max:11',
        'census' => 'required|max:20',
        'live_address' => 'required|max:100',
        'urgent_name' => 'required|max:20',
        'entry_time' => 'required',
        'salary_card' => 'required|max:20',
    ];

    public function index()
    {
        $user = Auth::user();
        $userExt = User::with('userExt')->where(['user_id' => $user->user_id])->first();
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $school = School::getSchoolList();
        $roleList = Role::getRoleTextList();

        return view('admin.profile.index', compact('user', 'userExt', 'job', 'dept', 'school', 'roleList'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    public function confirmEdit()
    {
        $user = Auth::user();
        $userExt = UserExt::where(['user_id' => $user->user_id])->first();
        $job = Job::getJobList();
        $dept = Dept::getDeptList();
        $school = School::getSchoolList();
        return view('admin.profile.confirm-edit', compact('user', 'userExt', 'job', 'dept', 'school'));

    }

    public function confirmUpdate(Request $request)
    {
        $this->validate($request, $this->_validateRuleExt);

        $user = User::findOrFail(\Auth::user()->user_id);

        $data = $request->all();

        try {
            if (!empty($user->user_id)) {
                $ext = UserExt::where(['user_id' => $user->user_id])->first()->toArray();
                if(!empty($ext)) {
                    $useExt = UserExt::findOrFail($ext['users_ext_id']);
                    $data['is_confirm'] = 1;
                    $useExt->update($data);
                }
            }

        } catch (Exception $ex) {
            flash(trans('app.编辑失败', ['value' => trans('app.个人信息确认')]), 'danger');
            return redirect($this->redirectTo);
        }

        flash(trans('app.编辑成功', ['value' => trans('app.个人信息确认')]), 'success');
        return redirect($this->redirectTo);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        UserExt::where(['user_id' => $user->user_id])->update([
            'live_address' => $request->get('live_address'),
            'urgent_name' => $request->get('urgent_name'),
            'urgent_tel' => $request->get('urgent_tel'),
            'marital_status' => $request->get('marital_status'),
        ]);

        flash(trans('app.编辑成功', ['value' => trans('app.账号')]), 'success');

        return redirect($this->redirectTo);
    }

    public function resetPassword()
    {
        $user = Auth::user();
        return view('admin.profile.reset-password', compact('user'));
    }

    public function resetPasswordUpdate(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'password' => 'required|min:8|alpha_num|confirmed',
            'password_confirmation' => 'required|min:6',
        ]);

        $user->update([
            'password' => bcrypt($request->get('password'))
        ]);

        $userRedsKey = sprintf('%d_%s', $user->user_id, $user->username);;
        $userRedsValue = base64_encode($request->get('password'));
        Redis::set(md5($userRedsKey), $userRedsValue, 'EX', 36000);

        flash(trans('app.编辑成功', ['value' => trans('app.账号')]), 'success');

        // 重新登录
        Auth::guard()->logout();
        return redirect('/');
    }

    public function mail()
    {
        $user = Auth::user();
        $content = trans('app.这是测试邮件 ，如果收到此邮件，说明您账号资料所填写的邮箱有效。');
        try {
            Mail::send('emails.user', ['user' => $user, 'content' => $content], function (Message $m) use ($user) {
                $m->to($user->email, $user->username)->subject(trans('app.测试邮件'));
            });
        } catch (\Swift_TransportException $e) {
            flash(trans('app.发送测试邮件失败'), 'danger');
            Log::error('发送测试邮件失败:' . $e->getMessage());
            return redirect($this->redirectTo);
        }
        flash(trans('app.发送成功', ['value' => trans('app.测试邮件')]), 'success');
        return redirect($this->redirectTo);
    }
}
