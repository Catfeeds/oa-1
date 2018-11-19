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
                    {{ Form::hidden('leave_id', $leave->leave_id ?? '') }}
                    <div class="form-group @if (!empty($errors->first('holiday_id'))) has-error @endif">
                        {!! Form::label('holiday_id', trans('att.调休类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <select onchange="showMemo()" id ='holiday_id' class="js-select2-single form-control" name="holiday_id" >
                                <option value="">请选择</option>
                                @foreach($holidayList as $k => $v)
                                    <option value="{{ $k }}" @if($k === (int)(!empty($leave->holiday_id) ? $leave->holiday_id : old('holiday_id'))) selected="selected" @endif>{{ $v }}</option>
                                @endforeach
                            </select>
                            <span class="help-block m-b-none">{{ $errors->first('holiday_id') }}</span>
                        </div>
                        <div class="col-sm-2">
                            <span id="show_memo" style="display: none"  class="help-block m-b-none">
                                <p style="color: red" id="show_p"></p>
                                <pre style="width: 30em; height: 10em"  id="show_pre"></pre>
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('start_time'))) has-error @endif">
                        {!! Form::label('start_time', trans('att.调休开始时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('start_time', !empty($leave->start_time) ? date('Y-m-d', strtotime($leave->start_time)) : $time  , [
                                'class' => 'form-control date',
                                'required' => true,
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('start_time') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <select  class="js-select2-single form-control" name="start_id" id="start_id" >
                                <option value="">-请选择时间点-</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: none" id="show_time" class="form-group @if (!empty($errors->first('end_time'))) has-error @endif">
                        {!! Form::label('end_time', trans('att.调休结束时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('end_time', $leave->end_time ?? old('start_time') ?? '' , [
                                'class' => 'form-control date_time',
                                ]) !!}
                                <span class="help-block m-b-none">{{ $errors->first('end_time') }}</span>
                            </div>
                        </div>

                        <div  class="col-sm-2">
                            <span id="show_msg_p" style="display: none" class="help-block m-b-none">
                                <p style=";color: red" id="show_msg"></p>
                            </span>
                        </div>
                    </div>

                    {{--只有审批权限才可以显示批量选择人员--}}
                    @if($isBatch &&Entrust::can(['leave.batchOvertime']))
                        <div class="hr-line-dashed"></div>

                        <div class="form-group @if (!empty($errors->first('dept_users'))) has-error @endif">
                            {!! Form::label('dept_users', trans('att.批量申请人员'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-8">
                                <select name="dept_users[]" class="form-control dual_select" multiple>
                                    @foreach($deptUsers as $dk => $dv)
                                        <option value="{{$dk}}">{{$dv}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    @endif

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('annex'))) has-error @endif">
                        {!! Form::label('annex', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <img height="100px" width="100px" src="{{ !empty($leave->annex) ?  asset($leave->annex) : asset('img/blank.png') }}"
                                 id="show_associate_image">
                            <input name="annex" type="file" accept="image/*" id="select-associate-file"/>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('reason'))) has-error @endif">
                        {!! Form::label('reason', trans('att.申请理由'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-8">
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
                                    <option value="{{ $val['user_id'] }}
                                    @if (!empty($copyUserIds) && in_array($key, $copyUserIds)) selected @endif">{{ $val['alias'].'('.$val['username'].')' }}</option>
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
                            <a href="javascript:history.go(-1);"
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
@include('widget.dual-listbox')
@section('scripts-last')
    <script>
        $(function() {

            $('.dual_select').bootstrapDualListbox({
                selectorMinimalHeight: 300
            });


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
                inquire();
            });

            showMemo();
            inquire();
        });

        function readURL(input, $class_id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $($class_id).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function inquire() {
            var holidayId = $('#holiday_id').val();
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            var startId = $('#start_id').val();

            if (holidayId != '' && startTime != '' && startId != '' )
                $.get('{{ route('leave.inquire')}}', {holidayId: holidayId,startTime: startTime, endTime: endTime, startId:startId}, function ($data) {
                    if ($data.status == 1) {
                        $('#show_step').html($data.step).find('select').select2();
                    } else {
                        $('#show_step').html('');
                    }
                })
        }

        /**
         * 显示剩余假期和描述
         */
        function showMemo() {
            var val = $('#holiday_id').children('option:selected').val();
            $('#start_time').attr('rel', '');
            $('#end_time').val('');
            if(val != "") {
                $('#show_pre').html('');
                $.get('{{ route('leave.showMemo')}}', {id: val}, function ($data) {
                    if ($data.status == 1) {
                        if($data.show_memo){
                            $('#show_pre').html($data.memo);
                            $('#show_memo').show();
                        }
                        if($data.show_day) {
                            $('#show_p').show();
                            $('#show_p').html($data.msg);


                            $("#start_id").select2("val", "");
                            $("#start_id").empty();
                            $("#start_id").select2({
                                placeholder: "-请选择时间点-", //默认所有
                                allowClear: true, //清楚选择项
                                multiple: false,// 多选
                                data: $data.point_list //绑定数据
                            });
                            inquire();

                        } else {
                            $("#start_id").select2("val", "");
                            $("#start_id").empty();
                            $('#show_p').hide();
                        }

                        if($data.show_time) {
                            $('#show_time').show();
                            $('#start_time').attr('rel', 1);
                            $('#start_time').val($data.day);

                            $("#start_id").select2({
                                placeholder: "-请选择时间点-", //默认所有
                                allowClear: true, //清楚选择项
                                multiple: false,// 多选
                                data: $data.start_id //绑定数据
                            });

                            $('#end_time').val($data.end_day);
                            inquire();
                            $('#show_msg').html($data.msg);
                            $('#show_msg_p').show();

                        } else {
                            $('#show_time').hide();
                        }

                    } else {
                        $("#start_id").select2("val", "");
                        $("#start_id").empty();
                        $('#show_memo').hide();
                        $('#show_p').hide();
                        $('#show_pre').html('');
                    }
                })
            } else {
                $("#start_id").select2("val", "");
                $("#start_id").empty();
                $('#show_pre').html('');
                $('#show_memo').hide();
                $('#show_step').html('');
            }
        }

        function inquireStartInfo() {
            var startTime = $('#start_time').val();
            var rel =  $('#start_time').attr('rel');
            if (rel != 1) return false;
            getPunchRules(startTime);
        }

        /**
         * 查询日期绑定的时间点
         * @param time
         * @param type
         */
        function getPunchRules(time) {
            $.get('{{ route('leave.getPunchRules')}}', {time: time}, function ($data) {
                if ($data.status == 1) {
                    $("#start_id").select2("val", "");
                    $("#start_id").empty();
                    $("#start_id").select2({
                        placeholder: "-请选择时间点-", //默认所有
                        allowClear: true, //清楚选择项
                        multiple: false,// 多选
                        data: $data.last_time //绑定数据
                    });
                } else {
                    $("#start_id").select2("val", "");
                    $("#start_id").empty();
                }
            })
        }

    </script>
@endsection