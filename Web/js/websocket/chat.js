var ws = {};
var client_id = 0;
var userlist = {};
var GET = getRequest();
var face_count = 19;

$(document).ready(function () {

    if(!webim.server){
        return;
    }
	if (window.WebSocket || window.MozWebSocket)
    {
        ws = new ReconnectingWebSocket(webim.server,null,{maxReconnectAttempts:10,timeoutInterval:3000});
    }
    //使用flash websocket
    else if (webim.flash_websocket)
    {
        WEB_SOCKET_SWF_LOCATION = "/static/websocket/WebSocketMain.swf";
        $.getScript("/static/websocket/swfobject.js", function () {
                ws = new WebSocket(webim.server);
        });
    }
    listenEvent();

});

function listenEvent() {
    /**
     * 连接建立时触发
     */
    ws.onopen = function (e) {
        //连接成功
        console.log("connect webim server success.");
        sendMsg('/index/index/index','login', 'text');
    };

    //有消息到来时触发
    ws.onmessage = function (e) {
        console.log(e);
        var message = JSON.parse(e.data);
        var code = message.code;
        if (code == '0000') {

        }else{
            //layer.msg(message.msg)
        }
    };

    /**
     * 连接关闭事件
     */
    ws.onclose = function (e) {
        //layer.msg("连接已断开，请刷新页面重新登录。");
    };

    /**
     * 异常事件
     */
    ws.onerror = function (e) {
        //layer.msg("服务器[" + webim.server +"]: 拒绝了连接. 请检查服务器是否启动.");
        console.log("onerror: " + e.data);
    };
}


function xssFilter(val) {
    val = val.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\x22/g, '&quot;').replace(/\x27/g, '&#39;');
    return val;
}

function parseXss(val) {
    for (var i = 1; i < 20; i++) {
        val = val.replace('#' + i + '#', '<img src="/static/img/face/' + i + '.gif" />');
    }
    val = val.replace('&amp;', '&');
    return val;
}

function getRequest() {
    var url = location.search; // 获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            var decodeParam = decodeURIComponent(strs[i]);
            var param = decodeParam.split("=");
            theRequest[param[0]] = param[1];
        }

    }
    return theRequest;
}

function sendMsg(url,content, type,get,post) {
    var msg = {};
        if (typeof content == "string") {
            content = content.replace(" ", "&nbsp;");
        }
        if (!content) {
            return false;
        }
        msg.url = url;
        msg.data = content;
        msg.type = type;
        msg.get = get||{};
        msg.post = post||{};
        ws.send(JSON.stringify(msg));
}
