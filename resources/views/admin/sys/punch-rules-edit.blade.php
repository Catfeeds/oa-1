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

                                    <div class="form-group">
                                        {!! Form::label('punch_type_id', '规则类型', ['class' => 'col-sm-1 control-label']) !!}
                                        <div class="col-sm-3">
                                            @foreach(\App\Models\Sys\PunchRules::$punchType as $k => $v)
                                                <label class="radio-inline i-checks">
                                                    {!! Form::radio('punch_type_id', $k, $k === ($punchRules['punch_type_id'] ?? old('punch_type_id') ?? 1), [
                                                    'required' => true,
                                                ]) !!} {{ $v }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                                        {!! Form::label('name', trans('app.规则名称'), ['class' => 'col-sm-1 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::text('name', isset($punchRules['name']) ? $punchRules['name']: old('name'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('app.请输入', ['value' => trans('app.规则名称')]),
                                            'required' => true,
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                                        </div>
                                    </div>

                                    @foreach($punchRules['config'] as $key => $val)
                                    <div id="copy_rule_{{$key}}" class="form-group">
                                        {{--折叠控件--}}
                                        <div class="panel-group" id="accordion">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 id="h3_a" class="panel-title col-sm-1 control-label">
                                                        <a href="#collapseOne" data-toggle="collapse0" data-parent="#accordion">上下班时间</a>
                                                    </h3>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="input-daterange input-group">
                                                                {!! Form::text('work['.$key.'][work_start_time]', $val['work_start_time'] ?? old('work_start_time'), [
                                                                'class' => 'input-sm form-control',
                                                                'id' => 'work_start_time',
                                                                ]) !!}
                                                                <span class="input-group-addon" style="background-color:#eeeeee;">to</span>
                                                                {!! Form::text('work['.$key.'][work_end_time]', $val['work_end_time'] ?? old('work_end_time'), [
                                                                'class' => 'input-sm form-control',
                                                                'id' => 'work_end_time',
                                                                ]) !!}
                                                                <span class="input-group-addon" >{!!trans('app.上下班准备时间')!!}</span>
                                                                {!! Form::text('work['.$key.'][ready_time]', !empty($val['ready_time']) ? $val['ready_time'] : (old('ready_time') ?? '') , [
                                                                'class' => 'input-sm form-control',
                                                                'id' => 'ready_time',
                                                                 ]) !!}
                                                                <span class="help-block m-b-none">{{ $errors->first('work_start_time') }}</span>
                                                                <span class="help-block m-b-none">{{ $errors->first('work_end_time') }}</span>
                                                            </div>
                                                        </div>
                                                        <button id="remove_rule" rel="{{$key}}" type="button" class="btn btn-danger btn-xs">删除</button>
                                                    </div>

                                                    <div id="collapseOne" class="panel-collapse collapse in">
                                                        <div class="panel-body">
                                                            {{--拷贝需要填入的地方--}}
                                                            <div rel="{{$key}}" id="create_div_{{$key}}">
                                                                @foreach($val['cfg'] as $vk => $vval)
                                                                {{--需要拷贝的内容--}}
                                                                <div id="row_copy_{{$key.$vk}}" class="row">
                                                                    <div class="col-md-10">
                                                                        <div class="row">
                                                                            <div class="form-inline">
                                                                                {!! Form::text('work['.$key.'][cfg]['.$vk.'][rule_desc]', $vval['rule_desc'] ?? old('rule_desc'), [
                                                                                   'class' => 'input-sm form-control',
                                                                                   'id' => 'rule_desc',
                                                                                   'placeholder' => trans('app.请输入', ['value' => trans('app.规则描述')])
                                                                                   ]) !!}
                                                                                <select id="late_type" name="work[{{$key}}][cfg][{{$vk}}][late_type]">
                                                                                    @foreach(\App\Models\Sys\PunchRules::$lateType as $k => $v)
                                                                                        <option value="{{ $k }}" @if($k === ($vval['late_type'] ?? old('late_type') ?? 0)) selected="selected" @endif>{{ $v }}</option>
                                                                                    @endforeach
                                                                                </select>

                                                                                <div class="input-daterange input-group">
                                                                                    {!! Form::text('work['.$key.'][cfg]['.$vk.'][start_gap]', $vval['start_gap'] ?? '[0,0,0,0,0,0]', [
                                                                                    'class' => 'input-sm form-control ',
                                                                                    'id' => 'start_gap',
                                                                                    ]) !!}
                                                                                    <span class="input-group-addon" style="background-color:#eeeeee;">to</span>
                                                                                    {!! Form::text('work['.$key.'][cfg]['.$vk.'][end_gap]', $vval['end_gap'] ?? '[0,0,0,0,0,0]', [
                                                                                    'class' => 'input-sm form-control ',
                                                                                    'id' => 'end_gap',
                                                                                    ]) !!}
                                                                                </div>

                                                                                {!! Form::label('group_id', '扣分类型') !!}
                                                                                <input id="ded_type1" name="work[{{$key}}][cfg][{{$vk}}][ded_type][{{$vk}}]" type="radio" value="1" @if($vval['ded_type'] == 1) checked="checked" @endif> 分数
                                                                                <input id="ded_type2" name="work[{{$key}}][cfg][{{$vk}}][ded_type][{{$vk}}]" type="radio" value="2" @if($vval['ded_type'] == 2) checked="checked" @endif>
                                                                                <select id="holiday_id" name="work[{{$key}}][cfg][{{$vk}}][holiday_id]" >
                                                                                    @foreach(\App\Models\Sys\HolidayConfig::holidayList() as $k => $v)
                                                                                        <option value="{{ $k }}" @if($k === ($vval['holiday_id'] ?? old('holiday_id') ?? 0)) selected="selected" @endif>{{ $v }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                {!! Form::text('work['.$key.'][cfg]['.$vk.'][ded_num]', $vval['ded_num'] ?? old('ded_num'), [
                                                                                    'class' => 'input-sm form-control ',
                                                                                    'id' => 'ded_num',
                                                                                ]) !!}
                                                                                <button id="remove_ded" rel="{{$key.$vk}}" key="{{$vk}}" type="button" class="btn btn-danger btn-xs">删除</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                                {{--需要拷贝的内容end--}}
                                                            </div>
                                                            {{--拷贝需要填入的地方end--}}

                                                            <div class="col-sm-offset-11">
                                                                {!! Form::button(trans('新增扣分规则'), ['class' => 'btn btn-info', 'id' => 'add_ded_'. $key, 'rel' => $key, 'key' => 0]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    <div id="create_rule">
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-6 col-sm-offset-10">
                                            {!! Form::button(trans('新增时间段'), ['class' => 'btn btn-primary', 'id' => 'add_create_rule', 'rel' => $div]) !!}
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
@include('widget.datepicker')
@include('widget.select2')
@section('scripts-last')
    <script>
        $(function() {

            var i = 100;
            var copy_key = 100;

            $('button[id^=add_ded_]').click(function () {
                i++;
                var key = $(this).attr('rel');
                var id = 'row_copy_' + i;
                var div = 'create_div_' + key;

                var html = $('#' + div).find("#row_copy_" + key + $(this).attr('key')).clone(true).attr({'id': id});
                html.appendTo('#' + div);

                /*复制之后变量重新定义*/
                $('#' + id).find("button[id='remove_ded']").attr({'id': 'remove_ded_'+ i, 'rel': i, 'key' : i});
                $('#' + id).find("*[id=ded_type1]").attr({'name': 'work['+ $(this).attr('rel') +'][cfg]['+i+'][ded_type]['+i+']', 'rel': i});
                $('#' + id).find("*[id=ded_type2]").attr({'name': 'work['+ $(this).attr('rel') +'][cfg]['+i+'][ded_type]['+i+']' , 'rel': i});
                $('#' + id).find("*[id=rule_desc]").attr({'name': 'work['+ key +'][cfg]['+ i +'][rule_desc]'});
                $('#' + id).find("*[id=late_type]").attr({'name': 'work['+ key +'][cfg]['+ i +'][late_type]'});
                $('#' + id).find("*[id=start_gap]").attr({'name': 'work['+ key +'][cfg]['+ i +'][start_gap]'});
                $('#' + id).find("*[id=end_gap]").attr({'name': 'work['+ key +'][cfg]['+ i +'][end_gap]'});
                $('#' + id).find("*[id=holiday_id]").attr({'name': 'work['+ key +'][cfg]['+ i +'][holiday_id]'});
                $('#' + id).find("*[id=ded_num]").attr({'name': 'work['+ key +'][cfg]['+ i +'][ded_num]'});

            });

            $('button[id^=remove_ded]').click(function () {
                var id = $(this).attr('rel');
                if($(this).attr('key') == 0) return false;
                $("#row_copy_" + id).remove();
            });


            $('#add_create_rule').click(function () {
                i++;
                var id = 'create_rule_' + copy_key;
                var html = $("#copy_rule_{{$div}}").clone(true).attr({'id': id});
                html.appendTo('#create_rule');

                $('#' + id).find('div[id^=row_copy_]').remove();

                var h3_a = '<a href="#collapseOne'+copy_key+'" data-toggle="collapse" data-parent="#accordion'+copy_key+'">上下班时间'+copy_key+'</a>';
                $('#' + id).find("h3[id^=h3_a]").html(h3_a);
                $('#' + id).find("div[id^=collapseOne]").attr({'id': 'collapseOne'+ copy_key});
                $('#' + id).find("div[id^=accordion]").attr({'id': 'accordion'+ copy_key});

                $('#' + id).find("div[id^=create_div]").attr({'id': 'create_div_'+ copy_key, 'rel': copy_key});

                var row_copy = $("#row_copy_"+ $(this).attr('rel') + 0).clone(true).attr({'id': 'row_copy_' + copy_key + 0});
                row_copy.appendTo('#create_div_'+ copy_key);

                $('#' + id).find("button[id='remove_rule']").attr({'id': 'remove_rule_'+ copy_key, 'rel': copy_key});

                $('#' + id).find("button[id^=add_ded]").attr({'id': 'add_ded_'+ copy_key,'rel': + copy_key , 'key': 0});

                /*复制之后变量重新定义*/
                $('#' + id).find("button[id='remove_ded']").attr({'rel': i, 'key' : 0});

                $('#' + id).find("*[id=work_start_time]").attr({'name': 'work['+ copy_key +'][work_start_time]', 'id': 'work_start_time' + copy_key });
                $('#' + id).find("*[id=work_end_time]").attr({'name': 'work['+ copy_key +'][work_end_time]', 'id': 'work_end_time' + copy_key });
                $('#' + id).find("*[id=ready_time]").attr({'name': 'work['+ copy_key +'][ready_time]', 'id': 'ready_time' + copy_key });
                $('#' + id).find("*[id=rule_desc]").attr({'name': 'work['+ copy_key +'][cfg]['+ i +'][rule_desc]'});
                $('#' + id).find("*[id=late_type]").attr({'name': 'work['+ copy_key +'][cfg]['+ i +'][late_type]'});
                $('#' + id).find("*[id=start_gap]").attr({'name': 'work['+ copy_key +'][cfg]['+ i +'][start_gap]'});
                $('#' + id).find("*[id=end_gap]").attr({'name': 'work['+ copy_key +'][cfg]['+ i +'][end_gap]'});
                $('#' + id).find("*[id=holiday_id]").attr({'name': 'work['+ copy_key +'][cfg]['+ i +'][holiday_id]'});
                $('#' + id).find("*[id=ded_num]").attr({'name': 'work['+ copy_key +'][cfg]['+ i +'][ded_num]'});
                $('#' + id).find("*[id=ded_type1]").attr({'name': 'work['+ copy_key +'][cfg]['+ i +'][ded_type]['+ i +']'});
                $('#' + id).find("*[id=ded_type2]").attr({'name': 'work['+ copy_key +'][cfg]['+ i +'][ded_type]['+ i +']'});

                copy_key ++;

            });

            $('button[id^=remove_rule]').click(function () {
                id = $(this).attr('rel');
                if(id == {{$div}}) return false;
                $("#copy_rule_" + id).remove();
                $("#create_rule_" + id).remove();
            })

        });
    </script>
@endsection