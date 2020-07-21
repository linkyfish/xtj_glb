/*png24*/

function setPng(obj)
{
	var i	= navigator.appVersion.indexOf("MSIE");
	if(i > 0)
	{
		i	= parseFloat(navigator.appVersion.substr(i + 4));
		if(i < 7)
		{
			if(obj.src != undefined && obj.src != "" && obj.src != null)
			{
				obj.width			= obj.height = 1 ;
				obj.className		= obj.className.replace(/\bpng\b/i,'') ;
				obj.style.filter	= "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ obj.src +"',sizingMethod='image');" ;
				obj.src				= "/Graphics/common/blank.gif" ;
			}
		}
	}

	return null ;
}

function SetPNGs()
{
	for(var i = 0 ; i < document.images.length ; i++)
	{
		var m	= document.images[i] ;

		if(m.className.toLowerCase().indexOf("png") == -1)
		{
			if(m.src.toLowerCase().lastIndexOf(".png") > 0)
			{
				m.width			= m.height = 1 ;
				m.style.filter	= "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ m.src +"',sizingMethod='image');" ;
				m.src			= "/Graphics/common/blank.gif" ;
			}
		}
	}
}

var nIEVersion	= navigator.appVersion.indexOf("MSIE") ;
if(nIEVersion > 0)
{
	nIEVersion	= parseFloat(navigator.appVersion.substr(nIEVersion + 4)) ;

	if(nIEVersion < 7)
		setTimeout("SetPNGs()", 1) ;
}

function goFamily(strUrl){
	if(strUrl ==""){
		return ;
	}else{
		window.open(strUrl);
	}
}
