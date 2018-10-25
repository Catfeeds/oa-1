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
                                                    <a href="#collapseOne" data-toggle="collapse0" data-parent="#accordion">上下班时间</a>
                                                </h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-daterange input-group">
                                                            {!! Form::text('work['.$key.'][work_start_time]', $punchRules->work_start_time ?? old('work_start_time'), [
                                                            'class' => 'input-sm form-control',
                                                            'id' => 'work_start_time',
                                                            ]) !!}
                                                            <span class="input-group-addon" style="background-color:#eeeeee;">to</span>
                                                            {!! Form::text('work['.$key.'][work_end_time]', $punchRules->work_end_time ?? old('work_end_time'), [
                                                            'class' => 'input-sm form-control',
                                                            'id' => 'work_end_time',
                                                            ]) !!}
                                                            <span class="input-group-addon" >{!!trans('app.上下班准备时间')!!}</span>
                                                            {!! Form::text('work['.$key.'][ready_time]', !empty($punchRules->ready_time) ? $punchRules->ready_time : (old('ready_time') ?? '') , [
                                                            'class' => 'input-sm form-control',
                                                            'id' => 'ready_time',
                                                             ]) !!}
                                                            <span class="help-block m-b-none">{{ $errors->first('work_start_time') }}</span>
                                                            <span class="help-block m-b-none">{{ $errors->first('work_end_time') }}</span>
                                                        </div>
                                                    </div>
                                                    <button id="remove_rule" rel="" type="button" class="btn btn-danger btn-xs">删除</button>
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
                                                                        {!! Form::text('work['.$key.'][rule_desc][]', $punchRules->rule_desc ?? old('rule_desc'), [
                                                                           'class' => 'input-sm form-control',
                                                                           'id' => 'rule_desc',
                                                                           'placeholder' => trans('app.请输入', ['value' => trans('app.规则描述')])
                                                                           ]) !!}
                                                                        <select id="work_type" name="work[{{$key}}][work_type][]">
                                                                            @foreach(\App\Models\Sys\PunchRules::$workType as $k => $v)
                                                                                <option value="{{ $k }}" @if($k === ($punchRules->work_type ?? old('work_type') ?? 0)) selected="selected" @endif>{{ $v }}</option>
                                                                            @endforeach
                                                                        </select>

                                                                        <div class="input-daterange input-group">
                                                                            {!! Form::text('work['.$key.'][start_gap][]', $punchRules->start_gap ?? '[0,0,0,0,0,0]', [
                                                                            'class' => 'input-sm form-control ',
                                                                            'id' => 'start_gap',
                                                                            ]) !!}
                                                                            <span class="input-group-addon" style="background-color:#eeeeee;">to</span>
                                                                            {!! Form::text('work['.$key.'][end_gap][]', $punchRules->end_gap ?? '[0,0,0,0,0,0]', [
                                                                            'class' => 'input-sm form-control ',
                                                                            'id' => 'end_gap',
                                                                            ]) !!}
                                                                        </div>

                                                                        {!! Form::label('group_id', '扣分类型') !!}
                                                                            <input id="ded_type1" name="work[{{$key}}][ded_type][{{$key}}]" type="radio" value="1" checked = checked> 分数
                                                                            <input id="ded_type2" name="work[{{$key}}][ded_type][{{$key}}]" type="radio" value="2">
                                                                            <select id="holiday_id" name="work[{{$key}}][holiday_id][]" >
                                                                                @foreach(\App\Models\Sys\HolidayConfig::holidayList() as $k => $v)
                                                                                    <option value="{{ $k }}" @if($k === ($punchRules->holiday_id ?? old('holiday_id') ?? 0)) selected="selected" @endif>{{ $v }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            {!! Form::text('work['.$key.'][ded_num][]', $punchRules->ded_num ?? old('ded_num'), [
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
            var $id = 'row_copy_' + i;
            var div = 'create_div_' + $(this).attr('rel');

            var $html = $('#' + div).find("#row_copy").clone().attr({'id': $id});
            $html.appendTo('#create_div_' + $(this).attr('rel'));

            $('#' + $id).find("button[id='remove_ded']").attr({'id': 'remove_ded_'+ i, 'rel': i});
            $('#' + $id).find("*[id=ded_type1]").attr({'name': 'work['+ $(this).attr('rel') +'][ded_type]['+i+']', 'rel': i, 'checked': 1});
            $('#' + $id).find("*[id=ded_type2]").attr({'name': 'work['+ $(this).attr('rel') +'][ded_type]['+i+']' , 'rel': i});

        });

        $('button[id^=remove_ded]').click(function () {
            $id = $(this).attr('rel');
            $("#row_copy_" + $id).remove();
        });


        $('#add_create_rule').click(function () {
            key++;

            var $id = 'create_rule_' + key;
            var $html = $("#copy_rule").clone(true).attr({'id': $id});
            $html.appendTo('#create_rule');

            var h3_a = '<a href="#collapseOne'+key+'" data-toggle="collapse" data-parent="#accordion'+key+'">上下班时间'+key+'</a>';


            $('#' + $id).find("h3[id^=h3_a]").html(h3_a);
            $('#' + $id).find("div[id^=collapseOne]").attr({'id': 'collapseOne'+ key});
            $('#' + $id).find("div[id^=accordion]").attr({'id': 'accordion'+ key});


            $('#' + $id).find("div[id^=create_div]").attr({'id': 'create_div_'+ key, 'rel': key});
            $('#' + $id).find("button[id='remove_rule']").attr({'id': 'remove_rule_'+ key, 'rel': key});
            $('#' + $id).find("button[id='add_ded']").attr({'rel': key});


            /*复制之后变量重新定义*/
            $('#' + $id).find("*[id=work_start_time]").attr({'name': 'work['+ key +'][work_start_time]', 'id': 'work_start_time' + key });
            $('#' + $id).find("*[id=work_end_time]").attr({'name': 'work['+ key +'][work_end_time]', 'id': 'work_end_time' + key });
            $('#' + $id).find("*[id=ready_time]").attr({'name': 'work['+ key +'][ready_time]', 'id': 'ready_time' + key });
            $('#' + $id).find("*[id=rule_desc]").attr({'name': 'work['+ key +'][rule_desc][]'});
            $('#' + $id).find("*[id=work_type]").attr({'name': 'work['+ key +'][work_type][]'});
            $('#' + $id).find("*[id=start_gap]").attr({'name': 'work['+ key +'][start_gap][]'});
            $('#' + $id).find("*[id=end_gap]").attr({'name': 'work['+ key +'][end_gap][]'});
            $('#' + $id).find("*[id=holiday_id]").attr({'name': 'work['+ key +'][holiday_id][]'});
            $('#' + $id).find("*[id=ded_num]").attr({'name': 'work['+ key +'][ded_num][]'});
            $('#' + $id).find("*[id=ded_type1]").attr({'name': 'work['+ key +'][ded_type][]'});
            $('#' + $id).find("*[id=ded_type2]").attr({'name': 'work['+ key +'][ded_type][]'});


            $('#' + $id).find('div[id^=row_copy_]').remove();


        });

        $('button[id^=remove_rule]').click(function () {
            $id = $(this).attr('rel');
            $("#create_rule_" + $id).remove();
        })

    });
</script>
@endsection