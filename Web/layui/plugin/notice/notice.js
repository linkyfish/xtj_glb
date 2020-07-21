var NOTICEINDEX = 0;
layui.define(['jquery'], function (exports) {
    var $ = layui.jquery;
    var noticeObj = {
        default: {
            type: "",
            calssName: "",
            title: "This is a notice",
            radius: "",
            icon: "",
            style: "",
            align: "",
            autoClose: true,
            time: 3000,
            click: true,
            end: null
        },
        init: function ($data) {
            var options = $.extend(this.default, $data);
            var _classN = "layui-bg-blue";
            switch (options.type) {
                case 'warm':
                    _classN = "layui-bg-yellow";
                    break;
                case 'danger':
                    _classN = "layui-bg-red";
                    break;
                case 'custom':
                    _classN = options.calssName;
                    break;
                default:

                    break;
            };

            var noticeObjHtml = '<div class="layui-container layui-anim layui-anim-up layui-notice"><div class="notice ' + _classN + ' has-radius">';

            var noticeClass = 'layui-notice-' + NOTICEINDEX;

            switch (options.align) {
                case 'left':
                    noticeObjHtml += '<p class="' + noticeClass + '">';
                    break;
                case 'right':
                    noticeObjHtml += '<p class="text-right ' + noticeClass + '">';
                    break;
                default:
                    noticeObjHtml += '<p class="text-center ' + noticeClass + '">';
                    break;
            };

            if (options.icon) {
                noticeObjHtml += '  <i class="layui-icon ' + options.icon + '"></i>';
            }
            noticeObjHtml += options.title;
            if (options.click) {
                noticeObjHtml +=
                    '<a href="javascript:;" class="pull-right"><i class="layui-icon layui-icon-close notice-close"></i></a>';
            }
            // 结束
            noticeObjHtml += '</p></div></div>';
            $("body").append(noticeObjHtml);

            if (options.autoClose) {
                window.setTimeout(function () {
                    $("." + noticeClass).parents(".layui-notice").addClass("layui-anim-fadeout").remove();
                }, options.time);
            }

            if (options.click) {
                $(".notice-close").click(function () {
                    $(this).parents(".layui-notice").addClass("layui-anim-fadeout").remove();
                });
            }

            this.end = function (callback) {
                callback();
            };
            NOTICEINDEX++;
        },
        close:function (){
            var noticeClass = 'layui-notice-' + NOTICEINDEX;
            $("." + noticeClass).parents(".layui-notice").addClass("layui-anim-fadeout").remove();
        },
        closeAll:function (){
            $(".notice-close").parents(".layui-notice").addClass("layui-anim-fadeout").remove();
        }
    };
    exports('notice', noticeObj);
});