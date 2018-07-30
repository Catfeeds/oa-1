<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Mail;
use Illuminate\Support\Facades\Redis;

class ProfileController extends Controller
{
    protected $redirectTo = '/admin/profile';

    public function index()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id|max:32',
        ]);

        $user->update([
            'email' => $request->get('email')
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
