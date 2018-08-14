@extends('admin.sys.sys')

@section('content')
    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $title ?? trans('app.游戏设置') }}</h5>
                        </div>
                        <div class="ibox-content">

                            @include('flash::message')

                            <div class="panel-heading">
                                <div class="panel blank-panel">
                                    <div class="panel-options">
                                        <ul class="nav nav-tabs">
                                            @include('admin.sys._link-tabs')
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="tab-content">
                                    <div class="tab-pane active">
                                        <div class="ibox-content profile-content">
                                            {!! Form::open(['class' => 'form-horizontal']) !!}

                                            <div class="form-group @if (!empty($errors->first('holiday'))) has-error @endif">
                                                {!! Form::label('holiday', trans('app.假期名称'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::text('holiday', isset($holiday->holiday) ? $holiday->holiday: old('holiday'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.假期名称')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('holiday') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('num'))) has-error @endif">
                                                {!! Form::label('num', trans('app.假期天数'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::number('num', isset($holiday->num) ? $holiday->num: old('num'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.假期天数')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('num') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('memo'))) has-error @endif">
                                                {!! Form::label('memo', trans('app.假期描述'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::textarea('memo', isset($holiday->memo) ? $holiday->memo: old('memo'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.假期描述')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('memo') }}</span>
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
                </div>
            </div>
        </div>
    </div>

@endsection