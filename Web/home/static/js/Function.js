function GT_Article(_strTagID, _strPath, _nTimeout)
{
	this.strTagID	= _strTagID ;
	this.strPath	= _strPath ;
	this.nTimeout	= _nTimeout ;

	this.Show		= function(_strQuery)
	{
		document.getElementById(this.strTagID).innerHTML	= "<div align=\"center\"><img src=\"/images/loading.gif\"></div>" ;

		var oArticleXML			= new XML() ;

		oArticleXML.strTagID	= this.strTagID ;

		oArticleXML.Handler		= function()
		{
			document.getElementById(this.strTagID).innerHTML	= oArticleXML.XMLData.ROOT.HTML ;
		}

		oArticleXML.Execute("/include/Article/x_GetHTML.aspx", "f_strPath="+ this.strPath + _strQuery +"&f_nTimeout="+ this.nTimeout) ;
	}
}

function GT_ShowArticle(_strTagID, _strPath, _nTimeout)
{
	GT_ShowArticle_CallBack(_strTagID, _strPath, _nTimeout, null) ;
}

function GT_ShowArticle_CallBack(_strTagID, _strPath, _nTimeout, callback)
{
	document.getElementById(_strTagID).innerHTML	= "<div align=\"center\"><img src=\"/images/loading.gif\"></div>" ;

	var oArticleXML		= new XML() ;

	oArticleXML.Handler = function () {
		document.getElementById(_strTagID).innerHTML = oArticleXML.XMLData.ROOT.HTML;

		if(callback != null)
			callback();
	}

	oArticleXML.Execute("/include/Article/x_GetHTML.aspx", "f_strPath="+ _strPath +"&f_nTimeout="+ _nTimeout) ;
}

function GT_SwitchArticleMenu()
{
	if(arguments.length > 1)
	{
		document.getElementById(arguments[0][0]).style.display	= "" ;

		GT_ShowArticle(arguments[0][1], arguments[0][2], arguments[0][3]) ;

		for(var i = 1 ; i < arguments.length ; i++)
			document.getElementById(arguments[i]).style.display	= "none" ;
	}
}