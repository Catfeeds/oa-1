@extends('attendance.side-nav')

@section('title', $title)

@push('css')
    <link rel='stylesheet' type='text/css' href="{{ asset('fullcalendar/fullcalendar.css') }}"/>
@endpush

@section('content')
    <div class="col-xs-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>假期天数</h5>
            </div>
            <div class="ibox-content">

                {!! Form::open(['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
                {{--分割线--}}
                <div>
                    @if(!empty($data))
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>假期名</th>
                            <th>假期详情</th>
                            <th>剩余假期天数</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr>
                                <td>{{ $d['holiday'] }}</td>
                                <td><pre style="width: 15em; height: 3em" >{{ $d['memo'] }}</pre></td>
                                <td>{{ $d['num'] . '天'}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <h1>当前未找到假期</h1>
                    @endif
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

    <div class="col-xs-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>工作日历</h5>
            </div>
            <div class="ibox-content">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type='text/javascript' src={{ asset('fullcalendar/lib/moment.min.js') }}></script>
<script type='text/javascript' src={{ asset('fullcalendar/fullcalendar.js') }}></script>
<script type="text/javascript" src={{ asset('fullcalendar/locale/zh-cn.js') }}></script>


<script type="text/javascript">
    $("[data-toggle='popover']").popover();
    $(function() {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'today'
            },
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
                                id: 'event_'+i
                            });

                        });
                        callback(events);
                    }
                });
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
    });
</script>
@endpush