@push('css')
<style type="text/css">
    .m-b-md {
        margin-bottom: 12px
    }
</style>
@endpush

<div class="ibox-title">
    <h4 style="float: left">考勤确认记录</h4>
    @include('widget.scope-month', ['scope' => $scope])
</div>

<div class="ibox-content" style="margin-bottom: 20px">
    @if($monthInfo[0] == 'success')
        <div class="table-responsive">
            <table class="table tooltip-demo table-bordered" id="example" width="100%">
                <thead>
                <tr>
                    <th colspan="4" style="text-align: center">{{ trans('att.基础信息') }}</th>
                    <th colspan="7" style="text-align: center">{{ trans('att.考勤天数') }}</th>
                    <th colspan="3" style="text-align: center">{{ trans('att.扣分统计') }}</th>
                    <th colspan="3" style="text-align: center">{{ trans('att.剩余假期') }}</th>
                    <th colspan="1" style="text-align: center">{{ trans('att.操作') }}</th>
                </tr>
                <tr>
                    <th>{{ trans('att.月份') }}</th>
                    <th>{{ trans('att.工号') }}</th>
                    <th>{{ trans('att.姓名') }}</th>
                    <th>{{ trans('att.部门') }}</th>
                    <th>{{ trans('att.应到天数') }}</th>
                    <th>{{ trans('att.实到天数') }}</th>
                    <th>{{ trans('att.加班') }}</th>
                    <th>{{ trans('att.调休') }}</th>
                    <th>{{ trans('att.无薪假') }}</th>
                    <th>{{ trans('att.带薪假') }}</th>
                    <th>{{ trans('att.全勤') }}</th>
                    <th>{{ trans('att.迟到总分钟') }}</th>
                    <th>{{ trans('att.其他') }}</th>
                    <th>{{ trans('att.合计扣分') }}</th>
                    <th>{{ trans('att.剩余年假') }}</th>
                    <th>{{ trans('att.剩余节日调休假') }}</th>
                    <th>{{ trans('att.剩余探亲假') }}</th>
                    <th>{{ trans('att.确认通知') }}</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 0;?>
                @foreach($monthInfo[1] as $k => $v)
                    <tr id="{{ $i }}">
                        <td>{{ $v['date'] }}</td>
                        <td>{{ $v['user_name'] }}</td>
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
                        <td>
                            <a class="confirm" con_state="{{ $v['send'] }}" id="confirm_{{ $v['user_id'] }}"
                               @if($v['send'] == \App\Models\Attendance\ConfirmAttendance::SENT)
                               href="{{ route('daily-detail.confirm', ['id' => $v['user_id'], 'date' => $v['date']]) }}"
                                    @endif
                            >
                                {{ \App\Models\Attendance\ConfirmAttendance::$stateUser[$v['send']] }}
                            </a>
                        </td>
                    </tr>
                    <?php $i++;?>
                @endforeach
                </tbody>
            </table>
        </div>
    @elseif($monthInfo[0] == 'error')
        <p style="color: indianred">未配置相应的假期,请联系管理员</p>
    @endif
</div>


@include('widget.select2')
@include('widget.datatable')
@include('widget.bootbox')
@push('scripts')
<script>

    $(function () {
        //发送考勤统计及确认考勤统计
        $('.confirm').each(function (index, ele) {
            var id = '#' + $(ele).attr('id');

            switch ($(ele).attr('con_state')) {
                case ("{{ \App\Models\Attendance\ConfirmAttendance::SEND }}"):
                    $(id).css({
                        'color': '#686b6d',
                        'cursor': 'default'
                    }).parents('tr').css({'background-color': 'rgb(239, 239, 239)'});
                    break;

                case ("{{ \App\Models\Attendance\ConfirmAttendance:: SENT}}"):
                    $(id).css({'color': '#1ab394', 'cursor': 'pointer'});
                    break;

                case ("{{ \App\Models\Attendance\ConfirmAttendance::CONFIRM }}"):
                    $(id).css({'color': '#ed5565', 'cursor': 'default'});
                    break;
            }
        });

        //对widget进行修改
        $('#startDate').parent('div').contents().filter(function () {
            return this.nodeType === 3
        }).remove();
        $('#endDate').remove();
    });
</script>
@endpush