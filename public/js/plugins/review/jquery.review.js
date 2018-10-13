/**
 * Created by weiming on 2018/8/15.
 *
 * 批量审核操作
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
            if (opt.url === null || opt.selector === null) return false;

            this.click(function () {
                var selector_params;
                if ($.type(opt.selector) === 'string') {
                    selector_params = $(opt.selector).serializeObject();
                } else {
                    selector_params = opt.selector;
                }

                if ($.isEmptyObject(selector_params)) {
                    bootbox.alert({
                        size: "small",
                        message: opt.alert_message,
                    });
                    return false;
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
                                        if (reason) {
                                            var data = {
                                                reason: reason
                                            };
                                            data = $.extend(selector_params, data);
                                            window.location = opt.url + '?' + $.param(data);
                                        }

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
                    }
                });

            });
        },
        batchAll: function (opt) {
            var defaults = {
                url: null,
                alert_confirm: null
            };
            opt = $.extend(defaults, opt);
            if (opt.url === null) return false;

            this.click(function () {
                // 组织参数
                bootbox.confirm(opt.alert_confirm, function (result) {
                    if (result) {
                        window.location = opt.url;
                    }
                });

            });

        }

    });

})(jQuery);
