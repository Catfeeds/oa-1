@extends('attendance.side-nav')

@push('css')
<style>
    #operate > div > a.btn {
        width: 85px;
    }
</style>
@endpush

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
                    <div class="row">
                        {{--分割线--}}
                        <div class="col-sm-6 b-r">
                            <div class="form-group">
                                {!! Form::label('user_id', trans('att.申请人'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ \App\User::getUserAliasToId($apply['user_id'])->alias ?? '' }}
                            </span>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                {!! Form::label('holiday_id', trans('att.所属部门'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ $dept[\App\User::getUserAliasToId($apply['user_id'])->dept_id] ?? '' }}
                            </span>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                {!! Form::label('holiday_id', trans('att.申请时间'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ $apply['created_at'] }}
                            </span>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                {!! Form::label('holiday_id', trans('att.预期归还时间'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ $apply['expect_return_time'] }}
                            </span>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                {!! Form::label('reason', trans('att.借用事由'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ $apply['reason'] }}
                            </span>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                {!! Form::label('reason', trans('att.借用项目'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-6">
                                    <div class="help-block m-b-none">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <td>id</td>
                                                <td>类型</td>
                                                <td>具体文件名称</td>
                                                <td>数量</td>
                                                <td>所属公司</td>
                                                <td>内容</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($apply['inventory'] as $inv)
                                                <tr>
                                                    <td>{{ $inv['id'] }}</td>
                                                    <td>{{ $inv['type'] }}</td>
                                                    <td>{{ $inv['name'] }}</td>
                                                    <td>1</td>
                                                    <td>{{ $inv['company'] }}</td>
                                                    <td>{{ $inv['content'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                {!! Form::label('annex', trans('att.附件图片'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-2">
                                    <img height="100px" width="100px"
                                         src="{{ !empty($apply['annex']) ? asset($apply['annex']) : asset('img/blank.png') }}"
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
                                {{ \App\Http\Components\Helpers\AttendanceHelper::showApprovalStep($apply['step_user']) }}
                            </span>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                {!! Form::label('reason', trans('att.审核状态'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-6">
                            <span class="help-block m-b-none">
                                {{ empty($apply['review_user_id']) ? \App\Models\Material\Apply::$stateChar[$apply['state']] : "待{$user->alias}({$user->username})审核"}}
                            </span>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div style="height: 20em;" class="form-group">
                                {!! Form::label('assign_uid', trans('att.处理详情'), ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    @foreach($logs as $lk => $lv)
                                        <span class="help-block m-b-none">
                                    <label class="label-primary b-r-sm">{{ $lv->created_at }}</label>
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
                    </div>
                    <div class="row">
                        <div class="form-group m-t-md">
                            <div class="col-sm-4 col-sm-offset-4" id="operate">
                                {{--@if(Entrust::can('leave.review'))
                                    @if(in_array($leave->status, [0, 1]))
                                        @if($leave->review_user_id == Auth::user()->user_id )--}}
                                    <div class="col-lg-2"><a data-id='{{ \App\Models\Material\Apply::APPLY_PASS }}'
                                                             class="btn btn-success">{{ trans('att.审核通过') }}</a></div>
                                    <div class="col-lg-2"><a data-id='{{ \App\Models\Material\Apply::APPLY_FAIL }}'
                                                             class="btn btn-warning">{{ trans('att.拒绝通过') }}</a></div>
                                    <div class="col-lg-2"><a data-id="{{ \App\Models\Material\Apply::APPLY_CANCEL }}"
                                                class="btn btn-info">{{ trans('att.取消申请') }}</a></div>
                                    <div class="col-lg-2"><a data-toggle="modal" data-target="#approve-modal" id="return"
                                                         class="btn btn-primary">{{ trans('att.确认归还') }}</a></div>
                                    @include('material.approve-modal')
                                    <div class="col-lg-2"><a class="btn btn-danger" id="back">{{ trans('att.返回列表') }}</a></div>
                                {{--<div>
                                    <a id="back_status" data-id=5 class="btn btn-primary">{{ trans('att.转审') }}</a>
                                </div>--}}


                                {{--        @endif
                                    @endif
                                @endif--}}
                                {{--@if((int)$type === \App\Models\Attendance\Leave::LOGIN_VERIFY_INFO)--}}
                                {{--<a href="{{route('leave.review.info')}}" class="btn btn-info">{{ trans('att.返回列表') }}</a>--}}
                                {{--@else--}}
                                {{--<a href="{{route('leave.info')}}" class="btn btn-info">{{ trans('att.返回列表') }}</a>--}}
                                {{--@endif--}}
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {

        $('#operate a:not(' + '#return, #back)').click(function () {
            var status = $(this).data('id');
            edit_status(status, '是否' + $(this).text() + '!');
        });

        function edit_status(status, $msg) {
            if (confirm($msg) == false) {
                return false;
            }
            $(location).prop('href', '{{ route('material.approve.optStatus',['id' => $apply['id']]) }}' + "?status=" + status);
        }

        $('[name=submit]').click(function () {
            var invIds = [];
            $('.i-checks:checked').each(function (index, ele) {
                invIds.push($(ele).val());
            });
            $(location).prop('href', '{{ route('material.approve.return') }}' + '?inventoryIds=' + JSON.stringify(invIds));
        });
    });
</script>
@endpush