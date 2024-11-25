/* index_index */
$(function(){
	INDEX.init();
});
const INDEX = {
	init: function() {
		//左1切换大小
		$('#index-page .left-one .toggle').on('click', function() {
			var pObj = $(this).parents('.nav-left');
			let type = 0;
			if (pObj.hasClass('left-close')) {
				pObj.removeClass('left-close');
			} else {
				pObj.addClass('left-close');
				type = 1
			}
			post('', {opn: 'setLeft', key: 'left_type', value: type});
		});
		//左侧悬浮标题
		$('#index-page .left-one [data-title]').on('mouseover', function(){
			if ($('.nav-left.left-close').length == 0) return false;
			var offTop = $(this).position().top;
			var oh = $(this).height();
			var diff = (oh - 24) / 2;
			$(this).parent().find('.tooltips').remove();
			$(this).parent().append('<div class="tooltips" style="top:'+(parseInt(offTop)+diff)+'px">'+$(this).data('title')+'</div>');
		}).on('mouseleave', function(){
			$(this).parent().find('.tooltips').remove();
		});
		//选择点击
		$('#index-page .left-content .nav-content li').on('click', function(){
			if ($(this).hasClass('select')) return false;
			$(this).addClass('select').siblings().removeClass('select');
			$('#index-page .left-two .item li').removeClass('select');
			$('#index-page .left-two .nav-son-content .item').hide();
			$('#index-page .left-two .nav-son-content [data-for="'+$(this).data('to')+'"]').show();
			$('#index-page .left-two .title .text').text($(this).data('title'));
			$('#index-page .left-two .title .text').find('.glyphicon').remove();
			$('#index-page .left-two .title .text').append($(this).find('.glyphicon').clone());
			post('', {opn: 'setLeft', key: 'last_group', value: $(this).data('to')});
		});
		$('#index-page .left-content .nav-son-content li').on('click', function(){
			$(this).addClass('select').siblings().removeClass('select');
			var src = $(this).data('src');
			$('#href-to-iframe').attr('src', src+'?iframe=1');
			post('', {opn: 'setLeft', key: 'last_url', value: src});
		});
		if ($('#index-page .left-content .auto-select').length > 0) {
			$('#index-page .left-content .auto-select').trigger('click');
		} else {
			$('#index-page .left-content .nav-content li').eq(0).trigger('click');
			$('#index-page .left-content .nav-son-content .item:visible li').eq(0).trigger('click');
		}
	}
};
/* index_index */
const ws_socket = {
	init: function() {
		const _this = this;
		if (!_this.ws_status) { 
			_this.socket = new WebSocket('wss://'+location.host+'/wss');
			// 连接完成
			_this.socket.onopen = function(e) {
				 _this.ws_status = true;
				console.log('socket opening...')
				_this.startping();
			};
			// 接收信息
			_this.socket.onmessage = function(e) {
				console.log(e);
			};
			// 关闭
			_this.socket.onclose = function(e) {
				_this.ws_status = false;
				clearInterval(_this.interval);
				console.log(e);
				setTimeout(function(){
					_this.init();
				}, 2000);
			};
			// 错误
			_this.socket.onerror = function(e) {
				console.log(e);
			};
		}
	},
	startping: function() {
		const _this = this;
		this.interval = setInterval(function() {
			_this.send('ping', 'ping');
		}, 25000);
	},
	send: function(type, data) {
		let param = new Array();
		param.push(type);
		param.push(data);
		this.socket.send(JSON.stringify(param));
	}
};
document.addEventListener('visibilitychange', function() {
	if (!document.hidden) {
		ws_socket.init();
	}
});
