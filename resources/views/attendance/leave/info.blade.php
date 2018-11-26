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
                                    @if(!empty($leave->start_time) && !empty($leave->end_time))
                                       {{ $leave->start_time }} ~ {{ $leave->end_time }}
                                    @elseif(!empty($leave->start_time))
                                            {{ $leave->start_time }}
                                    @elseif(!empty($leave->end_time))
                                        {{ $leave->end_time }}
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

                    <!--弹窗图片窗口-->

                    <!--弹窗图片窗口end-->

                    <div class="form-group">
                        {!! Form::label('annex', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            <div id="outerdiv" style="position:fixed;top:0;left:0;background:rgba(0,0,0,0.7);z-index:2;width:100%;height:100%;display:none;">
                                <div id="innerdiv" style="position:absolute;">
                                    <img id="bigimg" style="border:5px solid #fff;" src="" />
                                </div>
                            </div>
                            <img height="100px" width="100px" src="{{ !empty($leave->annex) ?  asset($leave->annex) : asset('img/blank.png') }}"
                                 id="show_image">
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
                                {{ !in_array($leave->status, \App\Models\Attendance\Leave::$retractList) ?  \App\Models\Attendance\Leave::$status[$leave->status] : '待['. \App\User::getUsernameAliasList()[$reviewUserId]. ']审核' }}
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

                        @if(in_array($leave->status, \App\Models\Attendance\Leave::$retractList) && $cypherType !== \App\Models\Sys\HolidayConfig::CYPHER_OVERTIME && $leave->user_id == Auth::user()->user_id && Entrust::can(['leave.retract']) )
                            <a id="retract_status" data-id='{{\App\Models\Attendance\Leave::RETRACT_REVIEW}}' class="btn btn-danger">{{ trans('att.撤回申请') }}</a>
                        @endif

                        @if(in_array($leave->status, \App\Models\Attendance\Leave::$restartList) && $cypherType !== \App\Models\Sys\HolidayConfig::CYPHER_OVERTIME && $leave->user_id == Auth::user()->user_id && Entrust::can(['leave.restart']) )
                            <a id="restart_status" data-id='{{\App\Models\Attendance\Leave::RESTART_REVIEW}}' class="btn btn-success">{{ trans('att.重启申请') }}</a>
                        @endif

                        {{--针对批量申请单操作--}}
                        @if(in_array($leave->status, \App\Models\Attendance\Leave::$retractList) && $cypherType === \App\Models\Sys\HolidayConfig::CYPHER_OVERTIME && $leave->user_id == Auth::user()->user_id && Entrust::can(['leave.retract']) )
                            <a id="batch_retract_status" data-id='{{\App\Models\Attendance\Leave::BATCH_RETRACT_REVIEW}}' class="btn btn-danger">{{ trans('att.批量撤回申请') }}</a>
                        @endif
                        {{--针对批量申请单个人操作--}}
                        @if(in_array($leave->status, \App\Models\Attendance\Leave::$retractList) && $cypherType === \App\Models\Sys\HolidayConfig::CYPHER_OVERTIME && (!empty($userIds) &&in_array(Auth::user()->user_id, $userIds)) && Entrust::can(['leave.retract']) )
                            <a id="retract_status" data-id='{{\App\Models\Attendance\Leave::RETRACT_REVIEW}}' class="btn btn-danger">{{ trans('att.个人撤回申请') }}</a>
                        @endif

                        @if(in_array($leave->status, \App\Models\Attendance\Leave::$restartList) && $cypherType === \App\Models\Sys\HolidayConfig::CYPHER_OVERTIME && $leave->user_id == Auth::user()->user_id && Entrust::can(['leave.restart']) )
                            <a id="batch_restart_status" data-id='{{\App\Models\Attendance\Leave::BATCH_RESTART_REVIEW}}' class="btn btn-success">{{ trans('att.批量重启申请') }}</a>
                        @endif

                        <a href="{{route('leave.info')}}" class="btn btn-info">{{ trans('att.返回列表') }}</a>
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
@include('widget.show-img')

@push('scripts')
    <script>
        $(function() {

            $('#show_image').click(function () {
                showImg("#outerdiv", "#innerdiv", "#bigimg", $(this));
            });

            $('#retract_status').click(function () {
                var status = $(this).data('id');
                edit_status(status, '确认撤回申请单?')
            });
            $('#batch_retract_status').click(function () {
                var status = $(this).data('id');
                edit_status(status, '确认批量撤回申请单?')
            });

            $('#restart_status').click(function () {
                var status = $(this).data('id');
                edit_status(status, '确认重启申请单?')
            });
            $('#batch_restart_status').click(function () {
                var status = $(this).data('id');
                edit_status(status, '确认批量重启申请单?')
            });
        });



        function edit_status(status, $msg){
            bootbox.confirm($msg, function (result) {
                if (result) {
                    $.get('{{ route('leave.review.optStatus',['id' => $leave->leave_id])}}', {status: status}, function ($data) {
                        if ($data.status == 1) {
                            //重启状态地址跳转
                            if($data.url != '') {
                                window.location = $data.url;
                            } else {
                                bootbox.alert($data.msg);
                                location.reload();
                            }

                        } else {
                            bootbox.alert($data.msg);
                        }
                    })
                }
            });
        }


    </script>
@endpush