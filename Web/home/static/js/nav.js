// JavaScript Document
$(function(){
	
	$(".bar").on("click", SHOWSHOW );
	$(".SignIn").on("click", LOGINBOXSHOW );
	$(".SignUp").on("click", REGISTERBOXSHOW );
	//$(".EzSignIn").on("click", EZ_LOGINBOXSHOW );

	function SHOWSHOW(){
		$(".navMenu").css({ "-webkit-transition" : "all 0.3s" });
		$(".navMenu").removeClass("navMenu--close").addClass("navMenu--open");
		$(".content--before").css({"background" : "rgba(0,0,0,0.7)" , "display" : "block"});	
		$("html").css({"overflow" : "hidden" , "position" : "fixed" , "width" : "100%"});
		$(".navMenu__x").on("click", HIDEHIDE );
		$(".content--before").on("click", HIDEHIDE);
	}
	
	function HIDEHIDE(){
		$("#message-box").find("li").slice(1).remove() ;
		$("#message-box").hide() ;
		$("#member-login-message-box").find("li").slice(1).remove() ;
		$("#member-login-message-box").hide() ;
		$(".navMenu").css({ "-webkit-transition" : "all 0.3s" });
		$(".navMenu").removeClass("navMenu--open").addClass("navMenu--close");
		$(".login_box").removeClass("login_box--open").addClass("login_box--close");
		$(".box_easylogin").removeClass("box_easylogin--open").addClass("box_easylogin--close");
		$(".register_box").removeClass("register_box--open").addClass("register_box--close");
		$(".content--before").css({ "background": "none", "display": "none" });
		//if ($('#box_gameframe').length > 0 && $('#box_gameframe').css('display') == 'none')
			$("html").css({"overflow" : "auto" , "position" : "static" , "width" : "100%"});
		$(".navMenu__x").off("click", HIDEHIDE );
		$(".content--before").off("click", HIDEHIDE );
		$(".lar_click").off("click", HIDEHIDE );
	}
	
	function LOGINBOXSHOW(){
		$(".login_box ").css({ "-webkit-transition" : "all 0.3s" });
		$(".login_box").removeClass("login_box--close").addClass("login_box--open");
		$(".navMenu").removeClass("navMenu--open").addClass("navMenu--close");
		$(".content--before").css({"background" : "rgba(0,0,0,0.7)" , "display" : "block"});
		$("html").css({"overflow" : "hidden" , "position" : "fixed" , "width" : "100%"});
		$(".navMenu__x").on("click", HIDEHIDE );
		$(".lar_click").on("click", HIDEHIDE);
		if ($('#imgVerifyCodeLogin').length > 0)
		{
			var strVerifyCode = $('#imgVerifyCodeLogin').attr('src');
			if (strVerifyCode.indexOf("about:blank") >= 0)
				$('#imgVerifyCodeLogin').attr("src", "/Member/LoginVerifyCode.aspx");
		}
	}
	
	// 快速登入試玩視窗
	//function EZ_LOGINBOXSHOW(){		
	//	$(".box_easylogin").css({ "-webkit-transition" : "all 0.1s" });
	//	$(".box_easylogin").removeClass("box_easylogin--close").addClass("box_easylogin--open");
	//	$(".navMenu").removeClass("navMenu--open").addClass("navMenu--close");
	//	$(".content--before").css({"background" : "rgba(0,0,0,0.7)" , "display" : "block"});
	//	$("html").css({"overflow" : "hidden" , "width" : "100%"});
	//	$(".navMenu__x").on("click", HIDEHIDE );
	//	$(".lar_click").on("click", HIDEHIDE );
	//}
		
	function REGISTERBOXSHOW(){
		if(!IsLogin())
		{
			$(".register_box").css({ "-webkit-transition" : "all 0.3s" });
			$(".register_box").removeClass("register_box--close").addClass("register_box--open");
			$(".navMenu").removeClass("navMenu--open").addClass("navMenu--close");
			$(".login_box").removeClass("login_box--open").addClass("login_box--close");
			$(".content--before").css({"background" : "rgba(0,0,0,0.7)" , "display" : "block"});
			$("html").css({"overflow" : "hidden" , "position" : "fixed" , "width" : "100%"});
			$(".navMenu__x").on("click", HIDEHIDE );
			$(".lar_click").on("click", HIDEHIDE );
			$('#f_strIDRegister').val('') ;
			$('#f_strPasswordRegister').val('');

			if ($('#imgVerifyCodeRegister').length > 0)
			{
				var strVerifyCode = $('#imgVerifyCodeRegister').attr('src') ;
				if (strVerifyCode.indexOf("data:image") >= 0)
					$('#imgVerifyCodeRegister').attr("src", "/Member/RegisterVerifyCode.aspx");
			}
		}
	}
	$(window).on("resize",CLEARSTYLE);
	
	function CLEARSTYLE(){
		if($(window).innerWidth()>812){
			$(".navMenu").attr("style","");
		}
	}
	
	
	$(".navMenu").on("webkitTransitionEnd", NOMOVE);
	
	function NOMOVE(){		
		$(".navMenu").css({ "-webkit-transition" : "all 0 ease-out" });
	}
	
});


/*遊戲訊息通知*/
$(function() {
	var Accordion = function(el, multiple) {
		this.el = el || {};
		this.multiple = multiple || false;

		// Variables privadas
		var links = this.el.find('.news-title');
		// Evento
		links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
	}

	Accordion.prototype.dropdown = function(e) {
		var $el = e.data.el;
			$this = $(this),
			$next = $this.next();

		$next.slideToggle();
		$this.parent().toggleClass('open');

		if (!e.data.multiple) {
			$el.find('.news-submenu').not($next).slideUp().parent().removeClass('open');
		};
	}	

	var accordion = new Accordion($('#accordion'), false);
});
