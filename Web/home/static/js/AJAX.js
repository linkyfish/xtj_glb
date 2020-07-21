/* --------------------------------------------------------------------------------------------------------------------
	修改歷史：
		
		2011/08/19	(英譽)	（淘）11-0802-003 Game淘官網改善事項
		2011/01/07	(小滋)	新增 oXML.nodeType == 4 (CDATA_SECTION_NODE) 的處理
-------------------------------------------------------------------------------------------------------------------- */

function XML()
{
	this.Request	= undefined ;
	this.Handler	= undefined ;
	this.XMLData	= new Object() ;

	if(window.ActiveXObject && !window.XMLHttpRequest)
	{
		var aryMSXML	= ["Msxml2.XMLHTTP.5.0", "Msxml2.XMLHTTP.4.0", "Msxml2.XMLHTTP.3.0", "Msxml2.XMLHTTP", "Microsoft.XMLHTTP"] ;

		for(var i = 0 ; i < aryMSXML.length ; i++)
		{
			try
			{
				this.Request	= new ActiveXObject(aryMSXML[i]) ;
				break ;
			}
			catch(e){}
		}
	}
	else
		this.Request	= new XMLHttpRequest() ;

	this.SetHeader	= function(_strKey, _strValue)
	{
		this.Request.setRequestHeader(_strKey, _strValue) ;
	}

	this.Execute	= function(_strUrl, _strData)
	{
		var m_this	= this ;
		
		
		var bPost	= _strData != undefined ;
		var m_this	= this ;

		m_this.Request.open((bPost ? "POST" : "GET"), _strUrl, true) ;

		if(bPost)
			m_this.Request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8") ;
		
		m_this.Request.send(_strData) ;

		m_this.Request.onreadystatechange	= function()
		{
			if(m_this.Request.readyState == 4)
			{
				if(m_this.Request.status == 200)
				{
					var oXML		= m_this.Request.responseXML ;
					
					m_this.BuildXML(oXML, m_this, "XMLData") ;

					if(typeof(m_this.Handler) == "function")
						m_this.Handler() ;
				}
				else
				{
					// throw new Error("XML Error : "+ m_this.Request.statusText) ;
				}
			}
		}
		
		
	}

	this.BuildXML	= function(_oXML, _oParentNode, _strNodeName, _nIndex)
	{
		var aryNodes	= new Object() ;

		for(var i = 0 ; i < _oXML.childNodes.length ; i++)
		{
			var strName			= _oXML.childNodes[i].nodeName ;

			aryNodes[strName]	= aryNodes[strName] != undefined ? (1 + aryNodes[strName]) : 1 ;

		}

		for(var i = 0 ; i < _oXML.childNodes.length ; i++)
		{
			var oXML	= _oXML.childNodes[i] ;

			if(oXML.nodeType == 1) //ELEMENT_NODE
			{
				var strName	= oXML.nodeName ;

				if(aryNodes[strName] > 1)
				{
					var nIndex	= 0 ;

					if(_oParentNode[_strNodeName][strName] == undefined)
					{
						_oParentNode[_strNodeName][strName]				= new Object() ;
						_oParentNode[_strNodeName][strName].Count		= 1 ;
					}
					else
					{
						_oParentNode[_strNodeName][strName].Count++ ;
						nIndex	= _oParentNode[_strNodeName][strName].Count - 1 ;
					}

					_oParentNode[_strNodeName][strName][nIndex]			= new Object() ;

					this.BuildXML(oXML, _oParentNode[_strNodeName], strName, nIndex) ;
				}
				else
				{
					if(_nIndex != undefined)
					{
						_oParentNode[_strNodeName][_nIndex][strName]	= new Object() ;

						this.BuildXML(oXML, _oParentNode[_strNodeName][_nIndex], strName) ;
					}
					else
					{
						_oParentNode[_strNodeName][strName]				= new Object() ;

						this.BuildXML(oXML, _oParentNode[_strNodeName], strName) ;
					}
				}
			}
			else if(oXML.nodeType == 3 && oXML.nodeValue.replace(/\s/gi, "").length > 0) // TEXT_NODE
			{
				if(typeof(_oParentNode[_strNodeName]) == "object")
					_oParentNode[_strNodeName]	= oXML.nodeValue ;
				else
					_oParentNode[_strNodeName]	+= oXML.nodeValue ;
			}
			else if(oXML.nodeType == 4 && oXML.nodeValue.replace(/\s/gi, "").length > 0) // CDATA_SECTION_NODE
			{
				if(typeof(_oParentNode[_strNodeName]) == "object")
					_oParentNode[_strNodeName]	= oXML.nodeValue ;
				else
					_oParentNode[_strNodeName]	+= oXML.nodeValue ;
			}
		}
	}
}