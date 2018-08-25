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

                            @if(Entrust::can(['role-all', 'role']))
                            <div class="ibox-tools">
                                <a class="btn btn-xs btn-primary" href="{{ route('role') }}">
                                    {{ trans('app.列表', ['value' => trans('app.职务')]) }}
                                </a>
                            </div>
                            @endif

                        </div>
                        <div class="ibox-content">

                            {!! Form::open(['class' => 'form-horizontal']) !!}

                            <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                                {!! Form::label('name', trans('app.职务'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::text('name', isset($role->name) ? $role->name : old('name'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.职务')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('display_name'))) has-error @endif">
                                {!! Form::label('display_name', trans('app.名称'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::text('display_name', isset($role->display_name) ? $role->display_name : old('display_name'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.名称')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('display_name') }}</span>
                                </div>
                            </div>

                            <div class="form-group @if (!empty($errors->first('description'))) has-error @endif">
                                {!! Form::label('description', trans('app.描述'), ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6">
                                    {!! Form::text('description', isset($role->description) ? $role->description : old('description'), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.请输入', ['value' => trans('app.描述')]),
                                    'required' => true,
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('description') }}</span>
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