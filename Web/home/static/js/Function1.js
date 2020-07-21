function GT_AD_CounterType() {
	this.NONE = 0;
	this.PASS = 1;
	this.HIT = 2;
	this.ALL = 3;
}

function GT_ShowAD(_strTagID, _strAreaID, _nCountType, _strATagExtString, _strIMGTagExtString, _bAvoidIP, _nTimeout, _strCommand) {
	document.getElementById(_strTagID).innerHTML = "<div align=\"center\"><img src=\"/images/loading.gif\"></div>";

	var oADXML = new XML();

	oADXML.Handler = function () {
		// 取得article站傳回之HTML資料
		document.getElementById(_strTagID).innerHTML = oADXML.XMLData.ROOT.HTML;

		// 2018/08/29	(有良)	（淘）18-0821-001 明星3缺1問題回報客服資訊連結變更
		if (document.getElementById(_strTagID).innerHTML.toLowerCase().indexOf("/images/blank.gif") < 0)
		{
			// 因為有些瀏覽器不支援 window.execScript，所以用類似功能 window.eval 替代
			if(window.execScript)
				window.execScript(_strCommand);
			else
				window.eval(_strCommand);
		}
	}

	// 2013/01/30 (damon) location.protocol 判斷 https 修正為 https:
	if (location.protocol == "https:") // 有跑https無法進行jquary ajax跨網域，因article站無https，故以POST方式取得article站資料(有Cache機制)
		oADXML.Execute("/include/AD/x_GetHTML.aspx", "f_strURL=" + escape(location.protocol + "//" + location.hostname + location.pathname + location.search) + "&f_strAreaID=" + _strAreaID + "&f_nCountType=" + _nCountType + "&f_strATagExtString=" + _strATagExtString + "&f_strIMGTagExtString=" + _strIMGTagExtString + "&f_bAvoidIP=" + _bAvoidIP + "&f_nTimeout=" + _nTimeout);
	else // jquary ajax跨網域呼叫article站(無Cache機制)
	{
		var strURL = location.protocol + "//" + GetAgentHostName('article',location.hostname) + "/common/lib/Advertisement/GetAD.aspx?format=json&jsoncallback=GetArticleAD&q_strAreaID=" + escape(_strAreaID) + "&q_nCountType=" + _nCountType + "&q_strATagExtString=" + escape(_strATagExtString) + "&q_strIMGTagExtString=" + escape(_strIMGTagExtString) + "&q_bAvoidIP=" + _bAvoidIP + "&q_strTagID=" + _strTagID;
		$.ajax
		({
			url: strURL,
			type: 'GET',
			dataType: 'jsonp'
		});
	}
}

function GT_ShowADList(_strTagID, _strAreaID, _nCountType, _strATagExtString, _strIMGTagExtString,_strCallBackFunction) {
	//document.getElementById(_strTagID).innerHTML = "<div align=\"center\"><img src=\"/images/loading.gif\"></div>";
	if (!_strCallBackFunction)
		_strCallBackFunction		= 'ADList_DefaultCallBack' ;

	var strURL = '' ;
	// 有跑https無法進行jquary ajax跨網域，因article站無https，故透過 x_GetADList.aspx
	if (location.protocol == "https:")
		strURL		= location.protocol + "//" + location.hostname + "/include/AD/x_GetADList.aspx" ;
	else
		strURL		= location.protocol + "//" + GetAgentHostName('article',location.hostname) + "/common/lib/Advertisement/GetADList.aspx" ;

		strURL		 +="?jsoncallback=" + _strCallBackFunction + "&q_strAreaID=" + escape(_strAreaID) + "&q_nCountType=" + _nCountType + "&q_strATagExtString=" + escape(_strATagExtString) + "&q_strIMGTagExtString=" + escape(_strIMGTagExtString) + "&q_strTagID=" + _strTagID;

	$.ajax
	({
		url: strURL,
		type: 'GET',
		dataType: 'jsonp'
	});
}



function GT_ShowADs(_strTagID, _strAreaID, _nCountType, _strATagExtString, _strIMGTagExtString,_strCallBackFunction) {
	
	if (!_strCallBackFunction)
		_strCallBackFunction		= 'AD_PageFunction' ;

	var strURL = '' ;
	// 有跑https無法進行jquary ajax跨網域，因article站無https，故透過 x_GetADList.aspx
	if (location.protocol == "https:")
		strURL		= location.protocol + "//" + location.hostname + "/include/AD/x_GetADs.aspx" ;
	else
		strURL		= location.protocol + "//" + GetAgentHostName('article',location.hostname) + "/common/lib/Advertisement/GetADs.aspx" ;

		strURL		+= "?jsoncallback=" + _strCallBackFunction + "&q_strAreaID=" + escape(_strAreaID) + "&q_nCountType=" + _nCountType + "&q_strATagExtString=" + escape(_strATagExtString) + "&q_strIMGTagExtString=" + escape(_strIMGTagExtString) + "&q_strTagID=" + _strTagID;

	$.ajax
	({
		url: strURL,
		type: 'GET',
		dataType: 'jsonp'
	});
}

// 取得article站傳回之JSON資料
function GetArticleAD(JData) {
	var o = document.getElementById(JData.TAG_ID);

	if (JData.AD_HTML != undefined) {
		// 2013/07/08 (victor) （行星）13-0614-003 行動版網頁項目相關製作需求
		// 行動版遊戲內嵌廣告連結帶入區域參數處理
		// 廣告區域ID命名含有MOBILE_INGAME
		if (JData.TAG_ID.indexOf("MOBILE_INGAME") != -1)					// ex:ad_MOBILE_INGAME_ACTION_AD1
		{
			o.innerHTML = JData.AD_HTML;

			var TrimStartIndex = JData.Area_ID.indexOf("ACTION") - 1;	// ex:Area_ID=MOBILE_I371_ANDROID_ACTION1
			var AD			= $("#" + JData.TAG_ID);
			var AD_aLink	= $("#" + JData.TAG_ID + " a");
			var AD_IMG		= $("#" + JData.TAG_ID + " img");
			var strURL		= AD_aLink.attr("href") ;

			// 2018/12/28 (damon) Fix NAI parameter setting
			if (strURL != undefined)
				AD_aLink.attr("href", strURL + (strURL.lastIndexOf("?") < 0 ? "?" : "&") + "NAI=" + escape(JData.Area_ID).substring(0, TrimStartIndex));	// ex:NAI=MOBILE_I371_ANDROID

			// 是預設圖檔/Graphics/Games/Mobile/images/new-ad/630x150.jpg時不顯示該廣告區域
			// 2014/05/16 (damon) src判斷修正為不需分大小寫
			if (AD_IMG.attr("src") != undefined && AD_IMG.attr("src").toLowerCase().indexOf("mobile/images/new-ad") != -1) {
				AD.css("display", "none");

				// 2014/05/16 (damon) 一併隱藏分隔線
				var AD_HR = o.nextSibling;

				if (AD_HR != undefined && AD_HR.tagName == "HR")
					AD_HR.style.display = "none";
			}
		}
		else
			o.innerHTML = JData.AD_HTML;
	}
	else
		o.innerHTML = "<div align=\"center\"><img src=\"/images/loading.gif\"></div>";
}

function GetAgentHostName(_strAgentName,_strHostName) {
	var nLength = 0;

	if (_strHostName != "") {
		nLength = _strHostName.indexOf('.', 0) ;
		var strName = _strHostName.substring(0,nLength);
		if (_strHostName.indexOf("twtest") > -1)
			return _strAgentName + "-agent-twtest." + GetWebDomainName(_strHostName) ;
		else
			return _strAgentName + "-agent." + GetWebDomainName(_strHostName) ;
	}
}

function GetWebDomainName(_strHostName) {
	var strInputUrl = _strHostName;
	var nStarDotIndex = 0;

	if (strInputUrl != "") {
		nStarDotIndex = strInputUrl.indexOf('.', 0) + 1;
		strInputUrl = strInputUrl.substring(nStarDotIndex);

		return strInputUrl;
	}
}

function GT_FlashADOpen(_url, _window, _arguments) {
	var ADPageRedirectTemp = "";

	for (var i = 0 ; i < _url.length ; i++) {
		if (_url.charCodeAt(i) == "162" && _url.charAt(i + 1) == "H") {
			ADPageRedirectTemp += "%";
			i++;
		}
		else
			ADPageRedirectTemp += _url.charAt(i);
	}

	open(ADPageRedirectTemp, _window.replace(/[^a-zA-Z]/gi, ""), _arguments);
}

var GT_AD_COUNTER_TYPE = new GT_AD_CounterType();

var JADLoop = new Object() ;	// ADLoop 的 json 變數
// Number 要用來記錄被摸到的物件的排行
// Count AD數量
// Move要用來計算座標
// Timer宣告一個變數，等一下要給計時器用
// WidthAD 寬度

// GT_ShowADList default callback
function ADList_DefaultCallBack(JData) {
	var strBanner = "";
	var strNumber = "";

	for (i = 0 ; i < JData.AD_DATA.length ; i++) {
		strBanner += "\r\n<li>" + JData.AD_DATA[i].AD_URL + "</li>";
		strNumber += "\r\n<li>●</li>";
	}

	// 加上 AD 的內容與事件
	$("#" + JData.TAG_ID + "").html(strBanner);

	JADLoop[JData.TAG_ID + "_Width"]		= $("#" + JData.TAG_ID + " img").eq(0).attr("width") ;
	JADLoop[JData.TAG_ID + "_Height"]		= $("#" + JData.TAG_ID + " img").eq(0).attr("height") ;
	JADLoop[JData.TAG_ID + "_Count"]		= $("#" + JData.TAG_ID + " img").length ;

	// 往上抓兩層設定width跟height(ad div)
	$("#" + JData.TAG_ID + "").parent().width(JADLoop[JData.TAG_ID + "_Width"]) ;
	$("#" + JData.TAG_ID + "").parent().parent().width(JADLoop[JData.TAG_ID + "_Width"]) ;
	$("#" + JData.TAG_ID + "").parent().parent().height(JADLoop[JData.TAG_ID + "_Height"]) ;

	$("#" + JData.TAG_ID + "").parent().hover(function () {
		clearTimeout(JADLoop[JData.TAG_ID + "_Timber"]);
	}, function () {
		JADLoop[JData.TAG_ID + "_Timber"] = setTimeout("ADLoop('" + JData.TAG_ID + "')", 3000);
	});

	// 點選的豆豆存在則加上內容與事件
	if($("#" + JData.TAG_ID + "_o").length > 0)
	{
		$("#" + JData.TAG_ID + "_o").html(strNumber);

		$("#" + JData.TAG_ID + "_o li").eq(0).addClass("click");

		$("#" + JData.TAG_ID + "_o").parent().hover(function () {
		clearTimeout(JADLoop[JData.TAG_ID + "_Timber"]);
		}, function () {
			JADLoop[JData.TAG_ID + "_Timber"] = setTimeout("ADLoop('" + JData.TAG_ID + "')", 3000);
		});

		$("#" + JData.TAG_ID + "_o li").click(function () {
			JADLoop[JData.TAG_ID + "_Number"] = $(this).prevAll().length ;
			JADLoop[JData.TAG_ID + "_Move"] = JADLoop[JData.TAG_ID + "_Number"] * JADLoop[JData.TAG_ID + "_Width"] * -1;

			$("#" + JData.TAG_ID + "").stop().animate({ left: JADLoop[JData.TAG_ID + "_Move"] }, 1000);
			$(this).addClass("click").siblings().removeClass();
		});
		
	}

	ADLoop(JData.TAG_ID); // 呼叫 啟動上面的計時器 function
}


//==========設定計時器=========================================================
function ADLoop(_strTagID) {

	if (JADLoop[_strTagID + "_Number"] < JADLoop[_strTagID + "_Count"] - 1) {
		JADLoop[_strTagID + "_Number"] += 1;
		JADLoop[_strTagID + "_Move"] = JADLoop[_strTagID + "_Number"] * JADLoop[_strTagID + "_Width"] * -1;
	} else {
		JADLoop[_strTagID + "_Number"] = 0;
		JADLoop[_strTagID + "_Move"] = 0;
	}

	$("#" + _strTagID + "").stop().animate({ left: JADLoop[_strTagID + "_Move"] }, 500);

	// 點選的豆豆存在則加上內容與事件
	if($("#" + _strTagID + "_o").length > 0)
	{
		$("#" + _strTagID + "_o li").removeClass().eq(JADLoop[_strTagID + "_Number"]).addClass("click");
	}

	JADLoop[_strTagID + "_Timber"] = setTimeout("ADLoop('" + _strTagID + "')", 3000);
}
