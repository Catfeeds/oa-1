@extends('sys.sys')

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
                                    @include('sys._link-tabs')
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
                                                    {!! Form::radio('punch_type_id', $k, $k === ($punchRules->punch_type_id ?? old('punch_type_id') ?? 1), [
                                                    'required' => true,
                                                ]) !!} {{ $v }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group @if (!empty($errors->first('name'))) has-error @endif">
                                        {!! Form::label('name', trans('app.规则名称'), ['class' => 'col-sm-1 control-label']) !!}
                                        <div class="col-sm-3">
                                            {!! Form::text('name', isset($punchRules->name) ? $punchRules->name: old('name'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('app.请输入', ['value' => trans('app.规则名称')]),
                                            'required' => true,
                                            ]) !!}
                                            <span class="help-block m-b-none">{{ $errors->first('name') }}</span>
                                        </div>
                                    </div>
                                  
                                    <div id="copy_rule" class="form-group">
                                    {{--折叠控件--}}
                                        <div class="panel-group" id="accordion">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 id="h3_a" class="panel-title col-sm-1 control-label">
                                                    <a href="#collapseOne" data-toggle="collapse0" data-parent="#accordion">{{trans('上下班时间')}}</a>
                                                </h3>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-inline">
                                                            <label>{{trans('上班时间显示:')}}</label>
                                                            <input id="show_start_yes" name="work[{{$key}}][is_show_start]" type="radio" value="0" @if(empty($punchRules->is_show_start)) checked="checked" @endif> 是
                                                            <input id="show_start_no" name="work[{{$key}}][is_show_start]" type="radio" value="1" @if(!empty($punchRules->is_show_start)) checked="checked" @endif> 否
                                                            {!! Form::text('work['.$key.'][work_start_time]', $punchRules->work_start_time ?? (old('work_start_time') ?? '[0,0,0,0,0,0]'), [
                                                            'class' => 'input-sm form-control',
                                                            'id' => 'work_start_time',
                                                            ]) !!}

                                                            <label>{{trans('下班时间显示:')}}</label>
                                                            <input id="show_end_yes" name="work[{{$key}}][is_show_end]" type="radio" value="0" @if(empty($punchRules->is_show_end)) checked="checked" @endif> 是
                                                            <input id="show_end_no" name="work[{{$key}}][is_show_end]" type="radio" value="1" @if(!empty($punchRules->is_show_end)) checked="checked" @endif> 否
                                                            {!! Form::text('work['.$key.'][work_end_time]', $punchRules->work_end_time ?? (old('work_end_time') ?? '[0,0,0,0,0,0]'), [
                                                            'class' => 'input-sm form-control',
                                                            'id' => 'work_end_time',
                                                            ]) !!}

                                                            <label>{{trans('上下班准备时间:')}}</label>
                                                            {!! Form::text('work['.$key.'][ready_time]', !empty($punchRules->ready_time) ? $punchRules->ready_time : (old('ready_time') ?? '[0,0,0,0,0,0]') , [
                                                            'class' => 'input-sm form-control',
                                                            'id' => 'ready_time',
                                                             ]) !!}

                                                            <button style="margin-left: 2em" id="remove_rule" rel="" type="button" class="btn btn-danger btn-xs">删除</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            <div id="collapseOne" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                {{--拷贝需要填入的地方--}}
                                                    <div id="create_div_{{$key}}">
                                                        {{--需要拷贝的内容--}}
                                                        <div id="row_copy" class="row">
                                                            <div class="col-md-10">
                                                                <div class="row">
                                                                    <div class="form-inline">
                                                                        {!! Form::text('work['.$key.'][cfg]['.$key.'][rule_desc]', $punchRules->rule_desc ?? old('rule_desc'), [
                                                                           'class' => 'input-sm form-control',
                                                                           'id' => 'rule_desc',
                                                                           'placeholder' => trans('app.请输入', ['value' => trans('app.规则描述')])
                                                                           ]) !!}
                                                                        <select id="late_type" name="work[{{$key}}][cfg][{{$key}}][late_type]">
                                                                            @foreach(\App\Models\Sys\PunchRules::$lateType as $k => $v)
                                                                                <option value="{{ $k }}" @if($k === ($punchRules->late_type ?? old('late_type') ?? 0)) selected="selected" @endif>{{ $v }}</option>
                                                                            @endforeach
                                                                        </select>

                                                                        <div class="input-daterange input-group">
                                                                            {!! Form::text('work['.$key.'][cfg]['.$key.'][start_gap]', $punchRules->start_gap ?? '[0,0,0,0,0]', [
                                                                            'class' => 'input-sm form-control ',
                                                                            'id' => 'start_gap',
                                                                            ]) !!}
                                                                            <span class="input-group-addon" style="background-color:#eeeeee;">to</span>
                                                                            {!! Form::text('work['.$key.'][cfg]['.$key.'][end_gap]', $punchRules->end_gap ?? '[0,0,0,0,0]', [
                                                                            'class' => 'input-sm form-control ',
                                                                            'id' => 'end_gap',
                                                                            ]) !!}
                                                                        </div>

                                                                        {!! Form::label('group_id', '扣分类型') !!}
                                                                            <input id="ded_type1" name="work[{{$key}}][cfg][{{$key}}][ded_type][{{$key}}]" type="radio" value="1" checked = checked> 分数
                                                                            <input id="ded_type2" name="work[{{$key}}][cfg][{{$key}}][ded_type][{{$key}}]" type="radio" value="2">
                                                                            <select id="holiday_id" name="work[{{$key}}][cfg][{{$key}}][holiday_id]" >
                                                                                @foreach(\App\Models\Sys\HolidayConfig::holidayList() as $k => $v)
                                                                                    <option value="{{ $k }}" @if($k === ($punchRules->holiday_id ?? old('holiday_id') ?? 0)) selected="selected" @endif>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            {!! Form::text('work['.$key.'][cfg]['.$key.'][ded_num]', $punchRules->ded_num ?? old('ded_num'), [
                                                                                'class' => 'input-sm form-control ',
                                                                                'id' => 'ded_num',
                                                                            ]) !!}
                                                                        <button id="remove_ded" rel="" type="button" class="btn btn-danger btn-xs">删除</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{--需要拷贝的内容end--}}
                                                    </div>
                                                    {{--拷贝需要填入的地方end--}}

                                                    <div class="col-sm-offset-11">
                                                        {!! Form::button(trans('新增扣分规则'), ['class' => 'btn btn-info', 'id' => 'add_ded', 'rel' => $key]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                         </div>
                                    </div>
                                    </div>

                                    <div id="create_rule">
                                    </div>


                                    <div class="form-group">
                                        <div class="col-sm-6 col-sm-offset-10">
                                            {!! Form::button(trans('新增时间段'), ['class' => 'btn btn-primary', 'id' => 'add_create_rule']) !!}
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
        var i = 0;
        var key = {{$key}};

        $('#add_ded').click(function () {
            i++;
            var id = 'row_copy_' + i;
            var div = 'create_div_' + $(this).attr('rel');

            var clone = $('#' + div).find("#row_copy").clone(true).prop({'id': id});

            /*复制之后变量重新定义*/
            clone.find("button[id='remove_ded']").attr({'id': 'remove_ded_'+ i, 'rel': i});
            clone.find("*[id=ded_type1]").attr({'name': 'work['+ $(this).attr('rel') +'][cfg]['+i+'][ded_type]['+i+']', 'rel': i});
            clone.find("*[id=ded_type2]").attr({'name': 'work['+ $(this).attr('rel') +'][cfg]['+i+'][ded_type]['+i+']' , 'rel': i});
            clone.find("*[id=rule_desc]").attr({'name': 'work['+ key +'][cfg]['+ i +'][rule_desc]'});
            clone.find("*[id=late_type]").attr({'name': 'work['+ key +'][cfg]['+ i +'][late_type]'});
            clone.find("*[id=start_gap]").attr({'name': 'work['+ key +'][cfg]['+ i +'][start_gap]'});
            clone.find("*[id=end_gap]").attr({'name': 'work['+ key +'][cfg]['+ i +'][end_gap]'});
            clone.find("*[id=holiday_id]").attr({'name': 'work['+ key +'][cfg]['+ i +'][holiday_id]'});
            clone.find("*[id=ded_num]").attr({'name': 'work['+ key +'][cfg]['+ i +'][ded_num]'});

            clone.appendTo('#create_div_' + $(this).attr('rel'));
        });

        $('button[id^=remove_ded]').click(function () {
            $id = $(this).attr('rel');
            $("#row_copy_" + $id).remove();
        });


        $('#add_create_rule').click(function () {
            key++;
            var id = 'create_rule_' + key;
            var clone = $("#copy_rule").clone(true).attr({'id': id});

            var h3_a = '<a href="#collapseOne'+key+'" data-toggle="collapse" data-parent="#accordion'+key+'">上下班时间'+key+'</a>';
            clone.find("h3[id^=h3_a]").html(h3_a);
            clone.find("div[id^=collapseOne]").attr({'id': 'collapseOne'+ key});
            clone.find("div[id^=accordion]").attr({'id': 'accordion'+ key});
            clone.find("div[id^=create_div]").attr({'id': 'create_div_'+ key, 'rel': key});
            clone.find("button[id='remove_rule']").attr({'id': 'remove_rule_'+ key, 'rel': key});
            clone.find("button[id='add_ded']").attr({'rel': key});

            /*复制之后变量重新定义*/
            clone.find("*[id=work_start_time]").attr({'name': 'work['+ key +'][work_start_time]', 'id': 'work_start_time' + key });
            clone.find("*[id=work_end_time]").attr({'name': 'work['+ key +'][work_end_time]', 'id': 'work_end_time' + key });
            clone.find("*[id=ready_time]").attr({'name': 'work['+ key +'][ready_time]', 'id': 'ready_time' + key });
            clone.find("*[id=show_start_yes]").attr({'name': 'work['+ key +'][is_show_start]', 'id': 'show_start_yes' + key });
            clone.find("*[id=show_start_no]").attr({'name': 'work['+ key +'][is_show_start]', 'id': 'show_start_no' + key });
            clone.find("*[id=show_end_yes]").attr({'name': 'work['+ key +'][is_show_end]', 'id': 'show_end_yes' + key });
            clone.find("*[id=show_end_no]").attr({'name': 'work['+ key +'][is_show_end]', 'id': 'show_end_no' + key });
            clone.find("*[id=rule_desc]").attr({'name': 'work['+ key +'][cfg]['+ i +'][rule_desc]'});
            clone.find("*[id=late_type]").attr({'name': 'work['+ key +'][cfg]['+ i +'][late_type]'});
            clone.find("*[id=start_gap]").attr({'name': 'work['+ key +'][cfg]['+ i +'][start_gap]'});
            clone.find("*[id=end_gap]").attr({'name': 'work['+ key +'][cfg]['+ i +'][end_gap]'});
            clone.find("*[id=holiday_id]").attr({'name': 'work['+ key +'][cfg]['+ i +'][holiday_id]'});
            clone.find("*[id=ded_num]").attr({'name': 'work['+ key +'][cfg]['+ i +'][ded_num]'});
            clone.find("*[id=ded_type1]").attr({'name': 'work['+ key +'][cfg]['+ i +'][ded_type]['+ i +']'});
            clone.find("*[id=ded_type2]").attr({'name': 'work['+ key +'][cfg]['+ i +'][ded_type]['+ i +']'});
            clone.find('div[id^=row_copy_]').remove();

            clone.appendTo('#create_rule');

        });

        $('button[id^=remove_rule]').click(function () {
            $id = $(this).attr('rel');
            $("#create_rule_" + $id).remove();
        })

    });
</script>
@endsection

