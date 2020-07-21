

    if (device.ie && device.ie < 8) {
        layer.alert('本站最低支持ie8，您当前使用的是古老的 IE' + device.ie + '，你丫的电脑该升级啦！！！')
    }
    var treeMobile = $('.site-tree-mobile'), shadeMobile = $('.site-mobile-shade');
    treeMobile.on('click', function() {
        $('body').addClass('site-mobile')
    });
    shadeMobile.on('click', function() {
        $('body').removeClass('site-mobile')
    });
