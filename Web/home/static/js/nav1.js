// JavaScript Document
$(function(){
	
	$(".bar").on("click", SHOWSHOW );
	
	function SHOWSHOW(){
		$(".navMenu").css({ "-webkit-transition" : "all 0.3s" });
		$(".navMenu").removeClass("navMenu--close").addClass("navMenu--open");
		$(".content--before").css({"background" : "rgba(60,60,60,0.80)" , "display" : "block"});	
		$("body").css({"overflow" : "hidden"});	
		$(".navMenu__x").on("click", HIDEHIDE );
		$(".content").on("click", HIDEHIDE );

		
	}
	
	function HIDEHIDE(){
		$(".navMenu").css({ "-webkit-transition" : "all 0.3s" });
		$(".navMenu").removeClass("navMenu--open").addClass("navMenu--close");
		$(".content--before").css({"background" : "none" , "display" : "none"});
		$("body").css({"overflow" : "auto"});	
		$(".navMenu__x").off("click", HIDEHIDE );
		$(".content").off("click", HIDEHIDE );
	}
	
	$(window).on("resize",CLEARSTYLE);
	
	function CLEARSTYLE(){
		if($(window).innerWidth()>736){
			$(".navMenu").attr("style","");
		}
	}
	
	
	$(".navMenu").on("webkitTransitionEnd", NOMOVE);
	
	function NOMOVE(){		
		$(".navMenu").css({ "-webkit-transition" : "all 0 ease-out" });
	}
	
});


/*通知功能展開收合*/
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
