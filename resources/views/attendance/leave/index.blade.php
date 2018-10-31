@extends('attendance.side-nav')

@push('css')
<style type="text/css">
    .pre-scrollable {
        max-height: 650px;
        overflow-y: scroll;
    }
    ::-webkit-scrollbar {
        width: 8px;
    }
    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888;
    }
    .panel-heading {
        padding: 0;
    }
</style>
@endpush

@section('title', $title)

@section('page-head')
    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['leave.create']))
                <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::LEAVEID]) }}" class="btn btn-primary btn-sm">{{ trans('请假申请') }}</a>
                <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::CHANGE]) }}" class="btn btn-success btn-sm">{{ trans('调休申请') }}</a>
                <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::RECHECK]) }}" class="btn btn-danger btn-sm">{{ trans('补打卡') }}</a>
            @endif
        </div>
    </div>

@endsection

@section('content')

    @include('flash::message')
    @include('widget.scope-leave', ['scope' => $scope])

    <div class="row">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $title }}</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel blank-panel">
                            <div class="panel-heading">
                                <div class="panel-options">
                                    <ul class="nav nav-tabs">
                                        @foreach($types as $k => $v)
                                            <li @if($k == $type) class="active" @endif>
                                                <a  class="dropdown-toggle count-info" href="{{ route('leave.info', ['type' => $k]) }}">{{ $v }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2 list-group" style="padding: 10px">
                        <div class="list-group-item"><h4 class="list-group-item-heading">剩余可申请的福利假</h4></div>
                        <p class="list-group-item">剩余年假: {{ $remainWelfare['year'] ?? '尚未配置该福利假' }}</p>
                        <p class="list-group-item">剩余节假日调休: {{ $remainWelfare['change'] ?? '尚未配置该福利假' }}</p>
                        <p class="list-group-item">剩余探亲假: {{ $remainWelfare['visit'] ?? '尚未配置该福利假' }}</p>
                    </div>
                    <div class="col-md-10 table-responsive pre-scrollable" style="padding-left: 10px; border-left: 1px solid #e7eaec;">
                        <table class="table table-striped tooltip-demo">
                        <thead>
                        <tr>
                            <th>{{ trans('att.申请类型') }}</th>
                            <th>{{ trans('att.明细类型') }}</th>
                            <th>{{ trans('att.开始时间') }}</th>
                            <th>{{ trans('att.结束时间') }}</th>
                            <th>{{ trans('att.申请时长') }}</th>
                            <th>{{ trans('att.申请事由') }}</th>
                            <th>{{ trans('att.申请时间') }}</th>
                            <th>{{ trans('att.申请状态') }}</th>
                            <th>{{ trans('att.操作') }}</th>
                            @if(Entrust::can('appeal.store'))
                                <th>{{ trans('att.对假期有疑问?') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($data as $k => $v)
                            <tr>

                                <td>{{ \App\Models\Sys\HolidayConfig::$applyType[\App\Models\Sys\HolidayConfig::getHolidayApplyList()[$v['holiday_id']]] }}</td>
                                <td>
                                    {{ \App\Models\Sys\HolidayConfig::getHolidayList()[$v['holiday_id']] }}
                                </td>

                                <td>

                                    @if(\App\Models\Sys\HolidayConfig::getHolidayApplyList()[$v['holiday_id']] === 3)
                                        {{$v['start_time'] ?? '---'}}
                                    @else
                                        {{ date('Y-m-d', strtotime($v['start_time'])).' '.\App\Models\Attendance\Leave::$startId[$v['start_id']] }}
                                    @endif
                                </td>
                                <td>
                                    @if(\App\Models\Sys\HolidayConfig::getHolidayApplyList()[$v['holiday_id']] === 3)
                                        {{$v['end_time'] ?? '---'}}
                                    @else
                                        {{ date('Y-m-d', strtotime($v['end_time'])).' '.\App\Models\Attendance\Leave::$endId[$v['end_id']] }}
                                    @endif
                                </td>
                                <td>

                                    {{ empty($v['number_day']) ? '---' : $v['number_day'] . '天'}}
                                </td>

                                <td><pre style="height: 5em;width: 20em">{{ $v['reason'] }}</pre></td>
                                <td>{{ $v['created_at'] }}</td>
                                <td>{{ \App\Models\Attendance\Leave::$status[$v['status']] }}</td>
                                <td>
                                    @if(Entrust::can(['leave-all', 'leave.edit']))
                                        {!! BaseHtml::tooltip(trans('app.设置'), route('leave.edit', ['id' => $v['leave_id']]), 'cog fa-lg') !!}
                                    @endif
                                    @if(($v['user_id'] == \Auth::user()->user_id || $v['review_user_id'] == \Auth::user()->user_id || in_array(\Auth::user()->user_id, $userIds)) && Entrust::can(['leave.edit', 'leave.review']))
                                        {!! BaseHtml::tooltip(trans('att.请假详情'), route('leave.optInfo', ['id' => $v['leave_id'], 'type' => \App\Models\Attendance\Leave::LOGIN_INFO]), 'cog fa fa-newspaper-o') !!}
                                    @endif
                                </td>
                                @if(Entrust::can('appeal.store'))
                                    <td>
                                        @if(!isset($appealData[$v['leave_id']]))
                                            <a data-toggle="modal" data-target="#exampleModal" data-whatever="{{
                                               serialize(['appeal_id' => $v['leave_id'],
                                               'appeal_type' => \App\Models\Attendance\Appeal::APPEAL_LEAVE])
                                               }}">申诉</a>
                                        @else
                                            <span>{{ \App\Models\Attendance\Appeal::getTextArr()[$appealData[$v['leave_id']]] }}</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('widget.appeal-modal')

@endsection
@include('widget.select2')
