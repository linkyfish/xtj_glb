var strDoLoginRedirectUrl	= "" ;
var strDoLoginOpenName		= "" ;
var strDoLoginOpenHeight	= 1000 ;
var strDoLoginOpenWidth		= 1000 ;
var strDoLoginWindows		= null ;
var strID					= "f_strID" ;
var strPassword				= "f_strPassword" ;
var strAutoSaveID			= "f_bSaveID" ;
var strVerifyCode			= "f_strVerifyCode" ;
var strImgVerifyCode		= "imgVerifyCode" ;

// 驗證碼按下enter也觸發Login
$(function() {
	
	if ($('#'+strVerifyCode).length > 0)
	{
		$('#'+strVerifyCode).keypress(function(e) {  
			var key = window.event ? e.keyCode : e.which;
			if (key == 13)
			{
				$('#imgLogin').click();
				event.cancelBubble = true;
				event.returnValue = false;
			}
		});
	}
}) ;

function DoLogin(_nGameNo,_strUrl,_strID,_strPassword,_strAutoSaveID,_strVerifyCode, _strImgVerifyCode,_strOpenName,_strOpenWidth,_strOpenHeight)
{
	strDoLoginRedirectUrl	= _strUrl ;
	strDoLoginOpenName		= (typeof(_strOpenName) !=  "undefined" ? _strOpenName : strDoLoginOpenName) ;
	strDoLoginOpenHeight	= (typeof(_strOpenHeight) !=  "undefined" ? _strOpenHeight : strDoLoginOpenHeight) ;
	strDoLoginOpenWidth		= (typeof(_strOpenWidth) !=  "undefined" ? _strOpenWidth : strDoLoginOpenWidth) ;
	strID					= (typeof(_strID) !=  "undefined" ? _strID : strID) ;
	strPassword				= (typeof(_strPassword) !=  "undefined" ? _strPassword : strPassword) ;
	strAutoSaveID			= (typeof(_strAutoSaveID) !=  "undefined" ? _strAutoSaveID : strAutoSaveID) ;
	strVerifyCode			= (typeof(_strVerifyCode) !=  "undefined" ? _strVerifyCode : strVerifyCode) ;
	strImgVerifyCode		= (typeof(_strImgVerifyCode) !=  "undefined" ? _strImgVerifyCode : strImgVerifyCode) ;

	if ($('.waterMarkText').length > 0)
	{
		$('.waterMarkText').each(function () {
			if(!this.reset)
				this.value = '' ;
		});
	}

	// 是否使用 jsonp(ie都使用 jsonp，原本應該是 && $.browser.version < 10.0，但是因為IE10會出現 XMLHttpRequest: Network Error 0x2ee4，原因是因為在iFrame內使用卻發現SSL憑證是錯誤的，所以開發測試都會死掉)
	// 微軟回應 0x2ee4 錯誤：https://connect.microsoft.com/IE/feedback/details/877525/ie11-returns-status-0-during-ajax-post-operation-from-an-iframe-xmlhttprequest-network-error-0x2ee4#tabs
	// 443 port 一律使用 json
	var bSsl = (location.port == 443) ;
	var bJsonp = !bSsl && (navigator.userAgent.indexOf("MSIE")>0 || navigator.userAgent.indexOf("Trident")>0) ;

	if (strDoLoginOpenName.length > 0 && decodeURIComponent((decodeURIComponent(location.href))).indexOf('/InGame/Shortcut/index.aspx') == -1)
	{
		strDoLoginWindows		= window.open('about:blank', strDoLoginOpenName,config='height=10,width=10') ;
		strDoLoginWindows.document.write("loading...") ;
	}

	$.ajax({
		type: "POST",
		url: "https://" + location.hostname + "/include/Services/x_DoLogin.aspx",
		data : {"f_strCallBack" : (bJsonp ? "DoLoginSuccess" : "") ,"f_nGameNo":_nGameNo,"f_strID":$('#'+strID).val(),"f_strPassword":$('#'+strPassword).val(),"f_strVerifyCode":$('#'+strVerifyCode).val(),"f_bSaveID":$('#'+strAutoSaveID).prop('checked') || $('#'+strAutoSaveID).val()},
		dataType: (bJsonp ? "jsonp" :"json"),
		async: true,
			xhrFields : {
			withCredentials : true
		},

		success: function(JData)
		{
			DoLoginSuccess(JData,_strID,_strPassword,_strVerifyCode, _strImgVerifyCode)
		},
		error: function(jqXHR,textStatus,errorThrown) {
			if(location.href.toLowerCase().indexOf('games/tmd') > -1)
				$('#custom-overlay').remove() ;

			if (!bJsonp)
				DoLoginFail(jqXHR.responseText + ":" + textStatus + ":" + errorThrown,_strID,_strPassword,_strVerifyCode, _strImgVerifyCode);
		}
	});
}

function DoLoginSuccess(JData,_strID,_strPassword,_strVerifyCode, _strImgVerifyCode)
{
	switch(JData.STATUS)
	{
		case 0 :
			var	strDoLoginNextUrl		= "" ;
			// 登入成功重整頁面
			if (JData.GAME_LOGIN)
				strDoLoginNextUrl = "/common/al.aspx?re=" + encodeURIComponent(strDoLoginRedirectUrl) + "&lc=" + JData.LC ;
			else
				strDoLoginNextUrl = strDoLoginRedirectUrl ;

				if (strDoLoginOpenName.length > 0 && decodeURIComponent((decodeURIComponent(location.href))).indexOf('/InGame/Shortcut/index.aspx') == -1)
				{
					strDoLoginWindows.resizeTo(strDoLoginOpenWidth,strDoLoginOpenHeight);
					strDoLoginWindows.location.href		= strDoLoginNextUrl ;
					strDoLoginWindows.blur() ;
					strDoLoginWindows.focus() ;
					setTimeout("window.parent.location.reload();",1000) ;
				}
				else
					top.location.href = strDoLoginNextUrl ;
			break ;
		default :
			if($("#member-login-message-box").length == 0)
			{
				alert('(' + JData.STATUS + ')' + JData.MESSAGE) ;
			}
			else
			{
				$("#member-login-message-box").find("li").slice(1).remove() ;
				$("#member-login-message-box").find("ul").append("<li>(" + JData.STATUS + ")" + JData.MESSAGE + "<li>") ;
				$("#member-login-message-box").show();
			}

			if (strDoLoginOpenName.length > 0 && decodeURIComponent((decodeURIComponent(location.href))).indexOf('/InGame/Shortcut/index.aspx') == -1)
			{
				strDoLoginWindows.close();
			}
			DoLoginReset(_strID,_strPassword,_strVerifyCode, _strImgVerifyCode) ;
			break ;
	}

	if(location.href.toLowerCase().indexOf('games/tmd') > -1)
		$('#custom-overlay').remove() ;
}

function DoLoginFail(strErrorMessage,_strID,_strPassword,_strVerifyCode, _strImgVerifyCode)
{
	if($("#member-login-message-box").length == 0)
	{
		alert('系統發生異常，請稍後再試！');
	}
	else
	{
		$("#member-login-message-box").find("li").slice(1).remove() ;
		$("#member-login-message-box").find("ul").append("<li>系統發生異常，請稍後再試！<li>") ;
		$("#member-login-message-box").show();
	}

	DoLoginReset(_strID,_strPassword,_strVerifyCode, _strImgVerifyCode) ;
}

function DoLoginReset(_strID,_strPassword,_strVerifyCode, _strImgVerifyCode)
{
	$('#'+_strPassword).val('') ;

	if ($('#'+_strVerifyCode).length > 0)
	{
		$('#'+_strVerifyCode).val('').blur() ;
		$('#'+_strImgVerifyCode).click() ;
	}
	if ($('#'+_strID).val().length == 0)
	{
		$('#'+_strID).blur().focus() ;
	}
	else
		$('#'+_strPassword).focus() ;
}