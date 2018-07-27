@extends('profile.profile')

@section('content-profile')

    <div class="alert alert-info">
        {{ trans('app.重置密码成功后需要重新登录') }}
    </div>

    {!! Form::open(['class' => 'form-horizontal']) !!}

    <div class="form-group @if (!empty($errors->first('password'))) has-error @endif">
        {!! Form::label('password', trans('app.密码'), ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-6">
            {!! Form::password('password', [
            'class' => 'form-control',
            'placeholder' => trans('app.请输入', ['value' => trans('app.密码')]),
            'required' => true,
            ]) !!}
            <span class="help-block m-b-none">{{ $errors->first('password') }}</span>
        </div>
    </div>

    <div class="form-group @if (!empty($errors->first('password'))) has-error @endif">
        {!! Form::label('password_confirmation', trans('app.确认密码'), ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-6">
            {!! Form::password('password_confirmation', [
            'class' => 'form-control',
            'placeholder' => trans('app.请再次输入密码'),
            'required' => true,
            ]) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

@endsection
