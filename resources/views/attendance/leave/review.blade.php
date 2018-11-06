@extends('attendance.side-nav')

@section('title', $title)

@section('page-head')
    @parent

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
                    @if(Entrust::can('leave.review'))
                        @include('widget.review-batch-operation-btn', ['btn' => [['review-btn-ok', '批量通过', 'btn-success'],['review-btn-no', '批量拒绝', 'btn-group']]])
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-striped tooltip-demo">
                            <thead>
                            <tr>
                                <th>-</th>
                                <th>{{ trans('att.请假类型') }}</th>
                                <th>{{ trans('att.假期类型') }}</th>
                                <th>{{ trans('att.申请人') }}</th>
                                <th>{{ trans('att.开始时间') }}</th>
                                <th>{{ trans('att.结束时间') }}</th>
                                <th>{{ trans('att.假期时长') }}</th>
                                <th>{{ trans('att.事由') }}</th>
                                <th>{{ trans('att.申请时间') }}</th>
                                <th>{{ trans('att.申请状态') }}</th>
                                <th>{{ trans('att.操作') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $v)
                                <tr>
                                    <td>
                                        @if(in_array($v['status'], [0, 1]) && $v['review_user_id'] == Auth::user()->user_id)
                                             <input id="text_box" type="checkbox" class="i-checks" name="leaveIds[]" value="{{ $v['leave_id'] }}">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ \App\Models\Sys\HolidayConfig::$applyType[\App\Models\Sys\HolidayConfig::getHolidayApplyList()[$v['holiday_id']]] }}</td>
                                    <td>{{ \App\Models\Sys\HolidayConfig::holidayList()[$v['holiday_id']] }}</td>
                                    <td>{{ \App\User::getAliasList()[$v['user_id']] ?? '' }}</td>
                                    <td>
                                        {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($v['holiday_id'], $v['start_time'], $v['start_id'], $v['number_day'])['time']}}
                                    </td>
                                    <td>
                                        {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($v['holiday_id'], $v['end_time'], $v['end_id'], $v['number_day'])['time']}}
                                    </td>
                                    <td>
                                        {{\App\Http\Components\Helpers\AttendanceHelper::spliceLeaveTime($v['holiday_id'], $v['start_time'], $v['start_id'], $v['number_day'])['number_day']}}
                                    </td>
                                    <td><pre style="height: 5em;width: 20em">{{ $v['reason'] }}</pre></td>
                                    <td>{{ $v['created_at'] }}</td>
                                    <td>{{ \App\Models\Attendance\Leave::$status[$v['status']] }}</td>
                                    <td>
                                        {!! BaseHtml::tooltip(trans('att.请假详情'), route('leave.review.optInfo', ['id' => $v['leave_id'], 'type' => \App\Models\Attendance\Leave::LOGIN_VERIFY_INFO]), 'cog fa fa-newspaper-o') !!}
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
@include('widget.bootbox')
@include('widget.select2')
@include('widget.icheck')
@include('widget.review-batch-operation')
@push('scripts')
<script>
    $(function () {

        $('#review-btn-ok').batch({
            url: '{{ route('leave.review.batchOptStatus', ['status' => 3]) }}',
            selector: '.i-checks:checked',
            type: '0',
            alert_confirm: '确定要批量通过审核吗？'
        });

        $('#review-btn-no').batch({
            url: '{{ route('leave.review.batchOptStatus', ['status' => 2]) }}',
            selector: '.i-checks:checked',
            type: '0',
            alert_confirm: '确定要批量拒绝审核吗？'
        });

    });
</script>
@endpush