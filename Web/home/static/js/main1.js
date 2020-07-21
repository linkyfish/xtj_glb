/*首頁_遊戲機台_icon點擊出現按鈕*/
$(window).load(function() {


  var obj = $('.gameicon_other_details');
  var push = $('.gameicon_other_pic').width() / 2;
  $('.gameicon_other_pic').on('mouseover', function(e) {
    e.stopPropagation();
    if(e.target != this) return;
    $(this).addClass('show').siblings().removeClass('show');
  });
  $(document).mouseover(function() {
    $('.gameicon_other_pic').removeClass('show');
  });


  //圖片放大效果
  var popup = document.getElementById("popup");

	if (document.getElementById("image-cover-modal") != null)
	{
	  var imgs = document.getElementById("image-cover-modal").getElementsByTagName("img");
	  var lens = imgs.length;

	  for (var i = 0; i < lens; i++) {
		imgs[i].onclick = function (event) {
		  event = event || window.event;
		  var target = document.elementFromPoint(event.clientX, event.clientY);
		  showBig(target.src);
		}
	  }
	popup.onclick = function () {
		popup.style.display = "none";
	  }
	}

 
  function showBig(src) {
    popup.getElementsByTagName("img")[0].src = src;
    popup.style.display = "block";
  } 

});


  


