@push('scripts')
<script>
    function showImg(outerdiv, innerdiv, bigimg, _this) {
        var src = _this.attr("src");
        $(bigimg).attr("src", src);

        $("<img/>").attr("src", src).on('load',function(){
            var windowW = $(window).width();
            var windowH = $(window).height();
            var realWidth = this.width;
            var realHeight = this.height;
            var imgWidth, imgHeight;
            var scale = 0.8;

            if(realHeight>windowH*scale) {
                imgHeight = windowH*scale;
                imgWidth = imgHeight/realHeight*realWidth;
                if(imgWidth>windowW*scale) {
                    imgWidth = windowW*scale;
                }
            } else if(realWidth>windowW*scale) {
                imgWidth = windowW*scale;
                imgHeight = imgWidth/realWidth*realHeight;
            } else {
                imgWidth = realWidth;
                imgHeight = realHeight;
            }
            $(bigimg).css("width",imgWidth);

            var w = (windowW-imgWidth)/2;
            var h = (windowH-imgHeight)/2;
            $(innerdiv).css({"top":h, "left":w});
            $(outerdiv).fadeIn("fast");
        });

        $(outerdiv).click(function(){
            $(this).fadeOut("fast");
        });
    }

    $(function() {
        function readURL(input, $class_id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $($class_id).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#select-thumb-file").change(function(){
            readURL(this, '#show_thumb_image');
        });

        $("#select-associate-file").change(function(){
            readURL(this, '#show_associate_image');
        });

        $("#select-mobile-header-file").change(function(){
            readURL(this, '#show_mobile_header_image');
        });
    });

</script>
@endpush