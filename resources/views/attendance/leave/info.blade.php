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

                    <div class="form-group">
                        {!! Form::label('user_id', trans('att.申请人'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{\App\User::getUserAliasToId($leave->user_id)->alias ?? ''}}
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('holiday_id', trans('att.所属部门'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{$dept[\App\User::getUserAliasToId($leave->user_id)->dept_id] ?? ''}}
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('holiday_id', trans('att.请假类型'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{$holidayList[$leave->holiday_id] ?? ''}}
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('holiday_id', trans('att.请假时间'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{date('Y-m-d', strtotime($leave->start_time)) .' '. \App\Models\Attendance\Leave::$startId[$leave->start_id] ?? '' }}
                                ~
                                {{date('Y-m-d', strtotime($leave->end_time)) .' '. \App\Models\Attendance\Leave::$endId[$leave->end_id] ?? '' }}
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('day', trans('att.请假天数'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ App\Components\Helper\DataHelper::diffTime(date('Y-m-d', strtotime($leave->start_time)) . ' ' . \App\Models\Attendance\Leave::$startId[$leave->start_id], date('Y-m-d', strtotime($leave->end_time)) . ' ' . \App\Models\Attendance\Leave::$endId[$leave->end_id]) .'天'}}
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('reason', trans('att.申请理由'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{$leave->reason}}
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('annex', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <img height="100px" width="100px" src="{{ !empty($leave->annex) ?  asset($leave->annex) : asset('img/blank.png') }}"
                                 id="show_associate_image">
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        {!! Form::label('reason', trans('att.当前审核流程状态'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ \App\Http\Components\Helpers\AttendanceHelper::showApprovalStep($leave->step_id) }}
                            </span>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-5">
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
        });
    </script>
@endsection