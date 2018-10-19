@push('css')
<link rel='stylesheet' type='text/css' href="{{ asset('fullcalendar/fullcalendar.css') }}"/>
@endpush

<div id="calendar"></div>

@push('scripts')
<script type='text/javascript' src={{ asset('fullcalendar/lib/moment.min.js') }}></script>
<script type='text/javascript' src={{ asset('fullcalendar/fullcalendar.js') }}></script>
<script type="text/javascript" src={{ asset('fullcalendar/locale/zh-cn.js') }}></script>

<script type="text/javascript">
    $("[data-toggle='popover']").popover();
    var selectDate = 1;

    function ajaxGetDayInfo(selectDate) {
        var selectDay = new Date(selectDate);
        var week = ['日', '一', '二', '三', '四', '五', '六'];
        $('#day-info-title').text(selectDay.getFullYear() + '年' +
            (selectDay.getMonth() + 1) + '月' + selectDay.getDate() + '日' + ' 星期' + week[selectDay.getDay()]);

        $('#day-info-subtitle').text('农历' + $('[data-date=' + selectDate + ']:last').children(':last').attr('lunar_date'));

        $.get('{{ route('user.getInfo') }}', {date: selectDate}, function (data) {
            var arr = JSON.parse(data);
            var str = '';
            $.each(arr['birthday'], function (i, e) {
                str = '\<h3\>[生日提醒]' + e.dept.dept + '-' + e.alias + '今天生日' + '\</h3\>' + str;
            });
            $.each(arr['entry'], function (i, e) {
                str = '\<h3\>[入职]' + e.dept.dept + '-' + e.alias + '今日入职' + '\</h3\>' + str;
            });
            $('#day-info-content').html(str);
        });
    }

    $(function () {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'today'
            },
            height: 500,
            weekends: true,
            themeSystem: 'bootstrap3',
            events: function (start, end, a, callback) {
                var month = this.getDate().format('MM');
                $.ajax({
                    url: '{{ route('calIndex') }}',
                    data: {month: month},
                    dataType: 'json',
                    success: function (info) {
                        var events = [];
                        $.each(info, function (i, n) {
                            events.push({
                                title: n.event,
                                start: n.date,
                                color: n.color,
                                content: n.content,
                                id: 'event_' + i
                            });

                        });
                        callback(events);
                    }
                });
            },
            eventRender: function (event, element) {
                element.attr('id', event.id).popover({
                    html: true,
                    title: event.title,
                    placement: 'top',
                    container: 'body',
                    content: event.content,
                    trigger: 'hover'
                });
                element.attr('data-toggle', 'popover');
            },
            @if(isset($clickRead))
            dayClick: function (date, jsEvent, view) {
                var day = date.format();

                if (selectDate == day) {
                    selectDate = 1;
                    $(this).css('background-color', '');
                } else {
                    $(this).css('background-color', '#e4edf5');
                    $('[data-date=' + selectDate + ']').first().css('background-color', '');
                    selectDate = day;
                }
                if (selectDate !== 1) {
                    ajaxGetDayInfo(selectDate);
                }
            },
            @endif
        }).find('.fc-body').attr('title', '可选中日期查看').css('cursor', 'pointer');

        @if(isset($clickRead))
        ajaxGetDayInfo('{{ date('Y-m-d', time()) }}');
        @endif
    });
</script>
@endpush