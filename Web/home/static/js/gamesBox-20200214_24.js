// 下方遊戲區塊動態
$(function(){
		// 預設標題區塊 .box .box02 的 top
		var _titleHeight = 0;
		$('.hotGameBox01').each(function(){
			// 先取得區塊的高及標題區塊等資料
			var $this = $(this), 
				_height = $this.height(), 
				$pic_con = $('.hotGameQRCode', $this),
				_pic_conHeight = $pic_con.outerHeight(true),
				// 顯示速度
				_speed = 350;
			
			// 當滑鼠移動到區塊上時
			$this.hover(function(){
				// 讓 $pic_con 往上移動
				$pic_con.stop().animate({
					top: _height - _pic_conHeight
				}, _speed);
			}, function(){
				// 讓 $pic_con 移回原位
				$pic_con.stop().animate({
					top: _height - _titleHeight
				}, _speed);
			});
		});
		// 預設標題區塊 .box .box02 的 top
		var _titleHeight = 0;
		$('.hotGameBox02').each(function(){
			// 先取得區塊的高及標題區塊等資料
			var $this = $(this), 
				_height = $this.height(), 
				$pic_con = $('.hotGameQRCode', $this),
				_pic_conHeight = $pic_con.outerHeight(true),
				// 顯示速度
				_speed = 350;
			
			// 當滑鼠移動到區塊上時
			$this.hover(function(){
				// 讓 $pic_con 往上移動
				$pic_con.stop().animate({
					top: _height - _pic_conHeight
				}, _speed);
			}, function(){
				// 讓 $pic_con 移回原位
				$pic_con.stop().animate({
					top: _height - _titleHeight
				}, _speed);
			});
		});
		// 預設標題區塊 .box .box02 的 top
		var _titleHeight = 0;
		$('.hotGameBox03').each(function(){
			// 先取得區塊的高及標題區塊等資料
			var $this = $(this), 
				_height = $this.height(), 
				$pic_con = $('.hotGameQRCode', $this),
				_pic_conHeight = $pic_con.outerHeight(true),
				// 顯示速度
				_speed = 350;
			
			// 當滑鼠移動到區塊上時
			$this.hover(function(){
				// 讓 $pic_con 往上移動
				$pic_con.stop().animate({
					top: _height - _pic_conHeight
				}, _speed);
			}, function(){
				// 讓 $pic_con 移回原位
				$pic_con.stop().animate({
					top: _height - _titleHeight
				}, _speed);
			});
		});
		// 預設標題區塊 .box .box02 的 top
		var _titleHeight = 0;
		$('.box01').each(function(){
			// 先取得區塊的高及標題區塊等資料
			var $this = $(this), 
				_height = $this.height(), 
				$pic_con = $('.QRCode', $this),
				_pic_conHeight = $pic_con.outerHeight(true),
				// 顯示速度
				_speed = 350;
			
			// 當滑鼠移動到區塊上時
			$this.hover(function(){
				// 讓 $pic_con 往上移動
				$pic_con.stop().animate({
					top: _height - _pic_conHeight
				}, _speed);
			}, function(){
				// 讓 $pic_con 移回原位
				$pic_con.stop().animate({
					top: _height - _titleHeight
				}, _speed);
			});
		});
		// 預設標題區塊 .box .box02 的 top
		var _titleHeight = 0;
		$('.box02').each(function(){
			// 先取得區塊的高及標題區塊等資料
			var $this = $(this), 
				_height = $this.height(), 
				$pic_con = $('.QRCode', $this),
				_pic_conHeight = $pic_con.outerHeight(true),
				// 顯示速度
				_speed = 350;
			
			// 當滑鼠移動到區塊上時
			$this.hover(function(){
				// 讓 $pic_con 往上移動
				$pic_con.stop().animate({
					top: _height - _pic_conHeight
				}, _speed);
			}, function(){
				// 讓 $pic_con 移回原位
				$pic_con.stop().animate({
					top: _height - _titleHeight
				}, _speed);
			});
		});
		// 預設標題區塊 .box .box02 的 top
		var _titleHeight = 0;
		$('.box03').each(function(){
			// 先取得區塊的高及標題區塊等資料
			var $this = $(this), 
				_height = $this.height(), 
				$pic_con = $('.QRCode', $this),
				_pic_conHeight = $pic_con.outerHeight(true),
				// 顯示速度
				_speed = 350;
			
			// 當滑鼠移動到區塊上時
			$this.hover(function(){
				// 讓 $pic_con 往上移動
				$pic_con.stop().animate({
					top: _height - _pic_conHeight
				}, _speed);
			}, function(){
				// 讓 $pic_con 移回原位
				$pic_con.stop().animate({
					top: _height - _titleHeight
				}, _speed);
			});
		});
		// 預設標題區塊 .box .box02 的 top
		var _titleHeight = 0;
		$('.box04').each(function(){
			// 先取得區塊的高及標題區塊等資料
			var $this = $(this), 
				_height = $this.height(), 
				$pic_con = $('.QRCode', $this),
				_pic_conHeight = $pic_con.outerHeight(true),
				// 顯示速度
				_speed = 350;
			
			// 當滑鼠移動到區塊上時
			$this.hover(function(){
				// 讓 $pic_con 往上移動
				$pic_con.stop().animate({
					top: _height - _pic_conHeight
				}, _speed);
			}, function(){
				// 讓 $pic_con 移回原位
				$pic_con.stop().animate({
					top: _height - _titleHeight
				}, _speed);
			});
		});
});
