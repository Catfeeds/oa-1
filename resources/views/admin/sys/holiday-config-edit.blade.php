@extends('admin.sys.sys')

@section('content')
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
                                {{--基本必填信息--}}
                                    <div class="form-group">
                                        {!! Form::label('apply_type_id', trans('app.配置类型'), ['class' => 'col-sm-3 control-label']) !!}
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
                                        {!! Form::label('holiday', trans('app.事件名称'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::text('holiday', isset($holiday->holiday) ? $holiday->holiday: old('holiday'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('app.请输入', ['value' => trans('app.事件名称')]),
                                            'required' => true,
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('holiday') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group @if (!empty($errors->first('show_name'))) has-error @endif">
                                        {!! Form::label('show_name', trans('app.显示事件名称'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::text('show_name', isset($holiday->show_name) ? $holiday->show_name: old('show_name'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('app.请输入', ['value' => trans('app.事件名称')]),
                                            'required' => true,
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('show_name') }}</span>
                                        </div>
                                    </div>


                                    <div class="form-group @if (!empty($errors->first('sort'))) has-error @endif">
                                        {!! Form::label('sort', trans('app.排序值'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::number('sort', isset($holiday->sort) ? $holiday->sort: old('sort'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('app.请输入', ['value' => trans('app.排序值')]),
                                            'required' => true,
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('sort') }}</span>
                                        </div>
                                        <div class="col-sm-2">
                                            <span class="help-block m-b-none">
                                                <i class="fa fa-info-circle"></i> {{ trans('app.值越大，越靠前') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('is_show', trans('app.是否显示'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-6">
                                            @foreach(\App\Models\Sys\HolidayConfig::$isShow as $k => $v)
                                                <label class="radio-inline i-checks">
                                                    {!! Form::radio('is_show', $k, $k === ($holiday->is_show ?? old('is_show') ?? \App\Models\Sys\HolidayConfig::STATUS_ENABLE), [
                                                ]) !!} {{ $v }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('is_full', trans('app.是否影响全勤'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-6">
                                            @foreach(\App\Models\Sys\HolidayConfig::$isShow as $k => $v)
                                                <label class="radio-inline i-checks">
                                                    {!! Form::radio('is_full', $k, $k === ($holiday->is_full ?? old('is_full') ?? \App\Models\Sys\HolidayConfig::STATUS_ENABLE), [
                                                ]) !!} {{ $v }}
                                                </label>
                                            @endforeach
                                        </div>
                                   </div>

                                    <div class="form-group">
                                        {!! Form::label('is_annex', trans('app.是否需要附件'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-6">
                                            @foreach(\App\Models\Sys\HolidayConfig::$isShow as $k => $v)
                                                <label class="radio-inline i-checks">
                                                    {!! Form::radio('is_annex', $k, $k === ($holiday->is_annex ?? old('is_annex') ?? \App\Models\Sys\HolidayConfig::STATUS_DISABLE), [
                                                ]) !!} {{ $v }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('is_before_after', trans('app.是否允许节假日前后申请'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-6">
                                            @foreach(\App\Models\Sys\HolidayConfig::$isShow as $k => $v)
                                                <label class="radio-inline i-checks">
                                                    {!! Form::radio('is_before_after', $k, $k === ($holiday->is_before_after ?? old('is_before_after') ?? \App\Models\Sys\HolidayConfig::STATUS_DISABLE), [
                                                ]) !!} {{ $v }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group @if (!empty($errors->first('memo'))) has-error @endif">
                                        {!! Form::label('memo', trans('app.配置描述'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-5">
                                            {!! Form::textarea('memo', isset($holiday->memo) ? $holiday->memo: old('memo'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('app.请输入', ['value' => trans('app.配置描述')]),
                                            'required' => true,
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('memo') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group @if (!empty($errors->first('cypher_type'))) has-error @endif">
                                        {!! Form::label('cypher_type', trans('app.计算类型'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-2">
                                            <select onchange="func()" class="js-select2-single form-control" id="cypher_type" name="cypher_type" >
                                                <option value="">{{trans('app.计算类型选择')}}</option>
                                                @foreach(\App\Models\Sys\HolidayConfig::$cypherType as $k => $v)
                                                    <option value="{{ $k }}" @if($k === $holiday->cypher_type) selected="selected" @endif>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            <span class="help-block m-b-none">{{ $errors->first('cypher_type') }}</span>
                                        </div>
                                    </div>
                                    {{--基本必填信息end--}}

                                    {{--无薪/带薪显示--}}
                                    <div id="unpaid" style="display: none">
                                        <div class="form-group @if (!empty($errors->first('under_day'))) has-error @endif">
                                            {!! Form::label('under_day', trans('app.请假下限天数'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('under_day', isset($holiday->under_day) ? $holiday->under_day: old('under_day'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.请假下限天数')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('under_day') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{--无薪/带薪显示end--}}

                                    {{--带薪显示--}}
                                    <div id="paid" style="display: none">
                                        <div class="form-group @if (!empty($errors->first('up_day'))) has-error @endif">
                                            {!! Form::label('up_day', trans('app.请假计薪上限天数'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('up_day', isset($holiday->up_day) ? $holiday->up_day: old('up_day'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.请假计薪上限天数')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('up_day') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('cycle_num'))) has-error @endif">
                                            {!! Form::label('cycle_num', trans('app.周期内可请假次数'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('cycle_num', isset($holiday->cycle_num) ? $holiday->cycle_num: old('cycle_num'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.周期内可请假次数')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('cycle_num') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('payable'))) has-error @endif">
                                            {!! Form::label('payable', trans('app.计薪比例'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('payable', isset($holiday->payable) ? $holiday->payable: old('payable'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.计薪比例')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('payable') }}</span>
                                            </div>
                                            <div class="col-sm-2">
                                                <span class="help-block m-b-none">
                                                    <i class="fa fa-info-circle"></i> {{ trans('每日计薪百分比') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('reset_type', trans('app.周期类型'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-6">

                                                @foreach(\App\Models\Sys\HolidayConfig::$resetType as $k => $v)
                                                    <label class="radio-inline i-checks">
                                                        {!! Form::radio('reset_type', $k, $k === ($holiday->reset_type ?? old('reset_type') ?? \App\Models\Sys\HolidayConfig::NO_SETTING), ['id'
                                                        => 'reset_type'
                                                    ]) !!} {{ $v }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('payable_reset_formula'))) has-error @endif">
                                            {!! Form::label('payable_reset_formula', trans('app.计薪天数重置周期'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('payable_reset_formula', isset($holiday->payable_reset_formula) ? $holiday->payable_reset_formula: old('payable_reset_formula'), [
                                                'class' => 'form-control',
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('payable_reset_formula') }}</span>
                                            </div>
                                            <div class="col-sm-2">
                                                <span id="show_i" class="help-block m-b-none">
                                                    <i class="fa fa-info-circle"></i> {{ trans('公式:[年,月,日,时,分,秒]') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('payable_claim_formula'))) has-error @endif">
                                            {!! Form::label('payable_claim_formula', trans('app.计薪天数起始要求'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('payable_claim_formula', isset($holiday->payable_claim_formula) ? $holiday->payable_claim_formula: old('payable_claim_formula'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.计薪天数起始要求')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('payable_claim_formula') }}</span>
                                            </div>
                                            <div class="col-sm-2">
                                                <span class="help-block m-b-none">
                                                    <i class="fa fa-info-circle"></i> {{ trans('公式:[年,月,日,时,分,秒]') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('payable_self_growth'))) has-error @endif">
                                            {!! Form::label('payable_self_growth', trans('app.计薪天数自增长'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('payable_self_growth', isset($holiday->payable_self_growth) ? $holiday->payable_self_growth: old('payable_self_growth'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.每个循环周期增长的计薪天数')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('payable_self_growth') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('restrict_sex', trans('app.限制男女'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-6">

                                                @foreach(array_merge(\App\Models\UserExt::$sex, ['不限'])  as $k => $v)
                                                    <label class="radio-inline i-checks">
                                                        {!! Form::radio('restrict_sex', $k, $k === ($holiday->restrict_sex ?? old('restrict_sex') ?? \App\Models\UserExt::SEX_NO_RESTRICT), [
                                                    ]) !!} {{ $v }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('exceed_change_id'))) has-error @endif">
                                            {!! Form::label('exceed_change_id', trans('app.超出计薪天数转换'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-6">
                                                <select style="width: 15em" name="exceed_change_id" class="js-select2-single form-control" >
                                                    <option value="">{{trans('app.转换配置选择')}}</option>
                                                    @foreach(\App\Models\Sys\HolidayConfig::holidayList() as $k => $v)
                                                        <option value="{{ $k }}" @if($k === $holiday->exceed_change_id) selected="selected" @endif>{{ $v }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="help-block m-b-none">{{ $errors->first('exceed_change_id') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{--带薪显示end--}}

                                    {{--加班/调休显示--}}
                                    <div id="delay" style="display: none">
                                        <div class="form-group @if (!empty($errors->first('work_relief_formula'))) has-error @endif">
                                            {!! Form::label('work_relief_formula', trans('app.上下班时间减免时长'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('work_relief_formula', isset($holiday->work_relief_formula) ? $holiday->work_relief_formula: old('work_relief_formula'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.上下班时间减免时长')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('work_relief_formula') }}</span>
                                            </div>
                                            <div class="col-sm-2">
                                                <span class="help-block m-b-none">
                                                    <i class="fa fa-info-circle"></i> {{ trans('公式:[年,月,日,时,分,秒]') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('work_relief_type'))) has-error @endif">
                                            {!! Form::label('work_relief_type', trans('app.上下班时间减免类型'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-6">
                                                @foreach(\App\Models\Sys\HolidayConfig::$reliefType as $k => $v)
                                                    <label class="radio-inline i-checks">
                                                        {!! Form::radio('work_relief_type', $k, $k === ($holiday->work_relief_type ?? old('work_relief_type') ?? \App\Models\Sys\HolidayConfig::NO_SETTING), [
                                                    ]) !!} {{ $v }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('work_relief_cycle_num'))) has-error @endif">
                                            {!! Form::label('work_relief_cycle_num', trans('app.上下班时间循环周期'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('work_relief_cycle_num', isset($holiday->work_relief_cycle_num) ? $holiday->work_relief_cycle_num: old('work_relief_cycle_num'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.上下班时间循环周期')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('work_relief_cycle_num') }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group @if (!empty($errors->first('add_pop'))) has-error @endif">
                                            {!! Form::label('add_pop', trans('app.加班换算调休假比例'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('add_pop', isset($holiday->add_pop) ? $holiday->add_pop: old('add_pop'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.加班换算调休假比例')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('add_pop') }}</span>
                                            </div>

                                            <div class="col-sm-2">
                                                <span class="help-block m-b-none">
                                                    <i class="fa fa-info-circle"></i> {{ trans('单位:百分比') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    {{--加班/调休显示end--}}
                                    {{--加班调休显示--}}
                                    <div id="change" style="display: none">
                                        <div class="form-group @if (!empty($errors->first('work_reset_formula'))) has-error @endif">
                                            {!! Form::label('work_relief_formula', trans('app.加班调休重置周期'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('work_reset_formula', isset($holiday->work_reset_formula) ? $holiday->work_reset_formula: old('work_relief_formula'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.上下班时间减免时长')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('work_reset_formula') }}</span>
                                            </div>
                                            <div class="col-sm-2">
                                                    <span class="help-block m-b-none">
                                                        <i class="fa fa-info-circle"></i> {{ trans('公式:[月,日,时,分,秒]') }}
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                    {{--加班调休显示end--}}

                                    {{--打卡显示--}}
                                    <div id="recheck" style="display: none">
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
                                    </div>
                                    {{--打卡显示end--}}

                                    {{--加班调休显示--}}
                                    <div id="duration" style="display: none">
                                        <div class="form-group @if (!empty($errors->first('duration'))) has-error @endif">
                                            {!! Form::label('duration', trans('app.夜班加班调休起效时长'), ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-3">
                                                {!! Form::text('duration', isset($holiday->duration) ? $holiday->duration: old('duration'), [
                                                'class' => 'form-control',
                                                'placeholder' => trans('app.请输入', ['value' => trans('app.夜班加班调休起效时长')]),
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('duration') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{--加班调休显示end--}}


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

@endsection
@include('widget.icheck')
@include('widget.select2')
@push('scripts')
<script>
    $(function () {

        $("#cypher_type").trigger('onchange');


        $('input[name="reset_type"]').on('ifChecked', function () {

            if ($(this).val() == "2") {
                $('#payable_reset_formula').val('[0,0,0,0,0]');
                $('#show_i').html(' <i class="fa fa-info-circle"></i> {{ trans('公式:[月,日,时,分,秒]') }}')
            } else {
                $('#payable_reset_formula').val('[0,0,0,0,0,0]');
                $('#show_i').html(' <i class="fa fa-info-circle"></i> {{ trans('公式:[年,月,日,时,分,秒]') }}')
            }
        });

    });

    function func(){
        var val = $('#cypher_type').children('option:selected').val();
        switch (val) {
            case '1':
                $("#unpaid").show();
                $("#paid").hide();
                $("#recheck").hide();
                $("#change").hide();
                $("#delay").hide();
                $("#duration").hide();
                break;
            case '2':
                $("#unpaid").show();
                $("#paid").show();
                $("#recheck").hide();
                $("#change").hide();
                $("#delay").hide();
                $("#duration").hide();
                break;
            case '3':
                $("#delay").show();
                $("#recheck").hide();
                $("#unpaid").hide();
                $("#paid").hide();
                $("#change").hide();
                $("#duration").hide();
                break;
            case '4':
                $("#change").show();
                $("#delay").hide();
                $("#unpaid").hide();
                $("#paid").hide();
                $("#recheck").hide();
                $("#duration").hide();
                break;
            case '6':
                $("#recheck").show();
                $("#unpaid").hide();
                $("#paid").hide();
                $("#change").hide();
                $("#delay").hide();
                $("#duration").hide();
                break;
            case '8':
                $("#duration").show();
                $("#recheck").hide();
                $("#unpaid").hide();
                $("#paid").hide();
                $("#change").hide();
                $("#delay").hide();
                break;
            default:
                $("#duration").hide();
                $("#change").hide();
                $("#unpaid").hide();
                $("#paid").hide();
                $("#recheck").hide();
                $("#delay").hide();
        }
    }

</script>
@endpush