@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent
    <div class="col-sm-8">
        <div class="title-action">
            <a href="javascript:history.back()" class="btn btn-success btn-sm">{{ trans('返回') }}</a>
        </div>
    </div>
@endsection

@section('content')
    @include('flash::message')
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
                                        <td>{{ $v['punch_start_time'] }}</td>
                                    @endif
                                    @if(empty($v['punch_end_time']))
                                        <td style="color: red">--</td>
                                    @else
                                        <td>{{ $v['punch_end_time'] }}</td>
                                    @endif
                                    <td>{{ $v['heap_late_num'] ? $v['heap_late_num']  : '--' }}</td>
                                    <td>{{ $v['lave_buffer_num'] ? $v['heap_late_num']  : '--'  }}</td>
                                    <td>{{ $v['deduction_num'] ? $v['heap_late_num']  : '--'  }}</td>
                                    <td>
                                        {{ \App\Http\Components\Helpers\AttendanceHelper::showLeaveIds($v['leave_id']) }}
                                    </td>
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