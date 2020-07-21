var nRegisterWidth		= 534 ;
if ($.browser.msie)
	nRegisterWidth		= 550 ;
function PopupRegister(_strGame,_strUrl)
{
	if ($('#divPopupRegister').length == 0)
		$('body').append('<div id="divPopupRegister" title="gametower會員註冊" style="display:none;"><iframe frameborder="0" scrolling="no" width="100%" height="600" id="RegisterIframe" src=""></iframe></div>') ;

	var popupdiv = $("#divPopupRegister").css('display','').dialog({
		modal: true,
		draggable: false,
		resizable: false,
		position: ['center', 'top+2%'],
		//show: 'blind',
		//hide: 'blind',
		width: nRegisterWidth,
		dialogClass: 'ui-dialog-osx',
		open: function(ev, ui){
            $('#RegisterIframe').attr('src','/Games/' + _strGame + '/member/e_Register.aspx?mode=5&smf=1&re=' + encodeURIComponent(_strUrl));
           }
	});

	// IE 再調整 left 位置，因為 IE 不吃上面的 center
	if ($.browser.msie)
	{
		var nWindowWidth = $(document).width() ;
		var nleft = (Math.abs((nWindowWidth - nRegisterWidth) / 2)) ;
		popupdiv.parent('div').css('left',nleft + 'px');
	}
	// 強制到最上層(因為多點幾次會出現 z-index 超大的狀況，會造成下蓋上的狀況，因此手動調整)
	//popupdiv.parent('div').next('.ui-widget-overlay').css('z-index',9998); // 遮罩
	$('.ui-widget-overlay').css('z-index',9998);
	popupdiv.parent('div').css('z-index',9999);	// 主視窗
}

function PopupGTRegister(_strGame,_strUrl)
{
	if ($('#divPopupRegister').length == 0)
		$('body').append('<div id="divPopupRegister" title="gametower會員註冊" style="display:none;"><iframe frameborder="0" scrolling="no" width="100%" height="600" id="RegisterIframe" src=""></iframe></div>') ;

	var popupdiv = $("#divPopupRegister").css('display','').dialog({
		modal: true,
		draggable: false,
		resizable: false,
		position: ['center', 'top+2%'],
		//show: 'blind',
		//hide: 'blind',
		width: nRegisterWidth,
		dialogClass: 'ui-dialog-osx',
		open: function(ev, ui){
            $('#RegisterIframe').attr('src','/member/e_Register.aspx?mode=5&smf=1&re=' + encodeURIComponent(_strUrl));
           }
	});

	// IE 再調整 left 位置，因為 IE 不吃上面的 center
	if ($.browser.msie)
	{
		var nWindowWidth = $(document).width() ;
		var nleft = (Math.abs((nWindowWidth - nRegisterWidth) / 2)) ;
		popupdiv.parent('div').css('left',nleft + 'px');
	}
	// 強制到最上層(因為多點幾次會出現 z-index 超大的狀況，會造成下蓋上的狀況，因此手動調整)
	//popupdiv.parent('div').next('.ui-widget-overlay').css('z-index',9998); // 遮罩
	$('.ui-widget-overlay').css('z-index',9998);
	popupdiv.parent('div').css('z-index',9999);	// 主視窗
}

var nLoginWidth		= 300 ;
if ($.browser.msie)
	nLoginWidth		= 320 ;

function PopupLogin(_strGame,_strUrl)
{
	PopupLogin(_strGame,_strUrl,'')
}

function PopupLogin(_strGame,_strUrl,_strQueryString)
{
	if (_strQueryString == null || _strQueryString==undefined || _strQueryString.length == 0)
		_strQueryString		= '' ;
	else
		_strQueryString		= '&' + _strQueryString ;

	if ($('#divPopupLogin').length == 0)
		$('body').append('<div id="divPopupLogin" title="隨你玩會員登入" style="display:none;"><iframe frameborder="0" scrolling="no" width="100%" height="200" id="LoginIframe" src=""></iframe></div>') ;

	var popupdiv = $("#divPopupLogin").css('display','').dialog({
		modal: true,
		draggable: false,
		resizable: false,
		position: ['center', 'top+2%'],
		//show: 'blind',
		//hide: 'blind',
		width: nLoginWidth,
		dialogClass: 'ui-dialog-osx',
		open: function (ev, ui) {
			
            $('#LoginIframe').attr('src','/Games/' + _strGame + '/member/e_Login.aspx?re=' + encodeURIComponent(_strUrl) + _strQueryString);
           }
	});

	// IE 再調整 left 位置，因為 IE 不吃上面的 center
	if ($.browser.msie)
	{
		var nWindowWidth = $(document).width() ;
		var nleft = (Math.abs((nWindowWidth - nLoginWidth) / 2)) ;
		popupdiv.parent('div').css('left',nleft + 'px');
	}
	// 強制到最上層(因為多點幾次會出現 z-index 超大的狀況，會造成下蓋上的狀況，因此手動調整)
	//popupdiv.parent('div').next('.ui-widget-overlay').css('z-index',9998); // 遮罩
	$('.ui-widget-overlay').css('z-index',9998);
	popupdiv.parent('div').css('z-index',9999);	// 主視窗

	$('.ui-dialog-title').css('color','#ffffff');
}

function PopupGTLogin(_strUrl)
{
	PopupGTLogin(_strUrl,'')
}

function PopupGTLogin(_strUrl,_strQueryString)
{
	if (_strQueryString == null || _strQueryString==undefined || _strQueryString.length == 0)
		_strQueryString		= '' ;
	else
		_strQueryString		= '&' + _strQueryString ;

	if ($('#divPopupLogin').length == 0)
		$('body').append('<div id="divPopupLogin" title="gametower會員登入" style="display:none;"><iframe frameborder="0" scrolling="no" width="100%" height="200" id="LoginIframe" src=""></iframe></div>') ;

	var popupdiv = $("#divPopupLogin").css('display','').dialog({
		modal: true,
		draggable: false,
		resizable: false,
		position: ['center', 'top+2%'],
		//show: 'blind',
		//hide: 'blind',
		width: nLoginWidth,
		dialogClass: 'ui-dialog-osx',
		open: function (ev, ui) {
			
            $('#LoginIframe').attr('src','/member/e_Login.aspx?re=' + encodeURIComponent(_strUrl) + _strQueryString);
           }
	});

	// IE 再調整 left 位置，因為 IE 不吃上面的 center
	if ($.browser.msie)
	{
		var nWindowWidth = $(document).width() ;
		var nleft = (Math.abs((nWindowWidth - nLoginWidth) / 2)) ;
		popupdiv.parent('div').css('left',nleft + 'px');
	}
	// 強制到最上層(因為多點幾次會出現 z-index 超大的狀況，會造成下蓋上的狀況，因此手動調整)
	//popupdiv.parent('div').next('.ui-widget-overlay').css('z-index',9998); // 遮罩
	$('.ui-widget-overlay').css('z-index',9998);
	popupdiv.parent('div').css('z-index',9999);	// 主視窗
}

function IsLogin()
{
	var bLogin = false ;
	$.ajax({
		type: "GET",
		url: "/include/Services/x_GetLoginStatus.aspx",
		dataType: "json",
		async: false,
		success: function(JData)
		{
			switch(JData.STATUS)
			{
				case 0 :
					bLogin		= true ;
					break ;
				default :
					bLogin		= false ;
					break ;
			}
		},
		error: function(xmlHttpRequest,error)
		{
			bLogin		= false ;
		}
	});

	return bLogin ;
}

function LoginRedirect(_strGame,_strUrl)
{
	return LoginRedirect(_strGame,_strUrl,'') ;
}

function LoginRedirect(_strGame,_strUrl,_strQueryString)
{
	var bRe		= false ;
	if(IsLogin()){bRe = true;}else{PopupLogin(_strGame,_strUrl,_strQueryString);bRe = false;}
	return bRe ;
}