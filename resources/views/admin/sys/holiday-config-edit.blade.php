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
                                                {!! Form::label('apply_type_id', trans('app.申请类型'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    @foreach(\App\Models\Sys\HolidayConfig::$applyType as $k => $v)
                                                        @if($k != 0)
                                                        <label class="radio-inline i-checks">
                                                            {!! Form::radio('apply_type_id', $k, $k === ($holiday->apply_type_id ?? old('apply_type_id') ?? 1), [
                                                            'required' => true,
                                                        ]) !!} {{ $v }}
                                                        </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>

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
                                                    {!! Form::text('num', isset($holiday->num) ? $holiday->num: old('num'), [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('app.请输入', ['value' => trans('app.假期天数')]),
                                                    'required' => true,
                                                    ]) !!}
                                                    <span class="help-block m-b-none">{{ $errors->first('num') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('is_boon', trans('app.是否员工福利假'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    @foreach(\App\Models\Sys\HolidayConfig::$isBoon as $k => $v)
                                                        <label class="radio-inline i-checks">
                                                            {!! Form::radio('is_boon', $k, $k === ($holiday->is_boon ?? old('is_boon') ?? \App\Models\Sys\HolidayConfig::STATUS_DISABLE), [
                                                        ]) !!} {{ $v }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('is_annex', trans('app.是否必须上传附件'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    @foreach(\App\Models\Sys\HolidayConfig::$isBoon as $k => $v)
                                                        <label class="radio-inline i-checks">
                                                            {!! Form::radio('is_annex', $k, $k === ($holiday->is_annex ?? old('is_annex') ?? \App\Models\Sys\HolidayConfig::STATUS_DISABLE), [
                                                        ]) !!} {{ $v }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('is_renew', trans('app.福利假使用完是否可再提交申请假期'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    @foreach(\App\Models\Sys\HolidayConfig::$isBoon as $k => $v)
                                                        <label class="radio-inline i-checks">
                                                            {!! Form::radio('is_renew', $k, $k === ($holiday->is_renew ?? old('is_renew') ?? \App\Models\Sys\HolidayConfig::STATUS_DISABLE), [
                                                        ]) !!} {{ $v }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('restrict_sex', trans('app.限制男女'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    @foreach(\App\Models\UserExt::$sex + [ 2 => '不限'] as $k => $v)
                                                        <label class="radio-inline i-checks">
                                                            {!! Form::radio('restrict_sex', $k, $k === ($holiday->restrict_sex ?? old('restrict_sex') ?? 2), [
                                                        ]) !!} {{ $v }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('punch_type', trans('app.补打卡设置'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-6">
                                                    @foreach(\App\Models\Sys\HolidayConfig::$punchType as $k => $v)
                                                        <label class="radio-inline i-checks">
                                                            {!! Form::radio('punch_type', $k, $k === ($holiday->punch_type ?? old('punch_type') ?? \App\Models\Sys\HolidayConfig::STATUS_DISABLE), [
                                                        ]) !!} {{ $v }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>


                                            <div class="form-group @if (!empty($errors->first('condition_id'))) has-error @endif">
                                                {!! Form::label('condition_id', trans('app.是否需要重置条件'), ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-2">
                                                    <select class="js-select2-single form-control" name="condition_id" >
                                                        <option value="">重置条件选择</option>
                                                        @foreach(\App\Models\Sys\HolidayConfig::$condition as $k => $v)
                                                            <option value="{{ $k }}" @if($k === $holiday->condition_id) selected="selected" @endif>{{ $v }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="help-block m-b-none">{{ $errors->first('condition_id') }}</span>
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
@include('widget.icheck')
@include('widget.select2')
@push('scripts')
<script>


</script>
@endpush