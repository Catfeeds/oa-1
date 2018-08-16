/**
 *
 * @description: gm批量操作
 *
 * User: dengzhenhua Email:dengzhenhua@shiyuegame.com
 * Date: 2017/5/18
 * Time: 14:26
 *
 *
 */

(function ($) {

    $.fn.extend({
        batch: function (opt) {
            var defaults = {
                url: null,
                selector: null,
                type: null,
                alert_confirm: null,
                alert_message: '请选择一项要操作的数据',
            };

            opt = $.extend(defaults, opt);

            this.click(function () {
                var selector_params;
                if ($.type(opt.selector) === 'string') {
                    selector_params = $(opt.selector).serializeObject();
                } else {
                    selector_params = opt.selector;
                }

                // 组织参数
                bootbox.confirm(opt.alert_confirm, function (result) {
                    if (result) {
                        switch(opt.type)
                        {
                            case '2':
                                bootbox.prompt({
                                    title: "操作",
                                    inputType: 'text',
                                    callback: function (reason) {
                                        var data = {
                                            reason: reason
                                        };
                                        data = $.extend(selector_params, data);
                                        window.location = opt.url + '&' + $.param(data);
                                    }
                                });
                                break;
                            default:
                                var data = {};
                                data = $.extend(selector_params, data);
                                if(opt.url.indexOf("?")!=-1){
                                    window.location = opt.url + '&' + $.param(data);
                                }else{
                                    window.location = opt.url + '?' + $.param(data);
                                }
                                break;
                        }
                    }else{
                        window.location.reload();
                    }
                });

            });
        }
    });
})(jQuery);
