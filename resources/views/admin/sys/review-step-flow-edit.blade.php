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

                                    <div class="form-group @if (!empty($errors->first('dept_id'))) has-error @endif">
                                        {!! Form::label('apply_type_id', trans('app.项目类型'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::select('apply_type_id', \App\Models\Sys\HolidayConfig::$applyType,  $step->apply_type_id ?? old('apply_type_id'), [
                                            'class' => 'form-control js-select2-single',
                                            'placeholder' => trans('app.请选择', ['value' => trans('app.项目类型')]),
                                            'required' => true,
                                            'id' => 'apply_type_id'
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('status') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group @if (!empty($errors->first('child_id'))) has-error @endif">
                                        {!! Form::label('child_id', trans('app.项目子类型'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            <select class="form-control child_id" id="child_id" name="child_id"></select>
                                            <span class="help-block m-b-none">{{ $errors->first('child_id') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group @if (!empty($errors->first('max_num')) || !empty($errors->first('min_num'))) has-error @endif">
                                        {!! Form::label('child_id', trans('app.限制条件'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            <div class="input-daterange input-group">
                                                {!! Form::text('min_num', $step->min_num ?? old('min_num'), [
                                                'class' => 'input-sm form-control',
                                                ]) !!}
                                                <span class="input-group-addon" style="background-color:#eeeeee;">to</span>
                                                {!! Form::text('max_num', $step->max_num ?? old('max_num'), [
                                                'class' => 'input-sm form-control',
                                                ]) !!}
                                                <span class="help-block m-b-none">{{ $errors->first('max_num') }}</span>
                                                <span class="help-block m-b-none">{{ $errors->first('min_num') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group @if (!empty($errors->first('is_modify'))) has-error @endif">
                                        {!! Form::label('is_modify', trans('app.是否允许修改审批人'), ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-3">
                                            @foreach(\App\Models\Sys\ApprovalStep::$modifyType as $k => $v)
                                                <label class="radio-inline i-checks">
                                                    {!! Form::radio('is_modify', $k, $k === ($step->is_modify ?? old('is_modify') ?? \App\Models\Sys\ApprovalStep::MODIFY_NO), [
                                                    'required' => true,
                                                ]) !!} {{ $v }}
                                                </label>
                                            @endforeach
                                            <span class="help-block m-b-none">{{ $errors->first('child_id') }}</span>
                                        </div>
                                    </div>
                                    @foreach($step->config as $sk => $sv)
                                        <div id="copy_div_{{$sk}}" class="form-group">
                                            {!! Form::label('is_modify', '步骤流程', ['class' => 'col-sm-3 control-label']) !!}
                                            <div class="col-sm-6">
                                                <div class="form-inline">
                                                    <select id="step_order_id" name="step[{{$sk}}][step_order_id]" >
                                                        @foreach(\App\Models\Sys\ApprovalStep::$step as $k => $v)
                                                            <option value="{{ $k }}" @if($k === ($sv->step_order_id ?? old('step_order_id') ?? '')) selected="selected" @endif>{{ $v }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input id="assign_type" name="step[{{$sk}}][assign_type]" type="radio" value="0" @if($sv['assign_type'] === 0) checked="checked" @endif> {{trans('app.指定人')}}
                                                    <select id="assign_uid" name="step[{{$sk}}][assign_uid]" >
                                                        <option value="">{{trans('app.不指定')}}</option>
                                                        @foreach($userList as $k => $v)
                                                            <option value="{{ $k }}" @if($k === ($sv->assign_uid ?? old('assign_uid') ?? '')) selected="selected" @endif>{{ $v }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input id="assign_type"  name="step[{{$sk}}][assign_type]" type="radio" value="1" @if($sv['assign_type'] === 1) checked="checked" @endif > {{trans('app.指定组')}}

                                                    <select id="group_type_id"  name="step[{{$sk}}][group_type_id]" >
                                                        @foreach(\App\Models\Sys\ApprovalStep::$groupType as $k => $v)
                                                            <option value="{{ $k }}" @if($k === ($sv->group_type_id ?? old('group_type_id') ?? '')) selected="selected" @endif>{{ $v }}</option>
                                                        @endforeach
                                                    </select>

                                                    <select id="assign_role_id" name="step[{{$sk}}][assign_role_id]" >
                                                        <option value="">{{trans('app.不指定')}}</option>
                                                        @foreach($roleList as $k => $v)
                                                            <option value="{{ $k }}" @if($k === ($sv->assign_role_id ?? old('assign_role_id') ?? '')) selected="selected" @endif>{{ $v }}</option>
                                                        @endforeach
                                                    </select>

                                                    <button id="remove_ded" rel="{{$sk}}" type="button" class="btn btn-danger btn-xs">删除</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div id="add_div">

                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-6 col-sm-offset-8">
                                            {!! Form::button(trans('app.新增步骤'), ['class' => 'btn btn-success', 'id' => 'add_step']) !!}
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
@endsection
@include('widget.icheck')
@include('widget.select2')
@section('scripts-last')
    <script>
        $(function() {
            var i = {{$i}};
            $('button[id^=add_step]').click(function () {
                i++;
                var id = 'copy_div_' + i;
                var div = 'add_div';

                var html = $('#copy_div_0').clone(true).attr({'id': id});
                html.appendTo('#' + div);
                /*复制之后变量重新定义*/
                $('#' + id).find("*[id='step_order_id']").attr({'name': 'step['+i+'][step_order_id]'});
                $('#' + id).find("*[id='assign_type']").attr({'name': 'step['+i+'][assign_type]', 'class' :'radio-inline i-checks', 'rel': i});
                $('#' + id).find("*[id='assign_uid']").attr({'name': 'step['+i+'][assign_uid]', 'rel': i});
                $('#' + id).find("*[id='group_type_id']").attr({'name': 'step['+i+'][group_type_id]', 'rel': i});
                $('#' + id).find("*[id='assign_role_id']").attr({'name': 'step['+i+'][assign_role_id]', 'rel': i});
                $('#' + id).find("*[id='remove_ded']").attr({'id': 'remove_ded_' + i, 'rel': i});

            });

            $('button[id^=remove_ded]').click(function () {
                var id = $(this).attr('rel');
                if(id == 0) return false;
                $("#copy_div_" + id).remove();
                i--;
            });


            $('#apply_type_id').change(function () {
                $("#child_id").html('');
                loadChild();
            });

            loadChild();

        });

        function loadChild() {
            var applyTypeId = $("#apply_type_id").val();
            $.get("{{ route('review-step-flow.getHoliday') }}", {id: applyTypeId}, function (result) {
                if (result.status == 1) {
                    console.log(result.data);
                    $(".child_id").select2({
                        placeholder: "-请选择子项目类型-", //默认所有
                        allowClear: true, //清楚选择项
                        multiple: false,// 多选
                        data: result.data, //绑定数据
                        minimumResultsForSearch: "1"
                    });
                }
                @if(!empty($step->child_id))
                    $(".child_id").val({{$step->child_id}}).select2();
                @endif

            });
        }

    </script>
@endsection