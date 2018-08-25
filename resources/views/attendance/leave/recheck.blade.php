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

                    {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'url' => 'attendance/leave/create' ]) !!}

                    {!! Form::hidden('apply_type_id', \App\Models\Sys\HolidayConfig::RECHECK) !!}
                    {!! Form::hidden('holiday_id', \App\Models\Sys\HolidayConfig::where('apply_type_id', \App\Models\Sys\HolidayConfig::RECHECK)->first()->holiday_id) !!}

                    <div class="form-group @if (!empty($errors->first('holiday_id'))) has-error @endif">
                        {!! Form::label('holiday_id', trans('att.补打卡类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="checkbox col-sm-2">
                            <label> 
                                <input type="checkbox" id="onwork_recheck" checked>上班补打卡
                            </label>
                        </div>

                        <div class="checkbox col-sm-2">
                            <label> 
                                <input type="checkbox" id="offwork_recheck" checked>下班补打卡
                            </label>
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('holiday_id') }}</span>
                    </div>


                    <div id="onwork_div">
                        <div class="hr-line-dashed"></div>

                        <div class="form-group @if (!empty($errors->first('start_time'))) has-error @endif" >
                            {!! Form::label('start_time', trans('att.上班补打卡时间'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    {!! Form::text('start_time', !empty($leave->start_time) ? $leave->start_time : (old('start_time') ?? '') , [
                                    'class' => 'form-control date_time',
                                    'required' => true
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('start_time') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="offwork_div">
                        <div class="hr-line-dashed"></div>

                        <div class="form-group @if (!empty($errors->first('end_time'))) has-error @endif" >
                            {!! Form::label('end_time', trans('att.下班补打卡时间'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    {!! Form::text('end_time', !empty($leave->end_time) ? $leave->end_time : (old('end_time') ?? '') , [
                                    'class' => 'form-control date_time',
                                    'required' => true
                                    ]) !!}
                                    <span class="help-block m-b-none">{{ $errors->first('end_time') }}</span>
                                </div>
                            </div>
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

                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group @if (!empty($errors->first('reason'))) has-error @endif">
                        {!! Form::label('reason', trans('att.请假理由'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::textarea('reason', $leave->reason ?? old('reason'), [
                            'required' => true,
                            ]) !!}
                        </div>
                        <span class="help-block m-b-none">{{ $errors->first('reason') }}</span>
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
@section('scripts-last')
    <script>
        $(function() {
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

            //未选择补卡则隐藏填写时间
            $('input:checkbox').click(function(){
                if ($(this).attr('id') == "onwork_recheck"){
                    if ($(this).is(":checked")) {
                        $('#start_time').val('');
                        $('#onwork_div').show();
                    } else {
                        $('#start_time').val('{{ \App\Models\Attendance\Leave::HASNOTIME }}');
                        $('#onwork_div').hide();
                    }
                }else {
                    if ($(this).is(":checked")) {
                        $('#end_time').val('');
                        $('#offwork_div').show();
                    } else {
                        $('#end_time').val('{{ \App\Models\Attendance\Leave::HASNOTIME }}');
                        $('#offwork_div').hide();
                    }
                }
            });
            //若用户未选择补打卡选项,进行提示
            $('form').submit(function () {
                if ($('#end_time').val() == '{{ \App\Models\Attendance\Leave::HASNOTIME }}' &&
                    $('#start_time').val() == '{{ \App\Models\Attendance\Leave::HASNOTIME }}'){
                    alert('请选择补打卡');
                    return false;
                }
            });
        });
    </script>
@endsection