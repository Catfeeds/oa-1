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
                    {{--分割线--}}
                    <div class="col-sm-6 b-r">
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
                            {!! Form::label('holiday_id', trans('att.申请类型'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ \App\Models\Sys\HolidayConfig::$applyType[\App\Models\Sys\HolidayConfig::getHolidayApplyList()[$leave->holiday_id]] ?? ''}}
                            </span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            {!! Form::label('holiday_id', trans('att.明细类型'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ \App\Models\Sys\HolidayConfig::getHolidayList()[$leave->holiday_id] ?? ''}}
                            </span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            {!! Form::label('holiday_id', trans('att.申请时间'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                @if($applyTypeId == \App\Models\Sys\HolidayConfig::LEAVEID)
                                    {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($leave->holiday_id, $leave->start_time, $leave->start_id, $leave->number_day)['time']}}
                                    ~
                                    {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($leave->holiday_id, $leave->end_time, $leave->end_id, $leave->number_day)['time']}}
                                @elseif($applyTypeId == \App\Models\Sys\HolidayConfig::CHANGE)
                                    {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($leave->holiday_id, $leave->start_time, $leave->start_id, $leave->number_day)['time']}}
                                @elseif($applyTypeId == \App\Models\Sys\HolidayConfig::RECHECK)
                                    @if(!empty($leave->start_time))
                                        {{ \App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($leave->holiday_id, $leave->start_time,$leave->start_id, $leave->number_day)['time']}}
                                    @elseif(!empty($leave->end_time))
                                        {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($leave->holiday_id, $leave->end_time, $leave->end_id, $leave->number_day)['time']}}}}
                                    @endif
                                @endif
                            </span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            {!! Form::label('day', trans('att.申请时长'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ empty($leave->number_day) ? trans('att.补打卡') : \App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($leave->holiday_id, $leave->start_time, $leave->start_id, $leave->number_day)['number_day']}}
                                @if(!empty($leave->exceed_day))
                                    <h4 style="color: red">{!! '自动转换类型为:' . \App\Models\Sys\HolidayConfig::holidayList()[$leave->exceed_holiday_id] . $leave->exceed_day . '天' ?? '数据异常'  !!}</h4>
                                @endif
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
                    </div>

                    {{--分割线--}}
                    <div class="col-sm-6">

                        <div class="form-group">
                            {!! Form::label('reason', trans('att.审核流程'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-8">
                            <span class="help-block m-b-none">
                                {{ \App\Http\Components\Helpers\AttendanceHelper::showApprovalStep($leave->step_user) ?? '未匹配到流程' }}
                            </span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            {!! Form::label('reason', trans('att.审核状态'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {!!   !in_array($leave->status, \App\Models\Attendance\Leave::$retractList) ?  \App\Models\Attendance\Leave::leaveColorStatus($leave->status)  : '待['. \App\User::getUsernameAliasList()[$reviewUserId]. ']审核' !!}
                            </span>
                            </div>
                        </div>

                        {{--调休名单显示--}}
                        @if($applyTypeId === \App\Models\Sys\HolidayConfig::CHANGE && !empty($leave->user_list) && !empty($userIds))
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                {!! Form::label('reason', trans('att.调休名单'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-6">
                                    <select disabled="disabled" multiple="multiple" class="js-select2-multiple form-control">
                                        @foreach($users as $key => $val)
                                            <option value="{{ $key }}"
                                                    @if (in_array($key, $userIds)) selected @endif>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="hr-line-dashed"></div>

                        <div style="height: 20em;" class="form-group">
                            {!! Form::label('assign_uid', trans('att.处理详情'), ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-10">
                                @foreach($logs as $lk => $lv)
                                    <span class="help-block m-b-none">
                                    <a class="btn btn-xs btn-primary">{{ $lv->created_at }}</a>
                                    <a class="btn btn-xs btn-rounded">{{ \App\User::getAliasList()[$lv->opt_uid]}}</a>
                                    <a class="btn btn-xs btn-default btn-rounded btn-outline">{{ $lv->opt_name }} </a>
                                        @if(!empty($lv->memo))
                                            <span style="color: #039"> {!! $lv->memo !!}</span>
                                        @endif
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-5">
                        @if(in_array($leave->status, \App\Models\Attendance\Leave::$retractList) && $leave->review_user_id == Auth::user()->user_id && Entrust::can(['leave.review']))
                            <a id="pass_status" data-id='{{\App\Models\Attendance\Leave::PASS_REVIEW}}' class="btn btn-success">{{ trans('att.审核通过') }}</a>
                            <a id="refuse_status" data-id='{{\App\Models\Attendance\Leave::REFUSE_REVIEW}}' class="btn btn-danger">{{ trans('att.拒绝通过') }}</a>
                        @endif
                        @if(Entrust::can(['leave.cancel']) && in_array($leave->status, \App\Models\Attendance\Leave::$cancelList))
                            <a id="cancel_status" data-id='{{\App\Models\Attendance\Leave::CANCEL_REVIEW}}' class="btn btn-danger">{{ trans('att.取消申请') }}</a>
                        @endif
                            <a href="{{route('leave.review.info')}}" class="btn btn-info">{{ trans('att.返回列表') }}</a>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>

@endsection
@include('widget.bootbox')
@include('widget.icheck')
@include('widget.select2')
@include('widget.datepicker')
@include('widget.review-batch-operation')
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


            $('#pass_status').click(function () {
                var status = $(this).data('id');
                edit_status(status, '是否审核通过!');
            });

            $('#refuse_status').click(function () {
                var status = $(this).data('id');
                edit_status(status, '是否拒绝通过!');
            });

            $('#cancel_status').click(function () {
                var status = $(this).data('id');
                edit_status(status, '是否取消申请!');
            });
        });

        function edit_status(status, $msg){
            bootbox.confirm($msg, function (result) {
                if (result) {
                    $.get('{{ route('leave.review.optStatus',['id' => $leave->leave_id])}}', {status: status}, function ($data) {
                        if ($data.status == 1) {
                            bootbox.alert($data.msg);
                            location.reload();
                        } else {
                            bootbox.alert($data.msg);
                        }
                    })
                }
            });
        }

    </script>
@endsection