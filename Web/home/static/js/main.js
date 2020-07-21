// 顯示錯誤訊息
function ShowMessage(rgstrMessage,_strMessageBoxId)
{
	var strMessageBoxId		= (typeof(_strMessageBoxId) ==  "undefined" ? "#message-box" : "#"+_strMessageBoxId) ;
	$(strMessageBoxId).find("li").slice(1).remove() ;
	
    // 捲動到頁面頂端
    window.scrollTo(0, 0) ;
    
    if(Array.isArray(rgstrMessage))
	{
		for(var i = 0; i < rgstrMessage.length; i++)
		{
			if(rgstrMessage[i].length > 0)
				$(strMessageBoxId).find("ul").append("<li>" + rgstrMessage[i] + "<li>") ;
		}
	}
	else if(rgstrMessage.length > 0)
	{
		$(strMessageBoxId).find("ul").append("<li>" + rgstrMessage + "<li>") ;
	}
	$(strMessageBoxId).show() ;
}

// 讀取遮罩
function ShowLoader()
{
	$("body").loader({imgUrl: "/mobile/images/loader.gif", size: 32, autoCheck: false});
}

function CloseLoader()
{
	$.loader.close() ;
}
