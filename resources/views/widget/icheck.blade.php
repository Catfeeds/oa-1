@push('css')
<link href="{{ asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<script>
    $(function () {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });
</script>
@endpush