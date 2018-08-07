{{-- http://www.bootcss.com/p/bootstrap-datetimepicker/ --}}
@push('css')

<link href="{{ asset('css/plugins/datetimepicker/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">

@endpush

@push('scripts')

<script src="{{ asset('js/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/plugins/datetimepicker/locales/bootstrap-datetimepicker.' . Lang::locale() . '.js') }}"></script>
<script>
    $(function() {
        $('.date').datetimepicker({
            language: "{{ Lang::locale() }}",
            format: 'yyyy-mm-dd',
            weekStart: 1,
            autoclose: 1,
            startView: 2,
            minView: 2,
            todayHighlight: 1,
            todayBtn: 1,
        });

        $('.date_time').datetimepicker({
            language: "{{ Lang::locale() }}",
            format: 'yyyy-mm-dd hh:ii:ss',
            weekStart: 1,
            autoclose: 1,
            startView: 2,
            todayHighlight: 1,
            todayBtn: 1,
        });
        $('.date_month').datetimepicker({
            language: "{{ Lang::locale() }}",
            format: 'yyyy-mm',
            autoclose: 1,
            startView: 3,
            minView: 3,
        });
    });
</script>

@endpush
