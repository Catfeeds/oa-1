@push('scripts')
<script src="{{ asset('js/plugins/bootbox/bootbox.min.js') }}"></script>
<script>
    $(function () {
        $('.confirmation').on('click', function (event) {
            event.preventDefault();
            var href = $(this).is('a') ? $(this).attr('href') : $(this).parent('a').attr('href');
            var message = $(this).attr('data-confirm');

            bootbox.confirm({
                size: "small",
                message: message,
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result && href) {
                        //include the href duplication link here?;
                        window.location = href;
                    }
                }
            });
        });
    });
</script>
@endpush