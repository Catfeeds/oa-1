@push('scripts')
<script>
    $(function () {
        $('#card_id').blur(function () {
            var card = $(this).val().trim().replace(/\s/g, "");
            $('.error_card b').remove();
            // 1 "验证通过!", 0 //校验不通过
            var format = /^(([1][1-5])|([2][1-3])|([3][1-7])|([4][1-6])|([5][0-4])|([6][1-5])|([7][1])|([8][1-2]))\d{4}(([1][9]\d{2})|([2]\d{3}))(([0][1-9])|([1][0-2]))(([0][1-9])|([1-2][0-9])|([3][0-1]))\d{3}[0-9xX]$/;
            //号码规则校验
            if(!format.test(card)){
                console.log('身份证号码不合规');
                $('#card_id').prop('value', '');
                $("<b style='color: red'>身份证号码不合规</b>").appendTo(".error_card");
                return false;
            }
            //区位码校验
            //出生年月日校验   前正则限制起始年份为1900;
            var year = card.substr(6, 4),//身份证年
                month = card.substr(10, 2),//身份证月
                date = card.substr(12, 2),//身份证日
                time = Date.parse(month+'-'+date+'-'+year),//身份证日期时间戳date
                now_time = Date.parse(new Date()),//当前时间戳
                dates = (new Date(year,month,0)).getDate();//身份证当月天数
            if(time > now_time || date > dates){
                console.log('出生日期不合规');
                $('#card_id').prop('value', '');
                $("<b style='color: red'>出生日期不合规</b>").appendTo(".error_card");
                return false;
            }
            //校验码判断
            var c = new Array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);   //系数
            var b = new Array('1','0','X','9','8','7','6','5','4','3','2');  //校验码对照表
            var id_array = card.split("");
            var sum = 0;
            for(var k=0;k<17;k++){
                sum+=parseInt(id_array[k])*parseInt(c[k]);
            }
            if(id_array[17].toUpperCase() != b[sum%11].toUpperCase()){
                console.log('身份证校验码不合规');
                $('#card_id').prop('value', '');
                $("<b style='color: red'>身份证校验码不合规</b>").appendTo(".error_card");
                return false;
            }
            console.log('身份证校验通过');
            $("<b style='color: green'>身份证校验通过</b>").appendTo(".error_card");
            return true;
        });
    });
</script>
@endpush
