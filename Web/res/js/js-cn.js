function LangSelect(a) {
    switch (a) {
    case "en":
        window.location.href = global_Domain + "Login.aspx";
        break;
    case "cn":
        window.location.href = global_Domain + "cn/Login.aspx";
        break;
    default:
        web_tips("即将开启...")
    }
}
function SelectLang(a, b) {
    var c;
    $.cookie(global_LangCookie, "", {
        expires: -1
    }),
    $.cookie(global_LangCookie, a, {
        expires: 7
    }),
    c = $.cookie(global_LangCookie),
    CheckLang(c),
    parent.location.href = b + rand()
}
function numPages(a, b) {
    return Math.ceil(a / b)
}
function CheckURL_Injection() {
    var a = window.location.search.toLowerCase(),
    b = a.substring(a.indexOf("=") + 1);
    re = /select|update|delete|truncate|join|union|exec|insert|drop|script|'|"|;|>|<|%/i,
    re.test(b) && (window.location.href = a.replace(b, ""))
}
function onDeleteMonitorPlayer(a, b, c) {
    swal({
        title: "确定要删除?",
        text: b,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DeleteMonitorPlayer",
                userName: b,
                CurDateTime: rand()
            },
            success: function(a) {
                a.success ? (swal(a.msg, "", "success"), c()) : swal(a.msg, "", "error")
            }
        })
    })
}
function whenMouseOver(a, b) {
    var c = 10,
    d = 10;
    $(a).mouseover(function(a) {
        this.myTitle = this.title,
        this.title = "";
        var e = "<div id='tooltip'>" + b + "</div>";
        $("body").append(e),
        $("#tooltip").css({
            top: a.pageY + d + "px",
            left: a.pageX + c + "px"
        }).show("fast")
    }).mouseout(function() {
        $("#tooltip").remove()
    }).mousemove(function(a) {
        $("#tooltip").css({
            top: a.pageY + d + "px",
            left: a.pageX + c + "px"
        })
    })
}
function onFormatBankID(a) {
    $(a).keyup(function() {
        var b = $(a).val().replace(/\s/g, " ").replace(/(\d{4})(?=\d)/g, "$1 - ");
        $(a).val(b)
    })
}
function getAccount(c) {
var bb= "";
	$.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
			async: false,
            data: {
                action: "GetAccount",
                ssid: c,
                rand: rand()
            },
            success: function(aa) {
			bb = aa.account;
            },
            beforeSend: function() {

            },
            complete: function() {

            }
	})
	return bb;
}

function onOrderSetScore(a, b, c, d, e) {
    var f = b,
    g = c,
    h = d;
    swal({
        title: "确定操作?",
        text: "订单号：" + e,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
		g = getAccount(g);
        $.ajax({
            url: "../ashx/platOrder/paymentOrder.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "onOrderSetScore",
                ID: f,
                user: g,
                costNum: h,
                rand: rand()
            },
            success: function(b) {
                b.success ? ($(a).parents("tr").find(".bg_flag").removeClass().addClass("badge bg-green").text("成功"), swal("操作成功.", "", "success"), $(a).hide()) : (swal(b.msg, "", "info"), $(a).removeAttr("disabled").text("进分补单"))
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}

function onIgnoreApply(a, b, c) {
    var d = b,
    e = c;
    swal({
        title: "确定忽略?",
        text: e,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/log/log.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "IgnoreApplySongData",
                id: d,
                rand: rand()
            },
            success: function(b) {
                b.success ? ($(a).parents("tr").remove(), swal("操作成功.", "", "success")) : (swal(b.msg, "", "info"), $(a).removeAttr("disabled").text("忽略这笔"))
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onIgnoreApply2(a, b) {
    var c = a,
    d = b;
    swal({
        title: "确定忽略?",
        text: d,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/log/log.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "IgnoreApplySongData",
                id: c,
                rand: rand()
            },
            success: function(a) {
                a.success ? swal("操作成功.", "", "success") : swal(a.msg, "", "info")
            }
        })
    })
}
function onIgnore(a, b, c) {
    var d = b,
    e = c;
    swal({
        title: "确定忽略?",
        text: e,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/log/log.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "IgnoreRegSongData",
                id: d,
                rand: rand()
            },
            success: function(b) {
                b.success ? ($(a).parents("tr").remove(), swal("操作成功.", "", "success")) : (swal(b.msg, "", "info"), $(a).removeAttr("disabled").text("忽略这笔"))
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onApplyScore2(a, b) {
    var bool = false;
    var c = a,
    d = b;
    swal({
        title: "确认通过系统代付?",
        text: d,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        if (bool) return;
        bool = true;
        $.ajax({
            url: "../ashx/log/log.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DoClickApplyScore",
                id: c,
                rand: rand()
            },
            success: function(a) {
                if(a.success)
                {
                    parent.window.getDataList(0);
                    swal({
                        title: "操作成功",
                        type: "success"
                    },function(isConfirm){
                        parent.window.layer.close(parent.window.openedLayDialog);
                    });
                } else {
                    swal(a.msg, "", "info");
                }
            }
        })
    })
}
function onCashPass(a, b) {
    var c = a,
    d = b;
    swal({
        title: "确定通过系统打款?",
        text: d,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/platOrder/accountCashOrder.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DoCashPass",
                DataID: c,
                User: d,
                rand: rand()
            },
            success: function(a) {
                a.success ? (alertOk('cash_detail')) : swal(a.msg, "", "info")

            }
        })
    })
}

function onRenGongCaoZuo(a, b) {
    console.log(parent.window.openedLayDialog);
    var c = a,
    d = b;
    swal({
        title: "确定人工打款?",
        text: d,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        alertDoing();
        $.ajax({
            url: "../ashx/platOrder/accountCashOrder.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DoRenGongPass",
                DataID: c,
                User: d,
                rand: rand()
            },
            success: function(a) {
              if(a.success)
              {
                  parent.window.getDataList(0);
                  swal({
                      title: "操作成功",
                      type: "success"
                  },function(isConfirm){
                      parent.window.layer.close(parent.window.openedLayDialog);
                  });
              } else {
                  swal(a.msg, "", "info");
              }
            }
        })
    })

}
function alertOk(closeId){

       swal({
           title: "操作成功",
           text: "操作成功",
           timer: 2000,
           showConfirmButton: false
       });
       if(closeId){
           setTimeout(function(){
               parent.layer.close();
               parent.art.dialog({id:closeId}).close()
           },2000)
       }
}


function alertDoing(){
    swal({
        title: "正在处理,请稍后...",
        text: "正在处理,请耐心等候，不要重复操作...",
        timer: 60000,
        showConfirmButton: false
    });
}

function onCancelCashOrder(a, b) {
    var c = a,
    d = b;
    swal({
        title: "确定取消订单?",
        text: d,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        alertDoing();
        $.ajax({
            url: "../ashx/platOrder/accountCashOrder.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DoCancelCashOrder",
                DataID: c,
                User: d,
                rand: rand()
            },
            success: function(a) {
                a.success ? (alertOk('cash_detail')) : swal(a.msg, "", "info")
            }
        })
    })
}
function onDisCashOrder(a, b) {
    var c = a,
    d = b;
    swal({
        title: "确定禁用?",
        text: d,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        alertDoing();
        $.ajax({
            url: "../ashx/platOrder/accountCashOrder.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DoDisCashOrder",
                DataID: c,
                User: d,
                rand: rand()
            },
            success: function(a) {
                a.success ? (alertOk('cash_detail')) : swal(a.msg, "", "info")
            }
        })
    })
}
function onApplyData_Detail(a, b, c) {
    var d = "onClick_ApplyDetail.aspx?sid=" + c + "&id=" + b;
    var setting = {
        title: c + " 的数据详情",
        url:d,
        wh:['95%','70%'],
        fn:null,
    };
    layOpen(setting);
}
function onCashData_Detail(a, b, c) {
    var d = "onClick_CashDetail.aspx?sid=" + c + "&id=" + b;
    var setting = {
        title: c + " 的数据详情",
        url:d,
        wh:['50%','50%'],
        pc:['1000px','700px'],
        fn:null
    };
     window.openedLay =  layOpen(setting);
}
function onRcvReport(a, b) {
    var c = "onClick_RcvReportDetail.aspx?s=" + a + "&e=" + b;
    var setting = {
        title: " 收款统计",
        url:d,
        wh:['95%','70%'],
        fn:null,
    };
    layOpen(setting);
}

function onIP(a, b) {

    var c = "onClick_IPDetail.aspx?acc=" + a + "&e=" + b;
    var setting = {
        title: " IP统计",
        url:c,
        wh:['95%','70%'],
        fn:null,
    };
    layOpen(setting);
}
function onOpenBalance(a, b) {
    var c = "onClick_Balance.aspx?sid=" + b;
    var setting = {
        title: b + " 结算数据",
        url:c,
        wh:['95%','70%'],
        fn:null,
    };
    layOpen(setting);

}function onRegLargess(a, b) {
    var c = "onClick_RegLargess.aspx?sid=" + b;
    var setting = {
        title: b + " 注册送",
        url: c,
        wh: ['95%', '70%'],
        fn: null,
    };
    layOpen(setting);
}
function onCashData_ViewDetail(a, b, c) {
    var d = "onClick_ViewCashDetail.aspx?sid=" + c + "&id=" + b;
    var setting = {
        title: c + " 的银行详情",
        url: d,
        wh: ['95%', '70%'],
        pc: ['500px', '180px'],
        fn: null,
    };
    layOpen(setting);
}
function onRegScore(a, b, c) {
    var bool = false;
    var d = b,
    e = c;
    swal({
        title: "确定通过?",
        text: e,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        if (bool) return;
        bool = true;
        $.ajax({
            url: "../ashx/log/log.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DoClickRegScore",
                id: d,
                rand: rand()
            },
            success: function(b) {
                b.success ? ($(a).parents("tr").remove(), swal("操作成功.", "", "success")) : (swal(b.msg, "", "info"), $(a).removeAttr("disabled").text("点击通过"))
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onPassTransfer(a, b, c, d, e) {
    var f = b,
    g = c;
    swal({
        title: "确定" + e + "?",
        text: "编号：" + b + " 账号：" + g,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/platOrder/transferOrder.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "OnPassTransferMoney",
                id: f,
                flag: d,
                rand: rand()
            },
            success: function(b) {
                b.success ? ($(a).parents("tr").remove(), swal("操作成功.", "", "success")) : (swal(b.msg, "", "info"), $(a).removeAttr("disabled").text("通过"))
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onDelPlayerSetScore(a, b, c) {
    var d = b,
    e = c;
    swal({
        title: "确定已处理?",
        text: e,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/log/log.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DelPlayerSetScore",
                id: d,
                rand: rand()
            },
            success: function(b) {
                b.success ? ($(a).parents("tr").remove(), swal("操作成功.", "", "success")) : swal("操作失败,请重试.", "", "info")
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function CheckLang(a) {
    a && "undefined" != typeof a && 0 != a && 0 !== a.length && $.ajax({
        url: global_Domain + "ashx/lang/Lang.ashx",
        type: "post",
        dataType: "json",
        data: {
            action: "SetLang_Cookie",
            langValue: a,
            CurDateTime: rand()
        },
        success: function() {}
    })
}
function getCurrDate() {
    var a = myDate.format("yyyy-MM-dd");
    return a
}
function getFirstDay() {
    return myDate.setDate(1),
    new XDate(myDate).toString("yyyy-MM-dd")
}
function getLastDay() {
    var a = new Date(myDate);
    return a.setMonth(myDate.getMonth() + 1),
    a.setDate(0),
    new XDate(a).toString("yyyy-MM-dd")
}
function GetQueryString(a) {
    var b = new RegExp("(^|&)" + a + "=([^&]*)(&|$)", "i"),
    c = window.location.search.substr(1).match(b);
    return null != c ? unescape(c[2]) : null
}
function web_dialog(a, b, c) {
    art.dialog({
        icon: a,
        title: b,
        time: 10,
        content: c,
        lock: !0,
        fixed: !0,
        drag: !1
    })
}
function web_dialog(a) {
    swal(a)
}
function web_tips(a) {
    swal(a)
}
function web_open() {
    var a = arguments[0],
    b = arguments[1] ? arguments[1] : "游戏记录";
    var setting = {
        title: b,
        url: a,
        wh: ['95%', '70%'],
        pc: ['540px', '540px'],
        fn: null,
    };
    layOpen(setting);
}
function checkUserName(a) {
    var b = /^([a-zA-Z0-9]{1}[a-zA-Z0-9_-]{6,16})+$/;
    return b.test(a)
}
function checkTgh(a) {
    var b = /^([a-zA-Z0-9]{1}[a-zA-Z0-9_-]{5})+$/;
    return b.test(a)
}
function checkPassWord(a) {
    var c, d, b = /^(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[a-z])[0-9A-Za-z!)-_]{6,15}$/;
    return b.test(a) ? (d = /^((?!123)(?!234)(?!345)(?!456)(?!567)(?!678)(?!789).)*?$/, c = d.test(a)) : c = b.test(a),
    c
}
function checkPassWord2(a) {
    var c, d, b = /^(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[a-z])[0-9A-Za-z!)-_]{6,15}$/;
    return b.test(a) ? (d = /^((?!123)(?!234)(?!345)(?!456)(?!567)(?!678)(?!789).)*?$/, c = d.test(a)) : c = b.test(a),
    c
}
function checkNum(a) {
    var b = /^[+-]?\d*\.?\d{1,2}$/;
    return b.test(a)
}
function checkAddNum(a) {
    var b = /^[+]?\d*\.?\d{1,2}$/;
    return b.test(a)
}
function checkPhone(a) {
    var b = /^1[3|4|5|7|8]\d{9}$/;
    return b.test(a)
}
function checkNum_FFINT(a) {
    var b = /^\d+$/;
    return b.test(a)
}
function checkNum_ZFINT(a) {
    var b = /^(^-?\d+$)$/;
    return b.test(a)
}
function onDeleteM(a, b, c,d) {
    swal({
        title: "确定要"+c+"?",
        text: b,
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DelM",
                userName: b,
                CurDateTime: rand()
            },
            success: function(a) {
                a.success ? (swal(a.msg, "", "success"), d()) : swal(a.msg, "", "error")
            }
        })
    })
}
function onDeleteNoticeGameClient(a, b, c,type) {
    swal({
        title: "确定要删除?",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../com/com_new_ad.aspx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "noticeDelete",
                id: b,
                type: type,
                CurDateTime: rand()
            },
            success: function(a) {
                a.success ? (swal(a.msg, "", "success"), c()) : swal(a.msg, "", "error")
            }
        })
    })
}


function onDeleteEmailGameClient(a, b, c) {
    swal({
            title: "确定要删除?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            closeOnConfirm: !1
        },
        function() {
            $.ajax({
                url: "../ashx/system/system.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "DelClientEmail",
                    id: b,
                    CurDateTime: rand()
                },
                success: function(a) {
                    a.success ? (swal(a.msg, "", "success"), c()) : swal(a.msg, "", "error")
                }
            })
        })
}




function onDeleteNotice(a, b, c) {
    swal({
        title: "确定要删除?",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/system/system.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "DelNotice",
                id: b,
                CurDateTime: rand()
            },
            success: function(a) {
                a.success ? (swal(a.msg, "", "success"), c()) : swal(a.msg, "", "error")
            }
        })
    })
}
function onChangeUserPwd(a, b) {
    swal({
        title: "修改密码",
        text: b,
        type: "input",
        showCancelButton: !0,
        closeOnConfirm: !1,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        animation: "slide-from-top",
        inputPlaceholder: "密码最少6位,最多15位."
    },
    function(a) {
        return a === !1 ? !1 : "" === a || a.length < 1 ? (swal.showInputError("密码不能为空."), !1) : (checkPassWord2(a) ? $.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            data: {
                action: "setPlatUserPwd",
                Newpwd: a,
                userName: b,
                CurDateTime: rand()
            },
            success: function(a) {
                a.success ? swal(a.msg, "", "success") : (a.ERROR_TYPE == global_Error500 && (parent.location.href = "../PageError/500.html"), global_NoLicense == a.msg ? parent.location.href = "../Login.aspx": swal(a.msg, "", "error"))
            }
        }) : swal.showInputError("密码最少6位,必须是数字和字母组合.字母须有大小写,不能有连续3位以上的数字."), void 0)
    })
}


function onChangeAgentSafecode(a, b) {
    swal({
            title: "修改安全码",
            text: b,
            type: "input",
            showCancelButton: !0,
            closeOnConfirm: !1,
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            animation: "slide-from-top",
            inputPlaceholder: "安全码为四位"
        },
        function(a) {
            return a === !1 ? !1 : "" === a || a.length < 1 ? (swal.showInputError("安全码不能为空."), !1) : (a.length==4 ? $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                data: {
                    action: "setPlatAgentSafecode",
                    Newpwd: a,
                    userName: b,
                    CurDateTime: rand()
                },
                success: function(a) {
                    a.success ? swal(a.msg, "", "success") : (a.ERROR_TYPE == global_Error500 && (parent.location.href = "../PageError/500.html"), global_NoLicense == a.msg ? parent.location.href = "../Login.aspx": swal(a.msg, "", "error"))
                }
            }) : swal.showInputError("安全码为四位"), void 0)
        })
}

function onHuodongsong(a, b) {
    swal({
            title: "活动送",
            text: b,
            type: "input",
            showCancelButton: !0,
            closeOnConfirm: !1,
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            animation: "slide-from-top",
            inputPlaceholder: "输入赠送金额（最多5000的上限）."
        },
        function(a) {

             if(a === !1 ? !1 : "" === a || a.length < 1) {
                 swal.showInputError("请填写赠送金额.")
                 return;
             }
                if(parseInt(a>5000)||a<1){
                    swal.showInputError("金额1-5000 !")
                    return;
                }

                $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                data: {
                    action: "OnHuodongsong",
                    money: a,
                    uid: b,
                    CurDateTime: rand()
                },
                    beforeSend:function(){
                        alertDoing();
                    },
                success: function(a) {
                    a.success ? swal(a.msg, "", "success") : (a.ERROR_TYPE == global_Error500 && (parent.location.href = "../PageError/500.html"), global_NoLicense == a.msg ? parent.location.href = "../Login.aspx": swal(a.msg, "", "error"))
                }
            })
        })
}


function onChangePwd(a, b) {
    swal({
        title: "修改密码",
        text: b,
        type: "input",
        showCancelButton: !0,
        closeOnConfirm: !1,
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        animation: "slide-from-top",
        inputPlaceholder: ""
    },
    function(a) {
        return a === !1 ? !1 : "" === a || a.length < 1 ? (swal.showInputError("密码不能为空."), !1) : (checkPassWord(a) ? $.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            data: {
                action: "setManagerPwd",
                Newpwd: a,
                userName: b,
                CurDateTime: rand()
            },
            success: function(a) {
                a.success ? swal(a.msg, "", "success") : (a.ERROR_TYPE == global_Error500 && (parent.location.href = "../PageError/500.html"), global_NoLicense == a.msg ? parent.location.href = "../Login.aspx": swal(a.msg, "", "error"))
            }
        }) : swal.showInputError("最少6位且必须数字和字母组合,字母必须有大小写."), void 0)
    })
}
function CheckLogin() {
    jQuery.ajax({
        url: "../ashx/common/CheckLogin.ashx",
        type: "post",
        dataType: "json",
        data: {
            action: "CheckLogin",
            CurDateTime: rand()
        },
        success: function(a) {
           // a.success || (parent.location.href = global_jump_login)
        }
    })
}
function LogOut() {
    swal({
        title: "确定退出吗?",
        text: "",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "LogOut",
                rand: rand()
            },
            success: function(b) {
                parent.location.href = global_jump_login
            },
            beforeSend: function() {
                //$(a).attr("disabled", "disabled").text("正在操作")
            },
            error: function (xhr) {
                parent.location.href = global_jump_login
            },
            complete: function(xhr) {
                if(xhr.status==302||xhr.status==301){
                    parent.location.href = global_jump_login
                }
            }
        })
    })
}
function onQuiteGame(a, b) {
    swal({
        title: "确定执行?",
        text: b + " 踢出游戏",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "quiteGame",
                user: b,
                rand: rand()
            },
            success: function(b) {
                b.success ? ($(a).text("踢出游戏"), swal("操作成功.", "", "success")) : ($(a).text("踢出游戏"), global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("玩家不在游戏.", "", "info"))
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}

function onCleanMoney(a, b) {
    swal({
            title: "确定余额清零?",
            text: b + " 余额清零",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#00a65a",
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            closeOnConfirm: !1
        },
        function() {
            $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "cleanMoney",
                    user: b,
                    rand: rand()
                },
                success: function(b) {
                    b.success ? ($(a).text("余额清零"), swal("操作成功.", "", "success")) : ($(a).text("余额清零"), global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal(b.msg, "", "info"))
                },
                beforeSend: function() {
                    $(a).attr("disabled", "disabled").text("正在操作")
                },
                complete: function() {
                    $(a).removeAttr("disabled")
                }
            })
        })
}




function onQuiteGame1(a, b) {
    art.dialog({
        title: b + " quite game",
        content: "Are you sure?",
        time: 10,
        ok: function() {
            $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "quiteGame",
                    user: b,
                    rand: rand()
                },
                success: function(b) {
                    b.success ? ($(a).text("quit game"), web_tips(b.msg)) : ($(a).text("quit game"), global_NoLicense == b.msg ? parent.location.href = global_jump_login: web_dialog(b.msg))
                },
                beforeSend: function() {
                    $(a).attr("disabled", "disabled").text("Waiting")
                },
                complete: function() {
                    $(a).removeAttr("disabled")
                }
            })
        },
        okVal: "OK",
        cancelVal: "Cancel",
        lock: !0,
        fixed: !1,
        cancel: !0
    })
}
function onChat(a, b, c) {
    window.location.href = "chart_user.aspx?sid=" + b + "&action=" + c
}
function onOpenCloseSetting(a, b) {
    var c = "onClick_UserSetting.aspx?sid=" + b;
    var setting = {
        title:b + " 功能设定",
        url:c,
        wh:['95%','70%'],
        pc:['420px','220px'],
        fn:null,
    };
    layOpen(setting);
}
function onRegSongSeting(a, b) {
    var c = "onClick_RegSetting.aspx?sid=" + b;
    var setting = {
        title:b + " 注册送设定",
        url:c,
        wh:['95%','45%'],
        pc:['500px','330px'],
        fn:null,
    };
    layOpen(setting);
}
function onNameList(a, b, cc) {
    var c = "onClick_NameListSetting.aspx?sid=" + b+"&gameid="+cc;
    var setting = {
        title:b + " 黑白名单设定",
        url:c,
        wh:['95%','70%'],
        fn:null,
    };
    layOpen(setting);
}



function onSMSSongSeting(a, b) {
    var c = "onClick_SendMessageSetting.aspx?sid=" + b;
    var setting = {
        title:b + " 功能设定",
        url:c,
        wh:['95%','45%'],
        pc:['400px','300px'],
        fn:null,
    };
    layOpen(setting);
}
function onSendSms(a, b) {
    var c = "onClick_SendSms.html?sid=" + b;
    var setting = {
        title:b + " 短信送设定",
        url:c,
        wh:['80%','30%'],
        pc:['360px','150px'],
        fn:null,
    };
    layOpen(setting);
}
function onAddAgentSetting(a, b) {
    var c = "onClick_AddAgentSetting.aspx?sid=" + b;
    var setting = {
        title:b + " 功能设定",
        url:c,
        wh:['95%','45%'],
        pc:['400px','260px'],
        fn:null,
    };
    layOpen(setting);
}


function onAdminIpWhiteListSetting(a, b) {
    var c = "Onclick_adminwhitelist.aspx?sid=" + b;
    var setting = {
        title:b + " 注册送设定",
        url:c,
        wh:['95%','70%'],
        fn:null,
    };
    layOpen(setting);
}



function onAgentPayMoney(a, b) {
    var c = "onClick_AgentPayMoney.aspx?sid=" + b;
    var setting = {
        title: "为 " + b + " 分账",
        url:c,
        wh:['95%','70%'],
        fn:null,
    };
    layOpen(setting);

}
function onTotal(a, b) {
    var c = "onClick_Total.aspx?sid=" + b;
    var setting = {
        title: b + " 帐号统计",
        url:c,
        wh:['95%','40%'],
        pc:['300px','200px'],
        fn:null,
    };
    layOpen(setting);
}
function onTotal_TGH(a, b) {
    var c = "onClick_TotalTGH.aspx?sid=" + b;
    var setting = {
        title: b + " 帐号统计",
        url:c,
        wh:['95%','70%'],
        fn:null,
    };
    layOpen(setting);
}
function onSetLimitMoney(a, b) {
    window.location.href = "../gameSetting/LiveGame_SettingLimitMoney.aspx?sid=" + b
}
function onReport(a, b, c) {

	$.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "GetAccount",
                ssid: b,
                rand: rand()
            },
            success: function(aa) {
			    var f=document.createElement('form');
				f.style.display='none';
				f.action='report_Account.aspx?idd='+b;
				f.method='post';
				f.innerHTML='<input type="hidden" name="sid" value="'+aa.onDisable+'"/> <input type="hidden" name="action" value="'+c+'"/> ';
				document.body.appendChild(f);
				f.submit();


            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
}
function onEdit(a, b) {
   // window.location.href = "editUser.aspx?sid=" + b

	$.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "GetAccount",
                ssid: b,
                rand: rand()
            },
            success: function(aa) {
               window.location.href = "editUser.aspx?sid=" +aa.account
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
}
function onScoreLog(a, b) {
    window.location.href = "log_setScore.aspx?sid=" + b
}
function onScoreLogForSub(a, b) {
    window.location.href = "report_SetScore.aspx?sid=" + b
}
function onGameLog(a, b) {
    window.location.href = "log_gamelog.aspx?sid=" + b
}
function onBonusLog(a, b) {
    window.location.href = "log_bonusLog.aspx?sid=" + b
}

function onRegSong(a, b,c) {

    //
    // layer.confirm(b+' 注册送', {
    //     icon:3,
    //     title:"注册送确认",
    //     btn: ['赠送', '忽略']
    // }, function(index, layero){
    //     //按钮【按钮一】的回调
    //     var jindex = layer.load(1);
    //     $(a).attr("disabled", "disabled").text("稍后");
    //     $.post('../../com/com_RegScore.aspx',{action:'send',type:1,usrid:c},function (e) {
    //         layer.close(jindex);
    //         if(e.resp_code=='0000'){
    //             layer.close(index);
    //             layer.msg(e.msg)
    //             $(a).text("已赠送")
    //         }else{
    //             layer.msg(e.msg)
    //             $(a).removeAttr("disabled").text("注册送")
    //         }
    //
    //     });
    //
    // }, function(index){
    //     //按钮【按钮二】的回调
    //     var jindex = layer.load(1);
    //     $(a).attr("disabled", "disabled").text("稍后");
    //     $.post('../../com/com_RegScore.aspx',{action:'send',type:2,usrid:c},function (e) {
    //         layer.close(jindex);
    //         if(e.resp_code=='0000'){
    //             layer.close(index);
    //             layer.msg(e.msg)
    //             $(a).text("已忽略")
    //         }else{
    //             layer.msg(e.msg)
    //             $(a).removeAttr("disabled").text("注册送")
    //         }
    //
    //     });
    // });

    var c = "onClick_RegSend.aspx?sid=" + b;
    var setting = {
        title: b + " 注册送",
        url:c,
        wh:['80%','80%'],
        fn:null,
    };
    layOpen(setting);
}


function onScore(a, b, c) {
    //window.open("setGameScore.html?sid=" + b + "&action=" + c)
	$.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "GetAccount",
                ssid: b,
                rand: rand()
            },
            success: function(aa) {
               window.location.href = "com_setscore_index.aspx?sid=" + aa.account + "&action=" + c
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })

}
function onEnableAll(a, b, c) {
    var d = $(a).text();
    swal({
        title: "确定执行?",
        text: "",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "EnableAll",
                agent: b,
                type: c,
                rand: rand()
            },
            success: function(b) {
                b.success ? ($(a).text(d), 1 == b.type && $("#tb_server tbody tr").find("button:eq(6)").removeClass().addClass("btn btn-danger btn-xs").text("解禁"), 2 == b.type && $("#tb_server tbody tr").find("button:eq(6)").removeClass().addClass("btn btn-warning btn-xs").text("禁用"), swal("操作成功.", "", "success")) : ($(a).text(d), global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("操作失败,请重试.", "", "info"))
            },
            beforeSend: function() {
                $(a).attr("disabled", "disabled").text("正在操作")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onEnableAll_1(a, b, c) {
    var d = $(a).text();
    art.dialog({
        title: "Msg",
        content: "Are you sure?",
        time: 10,
        ok: function() {
            $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "EnableAll",
                    agent: b,
                    type: c,
                    rand: rand()
                },
                success: function(b) {
                    b.success ? ($(a).text(d), 1 == b.type && $("#tb_server tbody tr").find("button:eq(6)").removeClass().addClass("btn btn-danger btn-xs").text("enable"), 2 == b.type && $("#tb_server tbody tr").find("button:eq(6)").removeClass().addClass("btn btn-warning btn-xs").text("disable"), web_tips(b.msg)) : ($(a).text(d), global_NoLicense == b.msg ? parent.location.href = global_jump_login: web_dialog(b.msg))
                },
                beforeSend: function() {
                    $(a).attr("disabled", "disabled").text("Waiting")
                },
                complete: function() {
                    $(a).removeAttr("disabled")
                }
            })
        },
        okVal: "OK",
        cancelVal: "Cancel",
        lock: !0,
        fixed: !1,
        cancel: !0
    })
}
function onOpenRechargeInfo(a, b) {
    $(a).text(),
    swal({
        title: "确定执行操作?",
        text: "",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/system/system.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "OpenCloseRecharge",
                id: b,
                rand: rand()
            },
            success: function(b) {
                b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-default btn-xs").text("已停用"), 0 == b.type && $(a).removeClass().addClass("btn btn-success btn-xs").text("使用中"), swal("操作成功.", "", "success")) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal(b.msg, "", "info")
            },
            beforeSend: function() {
                web_tips("操作中,请稍后 ....."),
                $(a).attr("disabled", "disabled")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onOpenCardInfo(a, b, c) {
    $(a).text(),
    swal({
        title: "确定执行操作?",
        text: "",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/system/system.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "OpenORCloseReciveInfo",
                id: b,
                mID: c,
                rand: rand()
            },
            success: function(b) {
                b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-default btn-xs").text("已停用"), 0 == b.type && $(a).removeClass().addClass("btn btn-success btn-xs").text("使用中"), swal("操作成功.", "", "success")) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("操作失败,请重试.", "", "info")
            },
            beforeSend: function() {
                web_tips("操作中,请稍后 ....."),
                $(a).attr("disabled", "disabled")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onDisable(a, b) {
    var c = $(a).text();
    swal({
        title: "确定执行?",
        text: c + " " + b,
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/account/account.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "disable",
                user: b,
                rand: rand()
            },
            success: function(b) {
                b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-danger btn-xs").text("解禁"), 2 == b.type && $(a).removeClass().addClass("btn btn-warning btn-xs").text("禁用"), swal("操作成功.", "", "success")) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("操作失败,请重试.", "", "info")
            },
            beforeSend: function() {
                web_tips("操作中,请稍后 ....."),
                $(a).attr("disabled", "disabled").text("稍后")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onDisableP(a, b) {
    var c = $(a).text();
    swal({
        title: "确定执行?",
        text: c + " " + b,
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/editField/editField.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "Disable_rank_virtual",
                id: b,
                rand: rand()
            },
            success: function(b) {
                b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-danger btn-xs").text("解禁"), 2 == b.type && $(a).removeClass().addClass("btn btn-warning btn-xs").text("禁用"), swal("操作成功.", "", "success")) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("操作失败,请重试.", "", "info")
            },
            beforeSend: function() {
                web_tips("操作中,请稍后 ....."),
                $(a).attr("disabled", "disabled").text("稍后")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onDisableC(a, b) {
    var c = $(a).text();
    swal({
        title: "确定执行?",
        text: c + " " + b,
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#00a65a",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: !1
    },
    function() {
        $.ajax({
            url: "../ashx/editField/editField.ashx",
            type: "post",
            dataType: "json",
            cache: !1,
            data: {
                action: "Disable_custom",
                id: b,
                rand: rand()
            },
            success: function(b) {
                b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-danger btn-xs").text("解禁"), 2 == b.type && $(a).removeClass().addClass("btn btn-warning btn-xs").text("禁用"), swal("操作成功.", "", "success")) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("操作失败,请重试.", "", "info")
            },
            beforeSend: function() {
                web_tips("操作中,请稍后 ....."),
                $(a).attr("disabled", "disabled").text("稍后")
            },
            complete: function() {
                $(a).removeAttr("disabled")
            }
        })
    })
}
function onDisable_1(a, b) {
    var c = $(a).text();
    art.dialog({
        title: c + " " + b,
        content: "Are you sure?",
        time: 10,
        ok: function() {
            $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "disable",
                    user: b,
                    rand: rand()
                },
                success: function(b) {
                    b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-danger btn-xs").text("enable"), 2 == b.type && $(a).removeClass().addClass("btn btn-warning btn-xs").text("disable"), web_tips(b.msg)) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: web_dialog(b.msg)
                },
                beforeSend: function() {
                    $(a).attr("disabled", "disabled").text("Waiting")
                },
                complete: function() {
                    $(a).removeAttr("disabled")
                }
            })
        },
        okVal: "OK",
        cancelVal: "Cancel",
        lock: !0,
        fixed: !1,
        cancel: !0
    })
}

function onAdminIpDel(a, b,f) {
    var c = $(a).text();
    swal({
            title: "确定执行?",
            text: c + " " + b,
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#00a65a",
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            closeOnConfirm: !1
        },
        function() {
            $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "delAdminIp",
                    id: b,
                    rand: rand()
                },
                success: function(b) {
                    b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-danger btn-xs").text("解禁"), 2 == b.type && $(a).removeClass().addClass("btn btn-warning btn-xs").text("禁用"), swal("操作成功.", "", "success"),f()) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("操作失败,请重试.", "", "info")
                },
                beforeSend: function() {
                    web_tips("操作中,请稍后 ....."),
                        $(a).attr("disabled", "disabled").text("稍后")
                },
                complete: function() {
                    $(a).removeAttr("disabled")
                }
            })
        })
}


function onControlDel(a, b,f) {
    var c = $(a).text();
    swal({
            title: "确定执行?",
            text: c + " " + b,
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#00a65a",
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            closeOnConfirm: !1
        },
        function() {
            $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "delSuperControl",
                    id: b,
                    rand: rand()
                },
                success: function(b) {
                    b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-danger btn-xs").text("解禁"), 2 == b.type && $(a).removeClass().addClass("btn btn-warning btn-xs").text("禁用"), swal("操作成功.", "", "success"),f()) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("操作失败,请重试.", "", "info")
                },
                beforeSend: function() {
                    web_tips("操作中,请稍后 ....."),
                        $(a).attr("disabled", "disabled").text("稍后")
                },
                complete: function() {
                    $(a).removeAttr("disabled")
                }
            })
        })
}

function onSupeuControlItemDel(a, b,f) {
    var c = $(a).text();
    swal({
            title: "确定执行?",
            text: c + " " + b,
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#00a65a",
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            closeOnConfirm: !1
        },
        function() {
            $.ajax({
                url: "../ashx/account/account.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "delSuperItemControl",
                    id: b,
                    rand: rand()
                },
                success: function(b) {
                    b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-danger btn-xs").text("解禁"), 2 == b.type && $(a).removeClass().addClass("btn btn-warning btn-xs").text("禁用"), swal("操作成功.", "", "success"),window.location.reload()) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal(b.msg, "", "info")
                },
                beforeSend: function() {
                    web_tips("操作中,请稍后 ....."),
                        $(a).attr("disabled", "disabled").text("稍后")
                },
                complete: function() {
                    $(a).removeAttr("disabled")
                }
            })
        })
}


function onDisablePaymentChannel(a, b,t,f) {
    var c = $(a).text();
    swal({
            title: "确定执行?",
            text: c + " " + b,
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#00a65a",
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            closeOnConfirm: !1
        },
        function() {
            $.ajax({
                url: "../ashx/editField/editField.ashx",
                type: "post",
                dataType: "json",
                cache: !1,
                data: {
                    action: "changepaychannelstatus" ,
                    id: b,
                    type: t,
                    rand: rand()
                },
                success: function(b) {
                    b.success ? (1 == b.type && $(a).removeClass().addClass("btn btn-gray btn-xs").text("开启通道"), 2 == b.type && $(a).removeClass().addClass("btn btn-success  btn-xs").text("关闭通道"), swal("操作成功.", "", "success"),f()) : global_NoLicense == b.msg ? parent.location.href = global_jump_login: swal("操作失败,请重试.", "", "info")
                },
                beforeSend: function() {
                    web_tips("操作中,请稍后 ....."),
                        $(a).attr("disabled", "disabled").text("稍后")
                },
                complete: function() {
                    $(a).removeAttr("disabled")
                }
            })
        })
}

function onSupuerControlSeting(a, b) {
    var c = "Onclick_supercotrolsetting.aspx?sid=" + b;
    window.location = c;
}


function ClearAllSpace(a) {
    var b = a.replace(/[ ]/g, "");
    return b
}
function rnd() {
    return rnd.seed = (9301 * rnd.seed + 49297) % 233280,
    rnd.seed / 233280
}
function rand() {
    return Math.ceil(1e7 * rnd())
}
function drawChart_Line(a, b) {
    var c = 400,
    d = a,
    e = new JSChart("graph", "line");
    e.setDataArray(d),
    e.setTitle("GameChart"),
    e.setTitleColor("#383838"),
    e.setTitleFontSize(14),
    e.setAxisNameX(""),
    e.setAxisNameY(""),
    e.setAxisColor("#38a4d9"),
    e.setGridColor("#38a4d9"),
    e.setAxisValuesColor("#38a4d9"),
    e.setAxisPaddingLeft(60),
    e.setAxisPaddingRight(20),
    e.setAxisPaddingTop(60),
    e.setAxisPaddingBottom(20),
    e.setAxisValuesNumberX(b),
    e.setAxisValuesNumberY(10),
    e.setTextPaddingBottom(12),
    e.setTextPaddingLeft(200),
    e.setGraphExtend(!1),
    e.setLineWidth(2),
    e.setLineColor("#C71112"),
    e.setAxisValuesDecimals(0),
    b > 1 && 10 >= b && e.setSize(500, c),
    b > 10 && 20 >= b && e.setSize(800, c),
    b > 20 && 31 >= b && e.setSize(1e3, c),
    e.draw()
}
function drawChart_Bar() {
    var a = 600,
    b = arguments[0],
    c = arguments[1],
    d = arguments[2] ? arguments[2] : "GameBet Total ReportChart",
    e = arguments[3] ? arguments[3] : !1,
    f = new JSChart("graph", "bar");
    f.setDataArray(b),
    f.setTitle(d),
    f.setTitleColor("#383838"),
    f.setTitleFontSize(14),
    f.setBarColor("#FF7F00"),
    f.setBarOpacity(.8),
    f.setBarBorderColor("#D9EDF7"),
    f.setBarValues(e),
    f.setAxisNameFontSize(14),
    f.setAxisWidth(1),
    f.setTextPaddingLeft(2),
    f.setBarBorderWidth(0),
    f.setAxisColor("#777E81"),
    f.setAxisValuesColor("#777E81"),
    f.setAxisValuesNumberY(15),
    f.setAxisNameX(""),
    f.setAxisNameY(""),
    f.setAxisPaddingBottom(20),
    f.setTextPaddingBottom(12),
    f.setAxisPaddingRight(20),
    f.setAxisValuesNumberX(c),
    f.setAxisPaddingLeft(80),
    f.setAxisValuesDecimals(0),
    c > 1 && 50 >= c && f.setSize(1350, a),
    c > 50 && 70 >= c && f.setSize(1600, a),
    c > 70 && 90 >= c && f.setSize(2200, a),
    c > 90 && f.setSize(3e3, a),
    f.draw()
}
var myDate, global_NoLicense = "noData",
global_LangCookie = "Lang_Cookie",
global_Domain = "http://test.com/",
global_Error500 = "ERROR_500",
global_jump_login = "../Login.aspx",
global_e_p_500 = "../PageError/500.html",
browser = {
    versions: function() {
        var a = navigator.userAgent;
        return navigator.appVersion,
        {
            trident: a.indexOf("Trident") > -1,
            presto: a.indexOf("Presto") > -1,
            webKit: a.indexOf("AppleWebKit") > -1,
            gecko: a.indexOf("Gecko") > -1 && -1 == a.indexOf("KHTML"),
            mobile: !!a.match(/AppleWebKit.*Mobile.*/) || !!a.match(/AppleWebKit/),
            ios: !!a.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
            android: a.indexOf("Android") > -1 || a.indexOf("Linux") > -1,
            iPhone: a.indexOf("iPhone") > -1 || a.indexOf("Mac") > -1,
            iPad: a.indexOf("iPad") > -1,
            webApp: -1 == a.indexOf("Safari")
        }
    } (),
    language: (navigator.browserLanguage || navigator.language).toLowerCase()
};
Date.prototype.format = function(a) {
    var c, b = {
        "M+": this.getMonth() + 1,
        "d+": this.getDate(),
        "h+": this.getHours(),
        "m+": this.getMinutes(),
        "s+": this.getSeconds(),
        "q+": Math.floor((this.getMonth() + 3) / 3),
        S: this.getMilliseconds()
    };
    /(y+)/.test(a) && (a = a.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length)));
    for (c in b) new RegExp("(" + c + ")").test(a) && (a = a.replace(RegExp.$1, 1 == RegExp.$1.length ? b[c] : ("00" + b[c]).substr(("" + b[c]).length)));
    return a
},
myDate = new Date,
rnd.today = new Date,
rnd.seed = rnd.today.getTime();

function setpage() {
    if ($(window).width() < 458) {
        return ['95%', '60%'];
    } else {
        return ['60%', '40%'];
    }
}

function  layOpen(setting) {
    var  ar =  ['50%', '50%'];
    console.log($(window).width());
    if ($(window).width() >458) {
        if(setting.pc)
        {
            ar = setting.pc;
        }
        else{
            ar =  ['40%', '40%'];
        }
    }
    else{
        ar = setting.wh;
    }
    console.log(ar);
    layer.open({
        type: 2,
        area: ar,
        content: setting.url,
        title: setting.title,
        success: function(layero, index){
            window.openedLayDialog = index ;
        }
    });
}