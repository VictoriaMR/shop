var STATINFO = {
	init: function() {
		this.getInfo();
		var setInterval = window.setInterval(function(){
			STATINFO.getInfo();
		}, 5000);
	},
	getInfo: function() {
		post(URI + 'index/statInfo', {opn: 'getSystemInfo'}, function(data){
			STATINFO.initdata(data);
		});
	},
	initdata: function(data) {
		if (data) {
			for (var i in data) {
				$('#' + i).text(data[i]);
			}
		}
	}
};