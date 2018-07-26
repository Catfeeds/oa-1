@extends('layouts.base')

@section('title', trans('app.登录'))
@section('body-class', 'gray-bg')

@section('base')

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name">{{ trans(config('app.nickname')) }}</h1>
            </div>
            <h3>{{ trans(config('app.name')) }}</h3>

            @include('flash::message')

            {!! Form::open(['url' => route('validateSMS'), 'class' => 'm-t']) !!}
            {!! Form::hidden('username', $request->username) !!}
            {!! Form::hidden('password', $request->password) !!}
            {!! Form::hidden('remember', $request->remember) !!}

                <div class="form-group">
                    {!! Form::text('mobile', $user->mobile, ['class' => 'form-control', 'placeholder' => '手机号', 'required' => true, 'readonly' => !empty($user->mobile)]) !!}
                </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::text('verifyCode', null, ['class' => 'form-control', 'placeholder' => '短信验证码']) !!}
                    </div>
                    <div class="col-md-6">
                        <a class="btn btn-sm btn-white btn-block" id="sendVerifySmsButton">获取验证码</a>
                    </div>
                </div>
            </div>

            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary block full-width m-b']) !!}
            {!! Form::close() !!}

            <p class="m-t"> <small>{{ trans(config('app.nickname'))  . ' &copy; 2016-' . date('Y') }}</small> </p>
        </div>
    </div>

@endsection

@section('scripts-last')
    <script src="{{ asset('js/laravel-sms.js') }}"></script>
    <script>
        $(function() {
            $('#sendVerifySmsButton').sms({
                token       : "{{csrf_token()}}",
                interval    : 60,
                requestData : {
                    mobile : function () {
                        return $('input[name=mobile]').val();
                    },
                    mobile_rule : 'mobile_required'
                }
            });
        });
    </script>
@endsection