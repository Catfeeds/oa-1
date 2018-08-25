<?php

namespace App\Http\Controllers\Auth;

use App\Components\Helper\HttpAgent;
use App\Components\Rsa;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use SmsManager;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $decayMinutes = 30;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * 指明登录账号字段
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'captcha' => 'required|captcha'
        ]);
        if ($validator->fails()) {
            flash(trans('app.验证码错误'), 'danger');
            return $this->sendFailedLoginResponse($request);
        }

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $user = User::where(['username' => $request->get('username')])->first();
        //手机验证
        if (!empty($user) && $user->is_mobile == User::IS_MOBILE_TRUE) {
            return $this->sms($request, $user);
        }

        if ($this->guard()->attempt($this->credentials($request) + ['status' => User::STATUS_ENABLE], $request->filled('remember'))) {
            return $this->sendLoginResponse($request);
        }

        flash(trans('app.账号或密码错误'), 'danger');

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    protected function sms(Request $request, $user)
    {
        return view('auth.sms', compact('request', 'user'));
    }

    public function validateSMS(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'mobile' => 'required|confirm_mobile_not_change|confirm_rule:mobile_required',
            'verifyCode' => 'required|verify_code',
        ]);

        if ($validator->fails()) {
            SmsManager::forgetState();
            flash('短信验证码错误', 'danger');
            return redirect()->back();
        }

        if ($this->guard()->attempt($this->credentials($request) + ['status' => User::STATUS_ENABLE], $request->filled('remember'))) {
            $user = $this->guard()->user();
            if (empty($user->mobile)) {
                $user->update([
                    'mobile' => $request->get('mobile')
                ]);
            }
            return $this->sendLoginResponse($request);
        }

        flash(trans('app.账号或密码错误'), 'danger');

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    public function captcha()
    {
        return response()->json(['src' => captcha_src()]);
    }


    /**
     * 动态时间，默认1分钟
     * @param Request $request
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit($this->throttleKey($request), $this->decayMinutes);
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), 5, $this->decayMinutes
        );
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $message = \Lang::get('auth.throttle', ['seconds' => $seconds]);
        flash($message, 'danger');

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([$this->username() => $message]);
    }

    public function weChatLogin(Request $request)
    {
        try {
            $privkey = file_get_contents(storage_path('app/public/key/privkey.key'));
            $data = $request->data;
            $getSign = $request->sign;
            $getTime = $request->postTime;
            $message = json_decode(Rsa::privateDecrypt($data, $privkey), true);
            $sign = md5($message['userid'] . $getTime . config('services.sign'));
            if ($getSign == $sign) {
                $user = User::where(['username' => $message['userid']])->first();
                if ($user) {
                    $name = str_replace('http://', '', config('app.url'));
                    $data = [
                        'userid' => $message['userid'],
                        'token' => $message['token']
                    ];

                    $postTime = time();
                    $sign = md5($message['userid'] . $postTime . config('services.sign'));
                    $url = sprintf('%s?data=%s&sign=%s&postTime=%s&name=%s', config('services.weChatUrl'), urlencode(Rsa::privateEncrypt(json_encode($data), $privkey)), $sign, $postTime, $name);
                    $ret = HttpAgent::getInstance()->request('GET', $url);

                    if ($ret['success']) {
                        $this->guard()->login($user);
                        return redirect()->route('home');
                    } else {
                        flash($ret['message'], 'danger');
                        return redirect()->route('login');
                    }
                } else {
                    flash('查不到该用户！', 'danger');
                    return redirect()->route('login');
                }
            } else {
                abort(404);
            }
        } catch (\Exception $e) {
            flash('非法操作！', 'danger');
            \Log::error('非法操作:' . $e->getMessage());
            return redirect()->route('login');
        }
    }
}
