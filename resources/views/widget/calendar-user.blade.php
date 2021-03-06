@push('css')
<link rel='stylesheet' type='text/css' href="{{ asset('fullcalendar/fullcalendar.css') }}"/>
<style>
    .fc-ltr .fc-basic-view .fc-day-top .fc-day-number {
        font-size: 15px;
        padding: 1px 10px 0 10px;
    }
    .fc-day-cnDate, .fc-day-cnTerm {
        padding: 0 4px 0 0;
        font-size: xx-small;
    }
    .fc-toolbar.fc-header-toolbar {
        margin-bottom: 0;
    }
    .fc-center > h2 {
        font-size: medium;
    }
    /*td.fc-event-container {
        position: absolute;
    }*/
    .fc-event {
        position: absolute;
        bottom: 55%;
        margin-left: 0.5%;
    }
    .fc-ltr .fc-basic-view .fc-day-top .fc-day-number {
        text-align: center;
    }
    .fc-ltr .fc-basic-view .fc-day-top .fc-day-number {
        padding-right: 0;
        padding-left: 0;
    }
    .fc-day-cnDate {
        text-align: center;
    }
    .fc-day-cnDate, .fc-day-cnTerm {
        padding-right: 0;
    }
    .fc-day-cnTerm {
        text-align: center;
    }
    .fc-ltr .fc-basic-view .fc-day-top .fc-day-number {
        padding-top: 8%;
    }
</style>
@endpush

<div id="calendar"></div>

@push('scripts')
<script type='text/javascript' src={{ asset('fullcalendar/lib/moment.min.js') }}></script>
<script type='text/javascript' src={{ asset('fullcalendar/fullcalendar.js') }}></script>
<script type="text/javascript" src={{ asset('fullcalendar/locale/zh-cn.js') }}></script>

<script type="text/javascript">
    $("[data-toggle='popover']").popover();
    var selectDate = 1;
    var hoverDate = 0;

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
            height: 'auto',
            weekends: true,
            themeSystem: 'bootstrap3',
            events: function (start, end, a, callback) {
                var year = this.getDate().format('YYYY');
                var month = this.getDate().format('MM');
                $.ajax({
                    url: '{{ route('calIndex') }}',
                    data: {year: year, month: month},
                    dataType: 'json',
                    success: function (info) {
                        var events = [];
                        $.each(info, function (i, n) {
                            events.push({
                                title: n.event_char,
                                start: n.date,
                                color: n.color,
                                content: n.content,
                                id: 'event_' + i,
                                pop_title: n.event,
                                day: n.date
                            });

                        });
                        callback(events);
                        $('.fc-day-top:not(.fc-other-month)').mouseover(function () {
                            hoverDate = $(this).attr('data-date');
                            $('a.fc-event[day=' + hoverDate + ']').popover('show');
                        }).mouseleave(function () {
                            $('a.fc-event[day=' + hoverDate + ']').popover('hide');
                            hoverDate = 0;
                        });
                        //$('.fc-day-cnDate, .fc-day-cnTerm').css('height', $('div.fc-row').last().height() * 0.93);
                    }
                });
            },
            eventRender: function (event, element) {
                element.attr({'id': event.id, 'day': event.day}).popover({
                    html: true,
                    title: event.pop_title,
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
        });

        @if(isset($clickRead))
        $('#calendar').find('.fc-body').attr('title', '可选中日期查看').css('cursor', 'pointer');
        ajaxGetDayInfo('{{ date('Y-m-d', time()) }}');
        @endif
    });
</script>
@endpush