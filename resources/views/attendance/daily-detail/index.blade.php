@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
@endsection

@section('content')

    @include('flash::message')
    @if(Entrust::can(['daily-detail.confirm']))
        @include('attendance.daily-detail.notice')
    @endif


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
                                <th>{{ trans('att.日期') }}</th>
                                <th>{{ trans('att.工号') }}</th>
                                <th>{{ trans('att.姓名') }}</th>
                                <th>{{ trans('att.上班时间') }}</th>
                                <th>{{ trans('att.下班时间') }}</th>
                                <th>{{ trans('att.当日迟到分钟数') }}</th>
                                <th>{{ trans('att.剩余缓冲时间') }}</th>
                                <th>{{ trans('att.扣分') }}</th>
                                <th>{{ trans('att.类型') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                                @if(Entrust::can('appeal.store'))
                                    <th>{{ trans('att.对考勤有疑问?') }}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>{{ $v['day'] }}</td>
                                    <td>{{ $userInfo['username'] }}</td>
                                    <td>{{ $userInfo['alias'] }}</td>
                                    @if(empty($v['punch_start_time']))
                                        <td style="color: red">--</td>
                                    @else
                                        <td @if($danger[$v['day']]['on_work'] === true) style="color: red" @endif>{{ $v['punch_start_time'] }}</td>
                                    @endif

                                    @if(empty($v['punch_end_time']))
                                        <td style="color: red">--</td>
                                    @else
                                        <td @if($danger[$v['day']]['off_work'] === true) style="color: red" @endif>{{ $v['punch_end_time'] }}</td>
                                    @endif

                                    <td>{{ $v['heap_late_num'] ?? '--' }}</td>
                                    <td>{{ $v['lave_buffer_num'] ?? '--'  }}</td>
                                    <td>{{ $v['deduction_num'] ?? '--'  }}</td>
                                    <td>
                                        {{ \App\Http\Components\Helpers\AttendanceHelper::showLeaveIds($v['leave_id']) }}
                                    </td>

                                    <td>
                                        {{--节日加班不需要补打卡与补假操作--}}

                                        @if((empty($v['punch_start_time']) || empty($v['punch_end_time'])))
                                        )

                                            <a href="{{route('leave.create', [
                                                'id' => \App\Models\Sys\HolidayConfig::RECHECK,
                                                'day' => $v['day']
                                            ])}}">
                                                {{ trans('att.补打卡') }}
                                            </a>

                                            <a href="{{route('leave.create', ['id' => \App\Models\Sys\HolidayConfig::LEAVEID])}}">
                                                {{ trans('att.补假') }}
                                            </a>
                                        @endif
                                    </td>
                                    @if(Entrust::can('appeal.store'))
                                        <td>
                                            @if(!isset($appealData[$v['id']]))
                                                <a data-toggle="modal" data-target="#exampleModal"
                                                   data-whatever="{{serialize([
                                                   'appeal_id' => $v['id'], 'appeal_type' => \App\Models\Attendance\Appeal::APPEAL_DAILY
                                                   ])}}">申诉</a>
                                            @else
                                                <span>{{ \App\Models\Attendance\Appeal::getTextArr()[$appealData[$v['id']]] }}</span>
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
