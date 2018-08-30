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

                                            <div class="form-group @if (!empty($errors->first('year'))) has-error @endif">
                                                {!! Form::label('year', trans('app.年'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::number('year', isset($calendar->year) ? $calendar->year : old('year'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.年')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('year') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('month'))) has-error @endif">
                                                {!! Form::label('year', trans('app.月'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::number('month', isset($calendar->month) ? $calendar->month : old('month'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.月')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('month') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('day'))) has-error @endif">
                                                {!! Form::label('day', trans('app.日'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::number('day', isset($calendar->year) ? $calendar->day : old('day'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.日')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('day') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('week'))) has-error @endif">
                                                {!! Form::label('week', trans('app.周'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-2">
                                                    <select class="js-select2-single form-control" name="week" >
                                                        <option value="">请选择周几</option>
                                                        @foreach(\App\Models\Sys\Calendar::$week as $k => $v)
                                                            <option value="{{ $k }}" @if($k === $calendar->week) selected="selected" @endif>{{ $v }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="help-block m-b-none">{{ $errors->first('week') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('punch_rules_id'))) has-error @endif">
                                                {!! Form::label('punch_rules_id', trans('app.排班规则'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-2">
                                                    <select class="js-select2-single form-control" name="punch_rules_id" >
                                                        <option value="">请选择排班规则</option>
                                                        @foreach(\App\Models\Sys\PunchRules::getPunchRulesList() as $k => $v)
                                                            <option value="{{ $k }}" @if($k === $calendar->punch_rules_id) selected="selected" @endif>{{ $v }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="help-block m-b-none">{{ $errors->first('punch_rules_id') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group @if (!empty($errors->first('memo'))) has-error @endif">
                                                {!! Form::label('memo', trans('app.备注'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    {!! Form::textarea('memo', isset($calendar->memo) ? $calendar->memo : old('memo'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.备注')]),
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
@include('widget.select2')