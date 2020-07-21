function GetFlashVersion()
{
	var nVersion	= -1;
	if(navigator.plugins && navigator.plugins.length)
	{
		for(var i=0; i<navigator.plugins.length; i++)
		{
			if(navigator.plugins[i].name.indexOf("Shockwave Flash") != -1)
			{
				nVersion	= navigator.plugins[i].description.match(/\s([\d]+)\./gi, "$1") ;
				break ;
			}
		}
	}
	else if(window.ActiveXObject)
	{
		for(var i=10; i>=2; i--)
		{
			try
			{
				if(eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash."+ i +"');"))
				{
					nVersion	= i ;
					break ;
				}
			}
			catch(e){}
		}
	}
	return nVersion;
}

function GetFlashPlayer(_popup)
{
	if(_popup)
	{
		var width	= 300 ;
		var height	= 200 ;
		var left	= (screen.width - width) / 2 ;
		var top		= (screen.height - height) / 2 ;
		var w		= open("/include/InstallFlashPlayer.htm", "InstallFlashPlayer", "top="+ top +",left="+ left +",width="+ width +",height="+ height +",toolbar=0,scrollbars=0,resizable=1") ;
		w.focus() ;
	}
	else
	{
		var o	= document.getElementById("FlashPlayerFrame") ;
		if(o)
			o.src	= "/include/InstallFlashPlayer.htm" ;
		else
		{
			if(document.all)
				document.body.innerHTML	+= "<iframe id=\"FlashPlayerFrame\" src=\"/include/InstallFlashPlayer.htm\" style=\"display:none\"></iframe>" ;
			else
				location.href			= "http://fpdownload.macromedia.com/get/flashplayer/current/install_flash_player_tc.exe" ;
		}
	}
}

function FlashObject(_name)
{
	if(navigator.appName.indexOf("Microsoft") != -1)
		return window[_name];
	else
		return document[_name];
}