@extends('attendance.side-nav')

@section('title', $title)

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">

                    {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}

                    <div class="form-group @if (!empty($errors->first('holiday_id'))) has-error @endif">
                        {!! Form::label('holiday_id', trans('att.请假类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <select onchange="showMemo()" class="js-select2-single form-control" id="holiday_id" name="holiday_id" >
                                <option value="">请选择</option>
                                @foreach($holidayList as $k => $v)
                                    <option value="{{ $k }}" @if($k === (int)old('holiday_id')) selected="selected" @endif>{{ $v }}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('holiday_id') }}</span>
                        </div>
                        <div class="col-sm-2">
                            <span id="show_memo" style="display: none"  style="color: red" class="help-block m-b-none">
                                <p style="color: red" id="show_p"></p>
                                <pre style="width: 30em; height: 10em"  id="show_pre"></pre>
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('start_time')) || !empty($errors->first('end_id'))) has-error @endif">
                        {!! Form::label('start_time', trans('att.请假开始时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('start_time', $time , [
                                'class' => 'form-control date',
                                'id' => 'start_time',
                                'required' => true,
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('start_time') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <select onchange="startChange()" class="js-select2-single form-control" id="start_id" name="start_id" > </select>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('end_time')) || !empty($errors->first('end_id'))) has-error @endif">
                        {!! Form::label('end_time', trans('att.请假结束时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('end_time', $time , [
                                'class' => 'form-control date',
                                'id' => 'end_time',
                                'required' => true,
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('end_time') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <select onchange="endChange()" class="js-select2-single form-control" id="end_id" name="end_id" ></select>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <img height="100px" width="100px" src="{{ !empty($leave->annex) ?  asset($leave->annex) : asset('img/blank.png') }}"
                                 id="show_associate_image">

                            <input name="annex" type="file" accept="image/*" id="select-associate-file"/>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('annex') }}</span>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('reason'))) has-error @endif">
                        {!! Form::label('reason', trans('att.请假理由'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::textarea('reason', $leave->reason ?? old('reason'), [
                            'required' => true,
                            ]) !!}
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('reason') }}</span>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('copy_user'))) has-error @endif">
                        {!! Form::label('copy_user', trans('att.抄送'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <select multiple="multiple" class="js-select2-multiple form-control"
                                    name="copy_user[]">
                                @foreach($allUsers as $key => $val)
                                    <option value="{{ $val['user_id'] }}">{{ $val['alias'].'('.$val['username'].')' }}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('copy_user') }}</span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.审批流程'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-9">
                            <div id="show_step" class="form-inline">

                            </div>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('annex') }}</span>
                    </div>


                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            {!! Form::submit(trans('app.提交'), ['class' => 'btn btn-primary']) !!}
                            <a href="{{route('leave.info')}}"
                               class="btn btn-info">{{ trans('att.返回列表') }}</a>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

@endsection
@include('widget.icheck')
@include('widget.select2')
@include('widget.datepicker')
@section('scripts-last')
    <script>
        $(function() {
            $.fn.select2.defaults.set("theme", "bootstrap");
            function readURL(input, $class_id) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $($class_id).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#select-thumb-file").change(function(){
                readURL(this, '#show_thumb_image');
            });

            $("#select-associate-file").change(function(){
                readURL(this, '#show_associate_image');
            });

            $("#select-mobile-header-file").change(function(){
                readURL(this, '#show_mobile_header_image');
            });

            $("#start_time").change(function(){
                inquireStartInfo();
            });
            $("#end_time").change(function(){
                inquireEndInfo();
            });

            showMemo();
            inquireStartInfo();
            inquireEndInfo();
            startChange();
            endChange();

        });

        function startChange() {
            inquire();
        }

        function endChange() {
            inquire();
        }

        /**
         * 显示剩余假期和描述
         */
        function showMemo() {
            var val = $('#holiday_id').children('option:selected').val();
            if(val != "") {
                $('#show_pre').html('');
                $.get('{{ route('leave.showMemo')}}', {id: val}, function ($data) {
                    if ($data.status == 1) {
                        /*显示描述*/
                        if($data.show_memo) {
                            $('#show_pre').html($data.memo);
                            $('#show_memo').show();
                        }
                        /*显示假期剩余情况*/
                        if($data.show_day) {
                            $('#show_p').show();
                            $('#show_p').html($data.msg);
                        } else {
                            $('#show_p').hide();
                        }
                        var html = $data.step;

                        $('#show_step').html(html).find('select').select2();
                        //$('#show_step').html($data.step)
                        inquire();

                    } else {
                        $('#show_memo').hide();
                        $('#show_p').hide();
                        $('#show_pre').html('');
                    }
                })
            } else {
                $('#show_pre').html('');
                $('#show_memo').hide();
            }
        }

        function inquireStartInfo() {
            var startTime = $('#start_time').val();
            getPunchRules(startTime, 1);
            inquire();

        }

        function inquireEndInfo() {
            var endTime = $('#end_time').val();
            getPunchRules(endTime, 2);
            inquire();

        }

        function inquire() {
            var holidayId = $('#holiday_id').val();
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            var startId = $('#start_id').val();
            var endId = $('#end_id').val();
            if (holidayId != '' && startTime != '' && endTime != '' && startId != '' && endId != '' )
            $.get('{{ route('leave.inquire')}}', {holidayId: holidayId,startTime: startTime, endTime: endTime, startId:startId, endId:endId}, function ($data) {
                if ($data.status == 1) {
                    $('#show_step').html($data.step).find('select').select2();
                } else {
                    $('#show_step').html('');
                }
            })
        }

        /**
         * 查询日期绑定的时间点
         * @param time
         * @param type
         */
        function getPunchRules(time, type) {
            $.get('{{ route('leave.getPunchRules')}}', {time: time}, function ($data) {

                switch (type){
                    case 1:
                        if ($data.status == 1) {
                            $("#start_id").select2("val", "");
                            $("#start_id").empty();
                            $("#start_id").select2({
                                placeholder: "-请选择时间点-", //默认所有
                                allowClear: true, //清楚选择项
                                multiple: false,// 多选
                                data: $data.start_time //绑定数据
                            });
                            $("#start_id").val($data.start_time[0]).select2();

                        } else {
                            $("#start_id").select2("val", "");
                            $("#start_id").empty();
                        }
                        break;
                    case 2:
                        if ($data.status == 1) {
                            $("#end_id").select2("val", "");
                            $("#end_id").empty();
                            $("#end_id").select2({
                                placeholder: "-请选择时间点-", //默认所有
                                allowClear: true, //清楚选择项
                                multiple: false,// 多选
                                data: $data.end_time //绑定数据
                            });

                            $("#end_id").val($data.end_time[0]).select2();
                        } else {
                            $("#end_id").select2("val", "");
                            $("#end_id").empty();

                        }
                }

            })
        }

    </script>
@endsection