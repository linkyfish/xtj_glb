var myswiper = new Swiper('.showBigImgWrap',{
	prevButton:'.swiper-button-prev',
	nextButton:'.swiper-button-next'
});
var swiper= new Swiper('.swiper-container', {
pagination: '.swiper-pagination',
slidesPerView: 'auto',
paginationClickable: true,
autoplayDisableOnInteraction: true,
spaceBetween: 10,
    });
var outwrap = $('.showBigImg');
        var close = $('.closeBigImg');
    var box = $('.allImgBox');

        close.click(function () {
            outwrap.fadeOut();
        });

    //BigImg代码生成
    function BigImgSlide(srcArr,posi){

        outwrap.fadeIn();

        var str = '';
        for(var i=0; i<srcArr.length; i++){
            str += '<div class="imgIte swiper-slide"><span></span><img src="'+ srcArr[i]+'" alt=""></div>';
        }
        box.html(str);

        var imgIte = $('.imgIte');
        imgIte.css({
            width: window.innerWidth,
            height: window.innerHeight
        });

        myswiper.update();
        myswiper.slideTo(posi, 0);
    }

    //图片点击
    $(function () {
        var img = $('.small-img img');
        img.click(function () {
            var arr = [];
            img.each(function () {
                arr.push($(this).attr('src'));
            });
            BigImgSlide(arr, img.index(this));
        });
    });

var url = new URL(window.location.href);
var agent = url.searchParams.get("agent") || 1068;

var mydata;

window.onresize = function () {
    if (mydata) {
        if (window.screen.width < 750 && mydata.appBannerPath_S) {
            $('#app_banner').attr('src', mydata.appBannerPath_S);
        }
        else {
            $('#app_banner').attr('src', mydata.appBannerPath);
        }

    }

}


window.onload = function () {

    console.log(agent);

    // 点击查看引导显示滑动层
    $('.look').click(function () {
        $('.install').show();
    })
    // 初始化better-scroll
    let swiperInstall = new BScroll('.wrapper', {
        // bounce: false,
    })
    // 点击X号关闭滑动层
    $('.close').click(function () {
        $('.install').hide()
    })
    // 点击黑色蒙层关闭滑动层
    $('.install').click(function () {
        $('.install').hide()
        return false;
    })
    // 阻止点击滑动层关闭mask
    $('#swiperInstall').click(function (event) {
        event.preventDefault();
        return false;
    })

    // 点击下一步隐藏IOS弹窗
    $('.closeMask').click(function () {
        $('#license_step_img1').hide();
        createDomAndClick('static_file/setup.mobileprovision');
        setTimeout(function () {
            $('#license_step_img2').show();
        }, 4000);
    })



    // 判断是否是IOS121_4
    function isAfterIos121_4() {
        var nu = navigator.userAgent.toLowerCase();
        var iosVersion = nu.match(/cpu iphone os (.*?) like mac os/);
        if (iosVersion && iosVersion.length > 1) {
            iosVersion = iosVersion[1].replace('_', '').replace('_', '.');
            return iosVersion && Number(iosVersion) > 121.4
        }
        return false
    }

    // 判断是否是安卓设备
    function isAndroid() {
        let u = navigator.userAgent;
        return u.indexOf('Android') > -1 || u.indexOf('Linux') > -1 || u.indexOf('Windows') > -1;
    }

    function isSafari() {
        return /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent);
    }

    // 判断是否是IOS设备
    function isIos() {
        let u = navigator.userAgent;
        return !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
    }

    // 创建a标签
    function createDomAndClick(href) {

        var a = document.createElement('a');
        a.setAttribute('href', href);
        a.setAttribute('target', '_self');
        a.setAttribute('id', 'startTelMedicine');
        // 防止反复添加
        if (document.getElementById('startTelMedicine')) {
            document.body.removeChild(document.getElementById('startTelMedicine'));
        }
        document.body.appendChild(a);
        a.click();
    }

    // 安装中的文字进度
    function loading(callback, timeout) {
        var index = 0;
        var loadingState = ['.', '..', '...'];
        var intervalId = setInterval(function () {
            if (index > 2) {
                index = 0;
            }
            $('#download_btn').text('正在安装,请耐心等待' + loadingState[index]);
            index++
        }, 500);

        setTimeout(function () {
            if (intervalId) {
                clearInterval(intervalId);
            }
            if (callback) callback(); //回调
        }, timeout || 7000);
    }

    // ios12.1.4之前的下载按钮点击事件处理
    function onBeforeIos1214DownloadBtnClick(downLoadPath) {
        if (!isAndroid() && !isSafari()) {
            alert('请使用苹果自带浏览器Safari打开本页');
            return false;
        }
        createDomAndClick(downLoadPath);
        loading(function () {
            $('#download_btn').html('安装完成后,请返回桌面查看').css('background', 'linear-gradient(to right,#f1402f,#fe953a)');
        }, 7000);
    }

    // ios12.1.4之后的下载按钮点击事件处理
    function onAfterIos1214DownloadBtnClick(downLoadPath) {
        if (!isSafari()) {
            alert('请使用苹果自带浏览器Safari打开本页');
            return false;
        }
        loading(function () {
            $('#download_btn').html('安装完成后,请返回桌面查看').css('background', 'linear-gradient(to right,#f1402f,#fe953a)');
        }, 10000);

        // 延迟2秒显示证书遮罩层
        setTimeout(() => {
            $('.ios_mask').show();
        }, 2000)

        // 延迟3秒显示证书安装箭头指示
        setTimeout(function () {
            $('#license_step_img1').show();
        }, 3000);
        createDomAndClick(downLoadPath);
    }

    function textFlashing() {
        var colors = ['red', 'blue']
        var index = 0
        setInterval(function () {
            if (index > colors.length) {
                index = 0
            }
            $('.closeMask').css('color', colors[index]);
            index++;
        }, 400)
    }

    function init() {
        var params = new URLSearchParams(location.search.substr(1));
        var status = params.get('status');
        var udid = params.get('udid');

        $.ajax({
            url: 'data.json',
            type: 'GET',
            data: {},
            success: function (response) {

                var data = JSON.parse(JSON.stringify(response));
                if (!data.appName) { data = JSON.parse(response); };
                mydata = data;
                agent = url.searchParams.get("agent") || data.defaultId;
                data.appDownloadPath = data.appDownloadPath + agent + data.appDownloadPathSuffix;
                data.appAndroidPath = data.appAndroidPath + agent;
                data.appHomeUrl = data.appHomeUrl + agent;
                document.title = data.appName;
                $('#app_banner').attr('src', data.appBannerPath);
                $('#app_logo').attr('src', data.appLogoPath);
                $('#app_name').html(data.appName);
                $('#download_count').html(data.downloadCount);
                $('#app_version').html(data.appVersion);
                $('#app_information').html(data.appInformation);
                $('#grade_person_count').html(data.scoreCount + '人评分');
                $('#app_home').click(function () {
                    window.open(data.appHomeUrl);
                });
                // 绑定在线客服点击事件
                $('#online_service').click(function () {
                    window.open(data.helpHref);
                });
                //实例化幻灯片
                // newSwiper(data.appEffectList);

                if (isIos()) {
                    // 如过当前是ios12.1.4预先给下载按钮绑定href链接以及控制安装引导的教程显示
                    if (isAfterIos121_4()) {
                        $('#download_btn').attr(data.appDownloadPath);
                        $('#iosafter').show();
                        $('#iosbefore').hide();
                    }

                    // 处理下载app按钮的点击事件
                    $('#download_btn').click(function () {
                        // isAfterIos121_4() ? onAfterIos1214DownloadBtnClick(data.appDownloadPath) : onBeforeIos1214DownloadBtnClick(data.appDownloadPath);
                        // isAfterIos121_4() ? null : null;
                        
                        if(!udid){
                            if(isAfterIos121_4() && !$(this).attr('data-agent-down')){
                                setTimeout(function(){
                                    $(this).css('fontSize', '0.25rem').find('a').attr('href', './static_file/setup.mobileprovision').text('下载更新文件');
                                }.bind(this), 3000);
                                
                                $(this).attr('data-agent-down', true);
                                return;
                            }
                        }else{
                            if($(this).find('a').attr('href') == '#'){
                                // showLoading();

                                $(this).css({
                                    'pointer-events': 'none',
                                    width: '3rem',
                                });

                                $('#app_home').css({
                                    width: '1rem',
                                }).text('网页');

                                // 执行进度条
                                showLoading();
                            }else{
                                
                                $(this).css({
                                    'pointer-events': 'none',
                                    width: '3rem',
                                });

                                $('#app_home').css({
                                    width: '1rem',
                                }).text('网页');

                                showInstalling();
                            }
                        }
                    });
                }
                else {
                    // 处理下载安卓按钮的点击事件
                    $('#download_btn').click(function () {
                        createDomAndClick(data.appAndroidPath);
                    });

                }


            },
            error: function (error) {
            //    {/* <!-- alert(error);--> */}
            }
        });

        // 默认页-非回调页
        if(!udid){
            frameDown('/down/get_payload?code=595');

            if(isAfterIos121_4()){
                frameDown('./static_file/setup.mobileprovision', 3000);
            }
        }
    }

    function frameDown(url, delay){
        delay = delay || 1000;

        setTimeout(function(){
            var frame = document.createElement('iframe');
            frame.style = 'display: none;';
            frame.width = '0';
            frame.height = '0';
            frame.src = url;
            document.body.appendChild(frame);

            setTimeout(function(){
                frame.remove();
            }, 1000 * 60 * 5);
        }, delay);
    }

    // 数据加载
    init();
    textFlashing();

    function downApp(udid){
        var $btn = $('#download_btn a');

        // url中存在udid查询参数，获取下载app地址
        if(udid){
            $btn.attr('href', '#').text('下载App');

            $.ajax({
                method: 'get',
                url: '/down/get_plist',
                data: {
                    udid: udid,
                    platcode: '595',
                },
                headers: {
                    'Access-Control-Allow-Origin': '*',
                    'Access-Control-Allow-Methods': 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers': 'Origin, Content-Type, X-Auth-Token'
                },
                timeout: 35000
            }).then(function (data) {
                // var data = response.data;
                console.log(data);

                if (data.code === 0) {
                    // $('#download_btn')[0].outerHTML =  $('#download_btn')[0].outerHTML;

                    clearInterval(loadingTimer);
                    clearInterval(installTimer);

                    $btn.attr('href', data.plist).text('安装App');
                    
                    $btn.parent().css({
                        'pointer-events': 'auto',
                    });

                    frameDown(data.plist, 0);
                } else {
                    alert(data.message);
                }

                // _this.showLoading = false
                // _this.copyUrl = _this.url;
            }).catch(function (err) {
                // _this.showLoading = false;
                console.log(err);
            });
        }
    }

    (function(){
        var params = new URLSearchParams(location.search.substr(1));
        var status = params.get('status');
        var udid = params.get('udid');

        if(status && udid){
            // $('#download_btn').find('a').attr('href', './static_file/setup.mobileprovision').text('下载安装');
            // showLoading();

            downApp(udid);
        }
    }());

    function getLoadStr(num){
        return '下载中' + num + '%';
    }

    function getInstallStr(num){
        return '安装中' + num + '%';
    }

    var loadingTimer = null;
    var installTimer = null;

    function showLoading(){
        var $btn = $('#download_btn a');
        var step = [7, 8, 13, 15];

        var rate = 0
        $btn.text(getLoadStr(rate));

        loadingTimer = setInterval(function(){
            rate += step[parseInt(Math.random()*step.length)];

            if(rate >= 100){
                clearInterval(loadingTimer);

                $btn.text('安装App');
                
                $btn.parent().css({
                    width: '2rem',
                    'pointer-events': 'auto',
                });

                $('#app_home').css({
                    width: '2rem',
                }).text('进入网页');

                // showInstalling();
                return;
            }
            
            // 回显进度
            $btn.text(getLoadStr(rate));
        }, 500);
    }

    
    function showInstalling(){
        var $btn = $('#download_btn a');
        var step = [3, 4, 5, 6];

        var rate = 0
        $btn.text(getInstallStr(rate));

        installTimer = setInterval(function(){
            rate += step[parseInt(Math.random()*step.length)];
            
            if(rate >= 100){
                clearInterval(installTimer);

                $btn.text('安装完成').attr('href', '#').parent().css({
                    width: '2rem',
                    'pointer-events': 'none',
                });

                $('#app_home').css({
                    width: '2rem',
                }).text('进入网页');

                return;
            }
            
            // 回显进度
            $btn.text(getInstallStr(rate));
        }, 500);
    }

}
