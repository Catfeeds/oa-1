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

            {!! Form::open(['url' => route('login'), 'class' => 'm-t']) !!}

            <div class="form-group">
                {!! Form::text('username', old('username'), ['class' => 'form-control', 'placeholder' => trans('app.账号'), 'required' => true]) !!}
            </div>

            <div class="form-group">
                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('app.密码'), 'required' => true]) !!}
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::text('captcha', null, ['class' => 'form-control', 'placeholder' => trans('app.验证码')]) !!}
                    </div>
                    <div class="col-md-6">
                        {!! captcha_img() !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::checkbox('remember') !!}
                {{ trans('app.下次自动登录') }}
            </div>

            {!! Form::submit(trans('app.登录'), ['class' => 'btn btn-primary block full-width m-b']) !!}

            {!! Form::close() !!}

            <p class="m-t">
                <small>{{ trans(config('app.nickname'))  . ' &copy; 2016-' . date('Y') }}</small>
            </p>
        </div>
    </div>

@endsection

@section('scripts-last')
    <script>
        $(function () {
            $('img').click(function () {
                $.getJSON('{{ route('captcha') }}', function (res) {
                    $('img').prop('src', res.src);
                });
            });
        });
    </script>
@endsection