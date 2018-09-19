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
            @include('widget.scope-month', ['scope' => $scope])
        </div>
    @endif

    <div class="ibox-content" style="margin-bottom: 20px">
        <div class="table-responsive">
            <table class="table tooltip-demo table-bordered" id="example" width="100%">
                <thead>
                <tr>
                    <th colspan="4" style="text-align: center">{{ trans('att.基础信息') }}</th>
                    <th colspan="7" style="text-align: center">{{ trans('att.考勤天数') }}</th>
                    <th colspan="3" style="text-align: center">{{ trans('att.扣分统计') }}</th>
                    <th colspan="3" style="text-align: center">{{ trans('att.剩余假期') }}</th>
                    <th colspan="{{ $title == trans('att.考勤管理') ? 3 : 1 }}"
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
                        <th>选择导出</th>
                    @else
                        <th>确认通知</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <?php $i = 0;?>
                @foreach($monthData as $k => $v)
                    <tr id="{{ $i }}">
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
                                <a href="{{ route('daily-detail.review.user', ['id' => $v['user_id']]) }}">明细</a>
                            </td>
                            <td>
                                <a class="send" id="send_{{ $v['user_id'] }}" date="{{ $v['date'] }}"
                                   con_state="{{ $v['send'] }}">
                                    {{ \App\Models\Attendance\ConfirmAttendance::$stateAdmin[$v['send']] }}
                                </a>
                            </td>
                            <td>
                                {!! Form::checkbox('export', $i) !!}
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
                    <?php $i++;?>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@include('widget.select2')
@include('widget.datatable')
@include('widget.bootbox')
@push('scripts')
<script>

    //发布考勤通知
    function sendTo(id, date) {
        $.get("{{ route('daily-detail.review.send') }}", {
            "user_id": id,
            "date": date
        }, function (data) {
            var select = '#' + id;
            if (data == 'success') {
                $(select).html("已发送").css({
                    'color': '#1ab394',
                    'cursor': 'default'
                }).unbind('click');
            }
        });
    }

    $(function () {
        //发送考勤统计及确认考勤统计
        $('.send, .confirm').each(function (index, ele) {
            var id = $(ele).attr('id');
            var date = $(ele).attr('date');

            switch ($(ele).attr('con_state')) {
                case ("{{ \App\Models\Attendance\ConfirmAttendance::SEND }}"):
                    if ($(ele).attr('class') == 'send') {
                        $(ele).click(function () {
                            sendTo(id, date);
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

        //对widget进行修改
        $('#startDate').parent('div').contents().filter(function () {
            return this.nodeType === 3
        }).remove();
        $('#endDate').remove();

        //dataTable设置
        @if($title == trans('att.考勤管理'))
        var exportArr = [];
        $('input[name=export]').click(function () {
            if ($(this).is(':checked')) {
                exportArr.push($(this).val());
            } else {
                exportArr.pop($(this).val());
            }
        });

        var columns = [];
        for (var i = 0; i < 17; i++) {
            columns.push(i);
        }

        $('#example').DataTable({
            language: {
                url: '{{ asset('js/plugins/dataTables/i18n/Chinese.json') }}'
            },
            bLengthChange: false,
            paging: true,
            info: false,
            searching: false,
            fixedHeader: true,
            pageLength: 4,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                {
                    text: '批量发送',
                    action: function () {
                        if (confirm("确定要批量发送吗?")) {
                            var date = $('.send').first().attr('date');
                            $(location).attr('href', "{{ route('daily-detail.review.send', ['user_id' => 'all']) }}" + '&date=' + date);
                        }
                    }
                },
                {
                    extend: 'excel',
                    text: '导出全部',
                    exportOptions: {
                        columns: columns
                    }
                },
                {
                    extend: 'excel',
                    text: '选择导出',
                    action: function (e, dt, node, config) {
                        if (exportArr.length == 0) {
                            alert('请选择用户后再进行点击导出');
                        } else {
                            config.exportOptions.rows = exportArr;
                            config.exportOptions.columns = columns;
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, node, config);
                        }
                    }
                }
            ]
        });
        @endif
    });
</script>
@endpush