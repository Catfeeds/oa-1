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
            if (opt.url === null || opt.selector === null) return false;

            this.click(function () {
                var selector_params;
                if ($.type(opt.selector) === 'string') {
                    selector_params = $(opt.selector).serializeObject();
                } else {
                    selector_params = opt.selector;
                }

                if ($.isEmptyObject(selector_params) && (opt.type == 3 || opt.type == 4 || opt.type == 5)) {
                    bootbox.alert({
                        size: "small",
                        message: opt.alert_message,
                    });
                    return false;
                }
                // 组织参数
                bootbox.confirm(opt.alert_confirm, function (result) {
                    if (result) {
                        switch (opt.type) {
                            case '2':
                                bootbox.prompt({
                                    title: "操作",
                                    inputType: 'text',
                                    callback: function (reason) {
                                        if (reason != null) {
                                            var data = {
                                                reason: reason
                                            };
                                            data = $.extend(selector_params, data);
                                            window.location = opt.url + '&' + $.param(data);
                                        }
                                    }
                                });
                                break;
                            case '3':
                                var html = '<div class="form-group" style="height: 30px">' +
                                    '<label for="platform_id" class="col-sm-2 control-label">开票号</label>' +
                                    '<div class="col-sm-6">' +
                                    '<input type="text" name="billing_num" value="" class="form-control" id="billing_num">' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="form-group"  style="height: 30px">' +
                                    '<label for="platform_id" class="col-sm-2 control-label">开票时间</label>' +
                                    '<div class="col-sm-6">' +
                                    '<input class="bootbox-input bootbox-input-date form-control" id="billing_time" name="billing_time" autocomplete="off" type="date">' +
                                    '</div>' +
                                    '</div>';
                                bootbox.dialog({
                                    title: "键值编辑",
                                    message: html,
                                    buttons: {
                                        "success": {
                                            "label": "<i class='icon-ok'></i> 提交",
                                            "className": "btn-sm btn-success",
                                            "callback": function () {
                                                var billing_num = $("#billing_num").val();
                                                var billing_time = $("#billing_time").val();
                                                var data = {
                                                    billing_num: billing_num,
                                                    billing_time: billing_time,
                                                };
                                                data = $.extend(selector_params, data);
                                                window.location = opt.url + '?' + $.param(data);
                                            }
                                        },
                                        "cancel": {
                                            "label": "<i class='icon-info'></i> 取消",
                                            "className": "btn-sm btn-danger",
                                            "callback": function () {
                                            }
                                        }
                                    }
                                });
                                break;
                            case '4':
                                bootbox.prompt({
                                    title: "操作",
                                    inputType: 'date',
                                    callback: function (payback_time) {
                                        if (payback_time) {
                                            var data = {
                                                payback_time: payback_time
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
                                if (opt.url.indexOf("?") != -1) {
                                    window.location = opt.url + '&' + $.param(data);
                                } else {
                                    window.location = opt.url + '?' + $.param(data);
                                }
                                break;
                        }
                    } else {
                        window.location.reload();
                    }
                });

            });
        }
    });
})(jQuery);
