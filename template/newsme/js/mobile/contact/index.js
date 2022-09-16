const CONTACT = {
	init: function(){
		const _this = this;
		_this.page = 1;
		const key = localStorage.getItem('contact_key');
		if (key) {
			_this.initMessage(key);
		} else {
			$.post('/contact/create', {}, function(res){
				if (res.code === '200') {
					localStorage.setItem('contact_key', res.data);
					_this.initMessage(res.data);
				} else {
					TIPS.loadout();
				}
			});
		}
	},
	initMessage: function(key) {
		const _this = this;
		$.post('/contact/message', {page: _this.page, key: key}, function(res){
			TIPS.loadout();
			if (res.code === '200') {
				console.log(res)
			}
		});
	}
};