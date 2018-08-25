{{-- http://abpetkov.github.io/switchery --}}
@push('css')
<link href="{{ asset('css/plugins/switchery/switchery.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/switchery/switchery.js') }}"></script>
<script>
    $(function () {
        var elem = document.querySelector('.js-switch');
        var switchery = new Switchery(elem, {
            color: '#1AB394'
            , secondaryColor: '#dfdfdf'
            , jackColor: '#fff'
            , jackSecondaryColor: null
            , className: 'switchery'
            , disabled: false
            , disabledOpacity: 0.5
            , speed: '0.1s'
            , size: 'default'
        });

    });
</script>
@endpush