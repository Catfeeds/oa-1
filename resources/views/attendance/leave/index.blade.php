@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
    <div class="col-sm-8">
        <div class="title-action">
            @if(Entrust::can(['attendance-all', 'leave.all', 'leave.edit', 'leave.create']))
                <a href="{{ route('leave.create') }}" class="btn btn-primary btn-sm">{{ trans('请假申请') }}</a>
            @endif

            @if(Entrust::can(['leave.all', 'leave.edit', 'leave.create']))
                <a href="{{ route('leave.recheck') }}" class="btn btn-primary btn-sm">{{ trans('补打卡') }}</a>
            @endif
        </div>
    </div>

@endsection

@section('content')

    @include('flash::message')
    @include('widget.scope-date', ['scope' => $scope])

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{ $title }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-striped tooltip-demo">
                            <thead>
                            <tr>
                                <th>{{ trans('att.请假类型') }}</th>
                                <th>{{ trans('att.假期类型') }}</th>
                                <th>{{ trans('att.开始时间') }}</th>
                                <th>{{ trans('att.结束时间') }}</th>
                                <th>{{ trans('att.假期时长') }}</th>
                                <th>{{ trans('att.事由') }}</th>
                                <th>{{ trans('att.申请时间') }}</th>
                                <th>{{ trans('att.申请状态') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                                {{--<th>{{ trans('att.对假期有疑问') }}</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>{{ \App\Models\Sys\HolidayConfig::$applyType[\App\Models\Sys\HolidayConfig::getHolidayApplyList()[$v['holiday_id']]] }}</td>
                                    <td>{{ \App\Models\Sys\HolidayConfig::getHolidayList()[$v['holiday_id']] }}</td>

                                    <td>
                                        @if($v['apply_type_id'] == \App\Models\Sys\HolidayConfig::RECHECK)
                                            @if(($a = date('Y-m-d', strtotime($v['start_time']))) == \App\Models\Attendance\Leave::HASNOTIME)
                                                暂无上班补打卡
                                            @else
                                                {{ '上班补打卡:' }}<br>{{  date('Y-m-d H:i:s', strtotime($v['start_time'])) }}
                                            @endif
                                        @else
                                            {{  date('Y-m-d', strtotime($v['start_time'])).' '.
                                            \App\Models\Attendance\Leave::$startId[$v['start_id']] }}
                                        @endif
                                    </td>

                                    <td>
                                        @if($v['apply_type_id'] == \App\Models\Sys\HolidayConfig::RECHECK)
                                            @if(($a = date('Y-m-d', strtotime($v['end_time']))) == \App\Models\Attendance\Leave::HASNOTIME)
                                                暂无下班补打卡
                                            @else
                                                {{ '下班补打卡:' }}<br>{{  date('Y-m-d H:i:s', strtotime($v['end_time'])) }}
                                            @endif
                                        @else
                                            {{  date('Y-m-d', strtotime($v['end_time'])).' '.
                                            \App\Models\Attendance\Leave::$endId[$v['end_id']] }}
                                        @endif
                                    </td>

                                    <td>{{ $a = App\Components\Helper\DataHelper::diffTime(
                                    date('Y-m-d', strtotime($v['start_time']))
                                    . ' ' . \App\Models\Attendance\Leave::$startId[$v['start_id']],
                                    date('Y-m-d', strtotime($v['end_time']))
                                    . ' ' . \App\Models\Attendance\Leave::$endId[$v['end_id']])}}
                                        {{ empty($a) ? '暂无' : '天' }}</td>

                                    <td><pre style="height: 5em;width: 20em">{{ $v['reason'] }}</pre></td>
                                    <td>{{ $v['created_at'] }}</td>
                                    <td>{{ \App\Models\Attendance\Leave::$status[$v['status']] }}</td>
                                    <td>
                                        {{--@if(Entrust::can(['leave-all', 'leave.edit']))--}}
                                            {{--{!! BaseHtml::tooltip(trans('app.设置'), route('leave.edit', ['id' => $v['leave_id']]), 'cog fa-lg') !!}--}}
                                        {{--@endif--}}
                                        @if(($v['user_id'] == \Auth::user()->user_id || $v['review_user_id'] == \Auth::user()->user_id ) && Entrust::can(['attendance-all', 'leave-all', 'leave.edit', 'leave.optStatus']))
                                            {!! BaseHtml::tooltip(trans('att.请假详情'), route('leave.optInfo', ['id' => $v['leave_id']]), 'cog fa fa-newspaper-o') !!}
                                        @endif


                                    </td>
                                    {{--<td></td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('widget.select2')
