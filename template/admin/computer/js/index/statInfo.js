$(function(){
	STATINFO.init();
})
const STATINFO = {
	timer: null,
	init: function() {
		const _this = this;
		_this.getInfo();
		if (_this.timer) {
			clearInterval(_this.timer);
		}
		_this.timer = setInterval(function() {
			_this.getInfo();
		}, 10000);
	},
	getInfo: function() {
		const _this = this;
		post('', {opn: 'getSystemInfo'}, function(res){
			if (res.code) {
				_this.initdata(res.data);
			} else {
				showTips(res);
			}
		});
	},
	initdata: function(data) {
		if (data) {
			for (const i in data) {
				$('#' + i).text(data[i]);
				$('#' + i + '_detail').text(data[i]);
				const val = parseFloat(data[i]);
				if (!isNaN(val)) {
					$('#' + i + '_bar').css('width', val + '%');
				}
			}
		}
	}
};