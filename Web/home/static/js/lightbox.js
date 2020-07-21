$(document).ready(function () {
	// 燈箱效果綁定有 class="iframe" 的連結
	var cBoxHeight = "80%",
		cBoxWidth = "80%";

	$(".iframe").colorbox({iframe: true, width: cBoxWidth, height: cBoxHeight, fixed: true}) ;

	$(window).resize(function () {
		$.colorbox.resize({width: cBoxWidth, height: cBoxHeight}) ;
	});
});