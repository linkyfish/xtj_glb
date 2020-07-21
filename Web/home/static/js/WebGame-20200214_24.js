function OpenWebGameWindow(GameKind, Link) {
	var nWidth;
	var nHeight;
	var nTop;
	var nLeft;
	var strTitle;
	var bGoToShortCut	= true ;	// 是否到捷徑檔頁面(預設true)
	var strHref			= "/Games/FreePlay/Shortcut/index.aspx" ;		// 網頁前往頁面

	switch (GameKind.toString().toUpperCase()) {
		case "WEB371":
		case "371":
			strTitle = '新明星3缺1';
			nWidth = 960;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "WEBBIG2":
			strTitle = '明星大老二';
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			bGoToShortCut = false;
			break;

		case "MARTAI":
			strTitle = '甜心小瑪莉';
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "TH":
			strTitle = '甜心德州撲克';
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "MFH":
			strTitle = '深海大冒險';
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "CMJ":
			strTitle = '競技麻將';
			nWidth = 1024;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			strHref = "/Games/FreePlay/Club/" ;
			break;

		case "CBIG2":
			strTitle = '競技撲克';
			nWidth = 1024;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			strHref = "/Games/FreePlay/Club/" ;
			break;

		case "CTH":
			strTitle = '競技淘金';
			nWidth = 1024;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			strHref = "/Games/FreePlay/Club/" ;
			break;

		case "FRUIT":
			strTitle = '淘金水果';
			nWidth = 1024;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "SICBO":
			strTitle = '甜心骰寶';
			nWidth = 1024;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "FPK":
			strTitle = '王牌5PK';
			nWidth = 1024;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "MD":
			strTitle = '滿貫大亨';
			nWidth = 1024;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "SPK":
			strTitle = '王牌7PK';
			nWidth = 1024;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;
			
		case "DDZ":
			strTitle = '新明星鬥地主';
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "PLOL":
			strTitle = "藍星戰記";
			nWidth = 1024;
			nHeight = 811;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;
			
		case "TGK":
			strTitle = "黃金騎士";
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "PK13":
			strTitle = "明星13支";
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			bGoToShortCut = false;
			break;

		case "NIUNIU":
			strTitle = '明星妞妞撲克';
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;
			
		case "WEBWE5":
			strTitle = "WEB 唯舞獨尊";
			nWidth = 800;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "CAT":
			strTitle = '尋寶夾夾樂';
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;
		
		case "SAN":
			strTitle = "戰三國";
			nWidth = 960;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "BJ21":
			strTitle = "皇家21點";
			nWidth = 960;
			nHeight = 640;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "BACCARAT":
			strTitle = "百家樂";
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "FANTAN":
			strTitle = "王牌接龍";
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;

		case "KOVWEB":
			strTitle = "三國戰紀";
			nWidth = 1200;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			bGoToShortCut = false ;
			break;

		case "PLS":
			strTitle = "海盜樂園";
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break ;

		case "DS":
			strTitle = '明星射龍門';
			nWidth = 960;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			bGoToShortCut = false;

		default:
			strTitle = 'gametower遊戲';
			nWidth = 800;
			nHeight = 600;
			nTop = window.screen.height / 2 - nHeight / 2;
			nLeft = window.screen.width / 2 - nWidth / 2;
			break;
	}
	var GameWindow ;

	GameWindow = window.open(Link, strTitle, 'toolbar=no, menubar=no, scrollbars=no, resizable=yes,location=no, status=no,top=' + nTop + ',left=' + nLeft + ',toolbar=no,width=' + nWidth + ',height=' + nHeight) ;


	// 已在捷徑檔 or 在活動頁 不導去捷徑檔
	if
	(
		bGoToShortCut &&
	 	(location.href.toLowerCase().indexOf("/games/freeplay/shortcut/index.aspx") == -1 && 
		location.href.toLowerCase().indexOf("/games/freeplay/shortcut/") == -1 && 
		location.href.toLowerCase().indexOf("/action/") == -1 &&
		location.href.toLowerCase().indexOf("/e_login.aspx") == -1)&&
		location.href.toLowerCase().indexOf("/Games/FreePlay/Shortcut/index.aspx") == -1
	) 
	{
		location = strHref ;
	}	
}