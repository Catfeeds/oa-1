@extends('layouts.top-nav')

@section('title', $title)
@section('body-class', 'top-navigation')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title }}</h5>

                            @if(Entrust::can(['user-all', 'user']))
                            <div class="ibox-tools">
                                <a class="btn btn-xs btn-primary" href="{{ route('user') }}">
                                    {{ trans('app.员工列表') }}
                                </a>
                            </div>
                            @endif

                        </div>
                        <div class="ibox-content">

                            {!! Form::open(['class' => 'form-horizontal']) !!}

                            <div class="form-group @if (!empty($errors->first('username'))) has-error @endif">
                                {!! Form::label('username', trans('app.账号'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {{-- 账号不可编辑 --}}
                                    {!! Form::text('username', isset($user->username) ? $user->username : old('username'), isset($user->username) ? [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.账号')]),
                                    'required' => true,
                                    'disabled',
                                    ] : [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.账号')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('username') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('alias'))) has-error @endif">
                                {!! Form::label('alias', trans('app.名称'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::text('alias', isset($user->alias) ? $user->alias : old('alias'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.名称')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('alias') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('password'))) has-error @endif">
                                {!! Form::label('password', trans('app.密码'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {{-- 编辑密码可选 --}}
                                    {!! Form::password('password', isset($user->password) ? [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入密码，为空则不变'),
                                    ] : [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.密码')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('password') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('password_confirmation'))) has-error @endif">
                                {!! Form::label('password_confirmation', trans('app.确认密码'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {{-- 编辑密码可选 --}}
                                    {!! Form::password('password_confirmation', isset($user->password) ? [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请再次输入密码，为空则不变'),
                                    ] : [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请再次输入密码'),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('password_confirmation') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('email'))) has-error @endif">
                                {!! Form::label('email', trans('app.邮箱'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::email('email', isset($user->email) ? $user->email : old('email'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.邮箱')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('email') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('dept_id'))) has-error @endif">
                                {!! Form::label('dept_id', trans('app.部门'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::select('dept_id', $dept, isset($user->dept_id) ? $user->dept_id: old('dept_id'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请选择', ['value' => trans('app.部门')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('status') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('role_id'))) has-error @endif">
                                {!! Form::label('role_id', trans('app.职务'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::select('role_id', $roleList, isset($user->role_id) ? $user->role_id : old('role_id'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请选择', ['value' => trans('app.职务')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('role_id') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('job_id'))) has-error @endif">
                                {!! Form::label('job_id', trans('app.岗位'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::select('job_id', $job, isset($user->job_id) ? $user->job_id: old('job_id'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请选择', ['value' => trans('app.岗位')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('job_id') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('status'))) has-error @endif">
                                {!! Form::label('status', trans('app.状态'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::select('status', \App\User::getStatusList(), isset($user->status) ? $user->status: old('status'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请选择', ['value' => trans('app.状态')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('status') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('is_mobile'))) has-error @endif">
                                {!! Form::label('is_mobile', '验证登录', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::select('is_mobile', \App\User::$isMobileList, isset($user->is_mobile) ? $user->is_mobile : old('is_mobile'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请选择', ['value' => '是否登录验证手机']),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('is_mobile') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('mobile'))) has-error @endif">
                                {!! Form::label('mobile', '手机号码', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::text('mobile', isset($user->mobile) ? $user->mobile : old('mobile'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => '手机号码']),
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('mobile') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('desc', trans('app.备注'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::textarea('desc', isset($user->desc) ? $user->desc : old('desc'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.备注')]),
                                    ]) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                                </div>
                            </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('widget.select2')

@section('scripts-last')
<script>
    $(function() {
        $('#role_id').select2();
        $('#status').select2();
        $('#is_mobile').select2();
        $('#dept_id').select2();
        $('#job_id').select2();
    });
</script>
@endsection
