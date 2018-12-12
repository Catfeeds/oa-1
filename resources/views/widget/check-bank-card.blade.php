@push('scripts')
<script>
    $(function () {
        $('#bank_card').blur(function () {
            var card = $(this).val().trim().replace(/\s/g, "");
            $('.error_bank_card b').remove();
            var url = 'https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo='+card+'&cardBinCheck=true';
            $.get(url, function (result) {
                console.log(result);
                console.log(result.validated);
                console.log(result.bank);
                if(result.validated && result.bank == 'CMB') {
                    console.log(result);
                    $('#bank_card').append('1');
                    $("<b style='color: green'>银行卡校验通过</b>").appendTo(".error_bank_card");
                } else {
                    $('#bank_card').prop('value', '');
                    $("<b style='color: red'>银行卡校验失败</b>").appendTo(".error_bank_card");
                }
            });
        });
    });
</script>
@endpush