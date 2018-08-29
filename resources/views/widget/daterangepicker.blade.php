@push('css')

    <link href="{{ asset('css/plugins/daterangepicker/daterangepicker-bs3.css').'?t='.time() }}" rel="stylesheet">

@endpush

@push('scripts')

    <script src="{{ asset('js/plugins/daterangepicker/moment.js') }}"></script>
    <script src="{{ asset('js/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        // 日期范围
        var startDate = $('#scope-start-date').val();
        var endDate = $('#scope-end-date').val();

        $('#date-range').daterangepicker({
            "locale": {
                "direction": "ltr",
                "format": "YYYY-MM-DD",
                "separator": " {{ trans('app.到') }} ",
                "applyLabel": "{{ trans('app.确认') }}",
                "cancelLabel": "{{ trans('app.取消') }}",
                "fromLabel": "{{ trans('app.从') }}",
                "toLabel": "{{ trans('app.到') }}",
                "customRangeLabel": "{{ trans('app.自定义') }}",
                "daysOfWeek": [
                    "{{ trans('app.日') }}",
                    "{{ trans('app.一') }}",
                    "{{ trans('app.二') }}",
                    "{{ trans('app.三') }}",
                    "{{ trans('app.四') }}",
                    "{{ trans('app.五') }}",
                    "{{ trans('app.六') }}",
                ],
                "monthNames": [
                    "{{ trans('app.一月') }}",
                    "{{ trans('app.二月') }}",
                    "{{ trans('app.三月') }}",
                    "{{ trans('app.四月') }}",
                    "{{ trans('app.五月') }}",
                    "{{ trans('app.六月') }}",
                    "{{ trans('app.七月') }}",
                    "{{ trans('app.八月') }}",
                    "{{ trans('app.九月') }}",
                    "{{ trans('app.十月') }}",
                    "{{ trans('app.十一月') }}",
                    "{{ trans('app.十二月') }}",
                ],
                "firstDay": 1
            },
            "ranges": {
                '{{ trans('app.今日') }}': [moment(), moment()],
                '{{ trans('app.昨日') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ trans('app.最近7天') }}': [moment().subtract(6, 'days'), moment()],
                '{{ trans('app.最近30天') }}': [moment().subtract(29, 'days'), moment()],
                '{{ trans('app.当月') }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ trans('app.上月') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true,
            "startDate": startDate,
            "endDate": endDate
        }, function (start, end, label) {
            $('#scope-start-date').val(start.format('YYYY-MM-DD'));
            $('#scope-end-date').val(end.format('YYYY-MM-DD'));
            //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });


        $('#date-range-timestamp').daterangepicker({
            timePicker: true, //显示时间
            timePicker24Hour: true, //时间制
            timePickerSeconds: true, //时间显示到秒
            "startDate": startDate,
            "endDate": endDate,
            "locale": {
                "direction": "ltr",
                "format": "YYYY-MM-DD HH:mm:ss",
                "separator": " {{ trans('app.到') }} ",
                "applyLabel": "{{ trans('app.确认') }}",
                "cancelLabel": "{{ trans('app.取消') }}",
                "fromLabel": "{{ trans('app.从') }}",
                "toLabel": "{{ trans('app.到') }}",
                "customRangeLabel": "{{ trans('app.自定义') }}",
                "daysOfWeek": [
                    "{{ trans('app.日') }}",
                    "{{ trans('app.一') }}",
                    "{{ trans('app.二') }}",
                    "{{ trans('app.三') }}",
                    "{{ trans('app.四') }}",
                    "{{ trans('app.五') }}",
                    "{{ trans('app.六') }}",
                ],
                "monthNames": [
                    "{{ trans('app.一月') }}",
                    "{{ trans('app.二月') }}",
                    "{{ trans('app.三月') }}",
                    "{{ trans('app.四月') }}",
                    "{{ trans('app.五月') }}",
                    "{{ trans('app.六月') }}",
                    "{{ trans('app.七月') }}",
                    "{{ trans('app.八月') }}",
                    "{{ trans('app.九月') }}",
                    "{{ trans('app.十月') }}",
                    "{{ trans('app.十一月') }}",
                    "{{ trans('app.十二月') }}",
                ],
                "firstDay": 1
            },
            "ranges": {
                '{{ trans('app.今日') }}': [moment().startOf('day'), moment().endOf('day')],
                '{{ trans('app.昨日') }}': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                '{{ trans('app.最近3天') }}': [moment().subtract(2, 'days').startOf('day'), moment().endOf('day')],
            },
            "alwaysShowCalendars": true,
        }, function (start, end, label) {
            $('#scope-start-date').val(start.format('YYYY-MM-DD HH:mm:ss'));
            $('#scope-end-date').val(end.format('YYYY-MM-DD HH:mm:ss'));
            //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
        @if (isset($id))
        $('#{!! $id !!}-range-timestamp').daterangepicker({
            timePicker: true, //显示时间
            timePicker24Hour: true, //时间制
            timePickerSeconds: true, //时间显示到秒
            "startDate": startDate,
            "endDate": endDate,
            "locale": {
                "direction": "ltr",
                "format": "YYYY-MM-DD HH:mm:ss",
                "separator": " {{ trans('app.到') }} ",
                "applyLabel": "{{ trans('app.确认') }}",
                "cancelLabel": "{{ trans('app.取消') }}",
                "fromLabel": "{{ trans('app.从') }}",
                "toLabel": "{{ trans('app.到') }}",
                "customRangeLabel": "{{ trans('app.自定义') }}",
                "daysOfWeek": [
                    "{{ trans('app.日') }}",
                    "{{ trans('app.一') }}",
                    "{{ trans('app.二') }}",
                    "{{ trans('app.三') }}",
                    "{{ trans('app.四') }}",
                    "{{ trans('app.五') }}",
                    "{{ trans('app.六') }}",
                ],
                "monthNames": [
                    "{{ trans('app.一月') }}",
                    "{{ trans('app.二月') }}",
                    "{{ trans('app.三月') }}",
                    "{{ trans('app.四月') }}",
                    "{{ trans('app.五月') }}",
                    "{{ trans('app.六月') }}",
                    "{{ trans('app.七月') }}",
                    "{{ trans('app.八月') }}",
                    "{{ trans('app.九月') }}",
                    "{{ trans('app.十月') }}",
                    "{{ trans('app.十一月') }}",
                    "{{ trans('app.十二月') }}",
                ],
                "firstDay": 1
            },
            "ranges": {
                '{{ trans('app.今日') }}': [moment().startOf('day'), moment().endOf('day')],
                '{{ trans('app.昨日') }}': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                '{{ trans('app.最近3天') }}': [moment().subtract(2, 'days').startOf('day'), moment().endOf('day')],
            },
            "alwaysShowCalendars": true,
        }, function (start, end, label) {
            $('#{!! $id !!}start_time').val(start.format('YYYY-MM-DD HH:mm:ss'));
            $('#{!! $id !!}end_time').val(end.format('YYYY-MM-DD HH:mm:ss'));
            //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
        @endif
    </script>

@endpush
