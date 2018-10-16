@push('css')
<link rel='stylesheet' type='text/css' href="{{ asset('fullcalendar/fullcalendar.css') }}"/>
<style type="text/css">
    #calendar_{{ $date }} {
        width: 700px;
    }
    .fc-content > span {
        height:19px; line-height:19px;
    }
    .fc-content {
        height: 19px;width: 75px;
    }
    .fc-event-container > a {
        width: 75px;margin: 0 auto;
    }
    .fc-event-container {
        text-align: center;
    }
</style>
@endpush

<div id="calendar_{{ $date }}"></div>

@push('scripts')
<script type='text/javascript' src={{ asset('fullcalendar/lib/moment.min.js') }}></script>
<script type='text/javascript' src={{ asset('fullcalendar/fullcalendar.js') }}></script>
<script type="text/javascript" src={{ asset('fullcalendar/locale/zh-cn.js') }}></script>

<script type="text/javascript">
    var selectDate = [];
    for (var i = 1; i <= 7; i ++) {
        selectDate[i] = [];
    }

    $("[data-toggle='popover']").popover();
    $(function() {
        $('#calendar_{{ $date }}').fullCalendar({
            header: {
                @if(!$date)
                    left: 'prev,next',
                    right: 'today',
                @else
                    left: '',
                    right: '',
                @endif
                center: 'title'
            },
            dayClick: function(date, jsEvent, view) {
                var day = date.format();
                var week = date.format('d') == 0 ? 7 : date.format('d');

                if ($.inArray(day, selectDate[week]) !== -1) {
                    selectDate[week].splice( $.inArray(day, selectDate[week]), 1 );
                    $(this).css('background-color', '');
                }else {
                    selectDate[week].push(day);
                    $(this).css('background-color', '#e4edf5');
                }
            },
            height: 550,
            defaultDate: '{{ $date }}',
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
                                content:n.content,
                                id: 'event_'+i,
                                data_id: n.data_id
                            });

                        });
                        callback(events);
                    }
                });
            },
            eventClick: function (event) {
                /*alert(event.data_id);*/
                $(location).attr('href', '{{ url('admin/sys/calendar/edit') }}' + '/' + event.data_id +
                    '{{ '?back='.urlencode(request()->fullUrl()) }}');
            },
            eventMouseover: function () {
                $(this).attr('color_', $(this).css('background-color'));
                $(this).css('background-color', '#636567');
            },
            eventMouseout: function () {
                $(this).css('background-color', $(this).attr('color_'));
            },
            eventRender: function (event, element) {
                element.attr('id', event.id).popover({
                    html:true,
                    title:event.title,
                    placement:'top',
                    container:'body',
                    content:event.content,
                    trigger:'hover'
                });
                element.attr('data-toggle', 'popover');
            }
        });
        $('#calendar_{{ $date }}').find('.fc-body').attr('title', '可选中日期进行配置').css('cursor', 'pointer');
    });
</script>
@endpush