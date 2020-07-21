//注意第一个key是带有安卓下载的，务必放置第一个
var key = ["ase691"];
var u = navigator.userAgent;
var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
var rand = Math.floor(Math.random()*key.length);
var data = key[rand];
//判断安卓用第一个key
if (!isIOS) {
    data = key[0];
}
console.log(data);
var datas = OpenInstall.parseUrlParams(); // 获取当前URL参数
var openInstall = new OpenInstall({
    appKey : data,
    onready : function() {
        var m = this;
        //尝试拉起已安装的APP
        m.schemeWakeup();
    }
}, datas);
