$(function(){
	STATINFO.init();
})
const STATINFO = {
	init: function() {
		const _this = this;
		_this.getInfo();
		clearInterval(_this.setInterval);
		_this.setInterval = window.setInterval(function(){
			_this.getInfo();
		}, 5000);
	},
	getInfo: function() {
		const _this = this;
		post(URI + 'index/statInfo', {opn: 'getSystemInfo'}, function(data){
			_this.initdata(data);
		});
	},
	initdata: function(data) {
		if (data) {
			for (const i in data) {
				$('#' + i).text(data[i]);
			}
		}
	}
};