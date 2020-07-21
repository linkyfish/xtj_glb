function TrimString(strString)
{
	var lre = /^\s*/ ;
	var rre = /\s*$/ ;
	strString = strString.replace(lre, "") ;
	strString = strString.replace(rre, "") ;

	return strString ;
}

function OpenWindow(strObjTitle, strObjURL, bObjPopup, strObjOpenWinArgs)
{
	var strReturn = "" ;

	strObjTitle = TrimString(strObjTitle) ;
	strObjURL = TrimString(strObjURL) ;
	strObjOpenWinArgs = TrimString(strObjOpenWinArgs) ;

	if(strObjURL == '')
		strReturn = strObjTitle ;
	else
	{
		if(bObjPopup == true)
			strReturn = "<a href='#' onClick=javascript:window.open('"+ strObjURL +"','','"+ strObjOpenWinArgs +"')>"+ strObjTitle +"</a>" ;
		else if(strObjOpenWinArgs != null)
			strReturn = "<a href='"+ strObjURL +"' target='"+ strObjOpenWinArgs +"'>"+ strObjTitle +"</a>";
	}

	return strReturn ;
}