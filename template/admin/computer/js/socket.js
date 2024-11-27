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
