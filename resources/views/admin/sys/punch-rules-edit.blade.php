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

                                            <div class="form-group">
                                                {!! Form::label('punch_type_id', '规则类型', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    @foreach(\App\Models\Sys\PunchRules::$punchType as $k => $v)
                                                        <label class="radio-inline i-checks">
                                                            {!! Form::radio('punch_type_id', $k, $k === ($punchRules->punch_type_id ?? old('punch_type_id') ?? 1), [
                                                            'required' => true,
                                                        ]) !!} {{ $v }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                                                {!! Form::label('name', trans('app.规则名称'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::text('name', isset($punchRules->name) ? $punchRules->name: old('name'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.规则名称')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('ready_time'))) has-error @endif">
                                                {!! Form::label('ready_time', trans('app.上班准备时间'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        {!! Form::text('ready_time', !empty($punchRules->ready_time) ? $punchRules->ready_time : (old('ready_time') ?? '') , [
                                                        'class' => 'form-control date_h',
                                                        'required' => true,
                                                        ]) !!}
                                                        <span class="help-block m-b-none">{{ $errors->first('ready_time') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('work_start_time'))) has-error @endif">
                                                {!! Form::label('work_start_time', trans('app.上班时间'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        {!! Form::text('work_start_time', !empty($punchRules->work_start_time) ? $punchRules->work_start_time : (old('work_start_time') ?? '') , [
                                                        'class' => 'form-control date_h',
                                                        'required' => true,
                                                        ]) !!}
                                                        <span class="help-block m-b-none">{{ $errors->first('work_start_time') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('work_end_time'))) has-error @endif">
                                                {!! Form::label('work_end_time', trans('app.下班时间'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        {!! Form::text('work_end_time', !empty($punchRules->work_end_time) ? $punchRules->work_end_time : (old('work_end_time') ?? '') , [
                                                        'class' => 'form-control date_h',
                                                        'required' => true,
                                                        ]) !!}
                                                        <span class="help-block m-b-none">{{ $errors->first('work_end_time') }}</span>
                                                    </div>
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
@include('widget.icheck')
@include('widget.datepicker')