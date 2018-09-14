@push('css')
<style type="text/css">
    .m-b-md {
        margin-bottom: 12px
    }
</style>
@endpush

@if($title == trans('att.我的每日考勤详情') || $title == trans('att.考勤管理'))
    @if($title == trans('att.我的每日考勤详情'))
        <div class="ibox-title">
            <h4 style="float: left">考勤确认记录</h4>
            @include('widget.scope-date', ['scope' => $scope])
        </div>
    @endif

    <div class="ibox-content" style="margin-bottom: 20px">
        <div class="table-responsive">
            <table class="table tooltip-demo table-bordered">
                <thead>
                <tr>
                    <th colspan="4" style="text-align: center">{{ trans('att.基础信息') }}</th>
                    <th colspan="7" style="text-align: center">{{ trans('att.考勤天数') }}</th>
                    <th colspan="3" style="text-align: center">{{ trans('att.扣分统计') }}</th>
                    <th colspan="3" style="text-align: center">{{ trans('att.剩余假期') }}</th>
                    <th colspan="{{ $title == trans('att.考勤管理') ? 2 : 1 }}"
                        style="text-align: center">{{ trans('att.操作') }}</th>
                </tr>
                <tr>
                    <th>月份</th>
                    <th>工号</th>
                    <th>姓名</th>
                    <th>部门</th>
                    <th>应到天数</th>
                    <th>实到天数</th>
                    <th>加班</th>
                    <th>调休</th>
                    <th>无薪假</th>
                    <th>带薪假</th>
                    <th>全勤</th>
                    <th>迟到总分钟</th>
                    <th>其他</th>
                    <th>合计扣分</th>
                    <th>剩余年假</th>
                    <th>剩余节日调休假</th>
                    <th>剩余探亲假</th>
                    @if($title == trans('att.考勤管理'))
                        <th>当月明细</th>
                        <th>发布确认通知</th>
                    @else
                        <th>确认通知</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($monthData as $v)
                    <tr>
                        <td>{{ $v['date'] }}</td>
                        <td>{{ $v['user_id'] }}</td>
                        <td>{{ $v['user_alias'] }}</td>
                        <td>{{ $v['user_dept'] }}</td>
                        <td>{{ $v['should_come'] }}</td>
                        <td>{{ $v['actually_come'] }}</td>
                        <td>{{ $v['overtime'] }}</td>
                        <td>{{ $v['change_time'] }}</td>
                        <td>{{ $v['no_salary_leave'] }}</td>
                        <td>{{ $v['has_salary_leave'] }}</td>
                        <td>{{ $v['is_full_work'] }}</td>
                        <td>{{ $v['late_num'] }}</td>
                        <td>{{ $v['other'] }}</td>
                        <td>{{ $v['deduct_num'] }}</td>
                        <td>{{ $v['remain_year_holiday'] }}</td>
                        <td>{{ $v['remain_change'] }}</td>
                        <td>{{ $v['remain_visit'] }}</td>
                        @if($title == trans('att.考勤管理'))
                            <td>
                                <a href="{{ route('daily-detail.review.user', ['id' => $v['user_id']]) }}">{{ $v['detail'] }}</a>
                            </td>
                            <td>
                                <a class="send" id="send_{{ $v['user_id'] }}" date="{{ $v['date'] }}"
                                   con_state="{{ $v['send'] }}">
                                    {{ \App\Models\Attendance\ConfirmAttendance::$stateAdmin[$v['send']] }}
                                </a>
                            </td>
                        @else
                            <td>
                                <a class="confirm" con_state="{{ $v['send'] }}" id="confirm_{{ $v['user_id'] }}"
                                   @if($v['send'] == \App\Models\Attendance\ConfirmAttendance::SENT)
                                   href="{{ route('daily-detail.confirm', ['id' => $v['user_id'], 'date' => $v['date']]) }}"
                                        @endif
                                >
                                    {{ \App\Models\Attendance\ConfirmAttendance::$stateUser[$v['send']] }}
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@push('scripts')
<script>
    $(function () {
        $('.send, .confirm').each(function (index, ele) {
            var id = $(ele).attr('id');
            var date = $(ele).attr('date');

            switch ($(ele).attr('con_state')) {
                case ("{{ \App\Models\Attendance\ConfirmAttendance::SEND }}"):
                    if ($(ele).attr('class') == 'send') {
                        $(ele).click(function () {

                            $.get("{{ route('daily-detail.review.send') }}", {
                                "user_id": id,
                                "date": date
                            }, function (data) {
                                if (data == 'success') {
                                    $('#' + id).html("已发送").css({
                                        'color': '#1ab394',
                                        'cursor': 'default'
                                    }).unbind('click');
                                } else {
                                    $('#' + id).css({'color': 'red', 'cursor': 'default'}).html("发送失败").unbind('click');
                                }
                            });
                        });
                    } else {
                        $('#' + id).css({
                            'color': '#686b6d',
                            'cursor': 'default'
                        }).parents('tr').css({'background-color': 'rgb(239, 239, 239)'});
                    }
                    break;

                case ("{{ \App\Models\Attendance\ConfirmAttendance:: SENT}}"):
                    if ($(ele).attr('class') == 'send') {
                        $('#' + id).css({'color': '#1ab394', 'cursor': 'default'});
                    } else {
                        $('#' + id).css({'color': '#1ab394', 'cursor': 'pointer'});
                    }
                    break;

                case ("{{ \App\Models\Attendance\ConfirmAttendance::CONFIRM }}"):
                    $('#' + id).css({'color': '#ed5565', 'cursor': 'default'});
                    break;
            }
        });
    });
</script>
@endpush