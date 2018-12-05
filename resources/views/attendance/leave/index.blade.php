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
                <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::CHANGE]) }}" class="btn btn-success btn-sm">{{ trans('加班/调休申请') }}</a>
                <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::RECHECK]) }}" class="btn btn-danger btn-sm">{{ trans('补打卡') }}</a>
            @endif
            @if(Entrust::can(['leave.batchOvertime']))
                <a href="{{ route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::OVERTIME]) }}" class="btn btn-info btn-sm">{{ trans('批量申请加班') }}</a>

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
                        @foreach($remainWelfare as $k => $v)
                            <p class="list-group-item">{!!  $v['holiday_name'] .' : ' .$v['msg']!!}</p>
                        @endforeach

                    </div>
                    <div class="col-md-10 table-responsive pre-scrollable" style="padding-left: 10px; border-left: 1px solid #e7eaec;">
                        <table id="example" class="table  dataTable table-striped tooltip-demo">
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
                                <td>{!! \App\Models\Sys\HolidayConfig::applyTypeColor($v['holidayConfig']->apply_type_id) !!}</td>
                                <td>
                                    {{ \App\Models\Sys\HolidayConfig::getHolidayList()[$v['holiday_id']] ?? '数据异常' }}
                                </td>
                                <td>
                                    {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($v['holiday_id'], $v['start_time'], $v['start_id'], $v['number_day'])['time']}}
                                </td>
                                <td>
                                    {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($v['holiday_id'], $v['end_time'], $v['end_id'], $v['number_day'])['time']}}
                                </td>
                                <td>
                                    {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($v['holiday_id'], $v['start_time'], $v['start_id'], $v['number_day'])['number_day']}}
                                </td>
                                <td><pre style="height: 3em;width: 10em">{{ $v['reason'] }}</pre></td>
                                <td>{{ $v['created_at'] }}</td>
                                <td>{!! \App\Models\Attendance\Leave::leaveColorStatus($v['status']) !!}</td>
                                <td>
                                   {{-- @if(Entrust::can(['leave.edit']))
                                        {!! BaseHtml::tooltip(trans('app.设置'), route('leave.edit', ['id' => $v['leave_id']]), 'cog fa-lg') !!}
                                    @endif--}}
                                    @if(($v['user_id'] === \Auth::user()->user_id || $v['review_user_id'] == \Auth::user()->user_id || in_array(\Auth::user()->user_id, $userIds[$v['leave_id']] ?? [])) && Entrust::can(['leave.create']))
                                       {{--针对批量调休成员查看主订单--}}
                                        @if(!empty($v['parent_id']))
                                            {!! BaseHtml::tooltip(trans('att.请假详情'), route('leave.optInfo', ['id' => $v['parent_id']]), 'cog fa fa-newspaper-o') !!}
                                        @else
                                            {!! BaseHtml::tooltip(trans('att.请假详情'), route('leave.optInfo', ['id' => $v['leave_id']]), 'cog fa fa-newspaper-o') !!}

                                        @endif
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
@include('widget.datatable')
@section('scripts-last')
    <script>
        $(function() {
            {{--{!! BaseChart::nativeDataTable('example')!!}--}}
                $('#example').dataTable({
                language: {
                    url: '{{ asset('js/plugins/dataTables/i18n/Chinese.json') }}'
                },
                bLengthChange: false,
                paging: 30,
                info: false,
                searching: false,
                fixedHeader: true,
                "order": [[7, "desc"]],
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel'}
                ]
            });
        });
    </script>
@endsection
