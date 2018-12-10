@push('css')
<style type="text/css">
    #example > thead th {
        padding-right: 5px;
    }
    #example > thead tr:nth-child(3) th {
        border-right-width: thin;
    }
</style>
@endpush
<div class="ibox-content" style="margin-bottom: 20px">
    @if($monthInfo[0] == 'success')
        <div class="table-responsive">
            <table class="table tooltip-demo table-bordered" id="example" width="100%">
                <thead>
                <tr>
                    <th colspan="4" style="text-align: center">{{ trans('att.基础信息') }}</th>
                    <th colspan="{{ 13 + $monthInfo[1][Auth::user()->user_id]['paid_unpaid_conf_count'] }}" style="text-align: center">{{ trans('att.考勤天数') }}</th>
                    <th colspan="3" style="text-align: center">{{ trans('att.扣分统计') }}</th>
                    <th colspan="7" style="text-align: center">{{ trans('att.剩余假期') }}</th>
                    @if(Entrust::can(['daily-detail.review.send', 'daily-detail.review.detail', 'daily-detail.review.export']))
                        <th style="text-align: center">{{ trans('att.操作') }}</th>
                    @endif
                </tr>
                <tr style="height: 10px">
                    <th rowspan="2">{{ trans('att.月份') }}</th>
                    <th rowspan="2">{{ trans('att.工号') }}</th>
                    <th rowspan="2">{{ trans('att.姓名') }}</th>
                    <th rowspan="2">{{ trans('att.部门') }}</th>
                    <th rowspan="2">{{ trans('att.应到天数') }}</th>
                    <th rowspan="2">{{ trans('att.实到天数') }}</th>
                    <th colspan="5" style="background-color: white; text-align: center; width: 5%">加班次数</th>
                    <th colspan="5" style="background-color: white; text-align: center; width: 5%">调休次数</th>
                    {{--<th rowspan="2">{{ trans('att.无薪假') }}</th>
                    <th rowspan="2">{{ trans('att.带薪假') }}</th>--}}
                    @foreach($monthInfo[1][Auth::user()->user_id]['paid_unpaid_conf'] as $items)
                        @foreach($items as $item)
                            <th rowspan="2">{{ $item['holiday'] }}</th>
                        @endforeach
                    @endforeach
                    <th rowspan="2">{{ trans('att.全勤') }}</th>
                    <th rowspan="2">{{ trans('att.迟到总分钟') }}</th>
                    <th rowspan="2">{{ trans('att.其他') }}</th>
                    <th rowspan="2">{{ trans('att.合计扣分') }}</th>
                    <th colspan="5" style="background-color: white; text-align: center; width: 5%">剩余调休假次数</th>
                    <th rowspan="2">{{ trans('att.剩余年假') }}</th>
                    <th rowspan="2">{{ trans('att.剩余探亲假') }}</th>

                    <?php $num = 0;?>
                    @if(Entrust::can(['daily-detail.review.detail']))
                        <th rowspan="2">{{ trans('att.当月明细') }}</th> <?php $num++;?>
                    @endif
                    @if(Entrust::can(['daily-detail.review.send']))
                        <th rowspan="2">{{ trans('att.发布确认通知') }}</th> <?php $num++;?>
                    @endif
                    @if(Entrust::can(['daily-detail.review.export']))
                        <th rowspan="2">{{ trans('att.选择导出') }}</th> <?php $num++;?>
                    @endif
                    {!! Form::hidden('permissions', $num) !!}
                </tr>
                <tr>
                    <th>9<br>~<br>12</th>
                    <th>9<br>~<br>18</th>
                    <th>9<br>~<br>20</th>
                    <th>14<br>~<br>20</th>
                    <th>14<br>~<br>18</th>

                    <th>9<br>~<br>12</th>
                    <th>9<br>~<br>18</th>
                    <th>9<br>~<br>20</th>
                    <th>14<br>~<br>20</th>
                    <th>14<br>~<br>18</th>

                    <th>9<br>~<br>12</th>
                    <th>9<br>~<br>18</th>
                    <th>9<br>~<br>20</th>
                    <th>14<br>~<br>20</th>
                    <th>14<br>~<br>18</th>
                </tr>
                </thead>
                <tbody>
                <?php $exp_id = 0;?>
                @foreach($monthInfo[1] as $k => $v)
                    <tr id="{{ $exp_id }}">
                        <td>{{ $v['date'] }}</td>
                        <td>{{ $v['user_name'] }}</td>
                        <td>{{ $v['user_alias'] }}</td>
                        <td>{{ $v['user_dept'] }}</td>
                        <td>{{ $v['should_come'] }}</td>
                        <td>{{ $v['actually_come'] }}</td>

                        @for($i = 1; $i <= 5; $i ++)
                            <td>{{ $v['overtime'][$i] ?? 0 }}</td>
                        @endfor

                        @for($i = 1; $i <= 5; $i ++)
                            <td>{{ $v['change_time'][$i] ?? 0 }}</td>
                        @endfor

                        {{--<td>{{ $v['no_salary_leave'] }}</td>
                        <td>{{ $v['has_salary_leave'] }}</td>--}}
                        @foreach($monthInfo[1][Auth::user()->user_id]['paid_unpaid_conf'][\App\Models\Sys\HolidayConfig::CYPHER_PAID] as $item)
                            <td>{{ $v['has_salary_leave'][$item['holiday_id']] ?? 0 }}</td>
                        @endforeach
                        @foreach($monthInfo[1][Auth::user()->user_id]['paid_unpaid_conf'][\App\Models\Sys\HolidayConfig::CYPHER_UNPAID] as $item)
                            <td>{{ $v['has_salary_leave'][$item['holiday_id']] ?? 0 }}</td>
                        @endforeach
                        <td>{{ $v['is_full_work'] }}</td>
                        <td>{{ $v['late_num'] }}</td>
                        <td>{{ $v['other'] }}</td>
                        <td>{{ $v['deduct_num'] }}</td>

                        @for($i = 1; $i <= 5; $i ++)
                            <td>{{ $v['remain_change'][$i] ?? 0 }}</td>
                        @endfor

                        <td>{{ $v['remain_year_holiday'] }}</td>
                        <td>{{ $v['remain_visit'] }}</td>

                        @if(Entrust::can(['daily-detail.review.detail']))
                            <td>
                                <a href="{{ route('daily-detail.review.user', ['id' => $v['user_id'], 'start_date' => strtotime($scope->startDate)]) }}">{{ trans('att.明细') }}</a>
                            </td>
                        @endif

                        @if(Entrust::can(['daily-detail.review.send']))
                            <td>
                                <a class="send" id="send_{{ $v['user_id'] }}" date="{{ $v['date'] }}"
                                   con_state="{{ $v['send'] }}">
                                    {{ \App\Models\Attendance\ConfirmAttendance::$stateAdmin[$v['send']] }}
                                </a>
                            </td>
                        @endif

                        @if(Entrust::can(['daily-detail.review.export']))
                            <td>
                                {!! Form::checkbox('export', $exp_id) !!}
                            </td>
                        @endif

                    </tr>
                    <?php $exp_id++;?>
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

    var exportArr = [];//选中的checkbox

    /**
     * @param columns //打印表的哪些列
     */
    function dataTableAction(columns){
        $('#example').DataTable({
            language: {
                url: '{{ asset('js/plugins/dataTables/i18n/Chinese.json') }}'
            },
            bLengthChange: false,
            paging: true,
            info: false,
            searching: false,
            pageLength: 20,
            responsive: true,
            autowidth:true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                {
                    text: '全选',
                    action: function () {
                        $('[name=export]').each(function (index, ele) {
                            $(ele).prop('checked', true);
                            $.inArray($(ele).val(), exportArr) == -1 ? exportArr.push($(ele).val()) : '';
                        });
                    }
                },
                {
                    text: '反选',
                    action: function () {
                        $('[name=export]').each(function (index, ele) {
                            if ($(ele).prop('checked') == true) {
                                $(ele).prop('checked', false);
                                var i = exportArr.indexOf($(ele).val());
                                if (i != -1) {
                                    exportArr.splice(i, 1);
                                }
                            }else {
                                $(ele).prop('checked', true);
                                $.inArray($(ele).val(), exportArr) == -1 ? exportArr.push($(ele).val()) : '';
                            }
                        });
                    }
                },
                {
                    text: '全部取消',
                    action: function () {
                        $('[name=export]:checked').each(function (index, ele) {
                            $(ele).prop('checked', false);
                            var i = exportArr.indexOf($(ele).val());
                            if (i != -1) {
                                exportArr.splice(i, 1);
                            }
                        });
                    }
                },
                    @if(Entrust::can(['daily-detail.review.export']))
                {
                    extend: 'excel',
                    text: '选择导出',
                    init: function ( dt, node, config ) {
                        node.css({'background-color':'#1ab394', 'color': 'white'});
                    },
                    filename: "{{ date('Y年m月-部分用户考勤记录') }}",
                    action: function (e, dt, node, config) {
                        if (exportArr.length == 0) {
                            alert('请选择用户后再进行点击导出');
                        } else {
                            config.exportOptions.rows = exportArr;
                            config.exportOptions.columns = columns;
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, node, config);
                        }
                    }
                },
                    @endif
                    @if(Entrust::can(['daily-detail.review.export-batch']))
                {
                    extend: 'excel',
                    text: '导出全部',
                    init: function ( dt, node, config ) {
                        node.css({'background-color':'#1c84c6', 'color': 'white'});
                    },
                    filename: "{{ date('Y年m月-全部用户考勤记录') }}",
                    exportOptions: {
                        columns: columns
                    }
                },
                @endif
            ]
        });
    }

    $(function () {

        //调整表头样式
        var num = $('input[name=permissions]').val();
        var action = $('tr').first().children().last();
        action.text() == '操作' ? action.attr('colspan', num) : '';

        //发送考勤统计及确认考勤统计
        $('.send, .confirm').each(function (index, ele) {
            var id = $(ele).attr('id');
            var id_s = '#' + id;
            var date = $(ele).attr('date');

            switch ($(ele).attr('con_state')) {
                case ("{{ \App\Models\Attendance\ConfirmAttendance::SEND }}"):
                    $(ele).click(function () {
                        sendTo(id, date);
                    });
                    break;
                case ("{{ \App\Models\Attendance\ConfirmAttendance:: SENT }}"):
                    $(id_s).css({'color': '#1ab394', 'cursor': 'default'});
                    break;
                case ("{{ \App\Models\Attendance\ConfirmAttendance::CONFIRM }}"):
                    $(id_s).css({'color': '#ed5565', 'cursor': 'default'});
                    break;
            }
        });

        //对widget进行修改
        /*$('#startDate').parent('div').contents().filter(function () {
         return this.nodeType === 3
         }).remove();
         $('#endDate').remove();*/

        //勾选添加导出行
        $('input[name=export]').click(function () {
            if ($(this).is(':checked')) {
                exportArr.push($(this).val());
            } else {
                exportArr.splice(exportArr.indexOf($(this).val()), 1);
            }
        });

        var columns = [];
        for (var i = 0; i < 17; i++) {
            columns.push(i);
        }
        dataTableAction(columns);

        $('#send-batch').click(function () {
            if (confirm("确定要批量发送吗?")) {
                var date = $('.send').first().attr('date');
                $(location).attr('href', "{{ route('daily-detail.review.send', ['user_id' => 'all']) }}" + '&date=' + date);
            }
        })
    });
</script>
@endpush