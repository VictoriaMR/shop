$(function(){
	STATINFO.init();
})
const STATINFO = {
	init: function() {
		const _this = this;
		_this.getInfo();
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
			}
		}
	}
};