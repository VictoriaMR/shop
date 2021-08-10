const ADDRESS = {
	init: function() {
		//设置默认
		$('.address-list').on('click', '.item .default-btn', function(){
			if ($(this).hasClass('active')) {
				return false;
			}
			const id = $(this).parents('.item').data('id');
			TIPS.confirm('Make this address default?', function(){
				TIPS.loading($('#confirm-modal .content'));
				$.post(URI+'userInfo/setAddressDefault', {id: id}, function(res){
					if (res.code === '200') {
						window.location.reload();
					} else {
						TIPS.loadout($('#confirm-modal .content'));
						TIPS.error(res.message);
					}
				});
			});
		});
		//删除地址
		$('.address-list').on('click', '.item .delete-btn', function(){
			const id = $(this).parents('.item').data('id');
			TIPS.confirm('Sure delete this address?', function(){
				TIPS.loading($('#confirm-modal .content'));
				$.post(URI+'userInfo/deleteAddress', {id: id}, function(res){
					if (res.code === '200') {
						window.location.reload();
					} else {
						TIPS.loadout($('#confirm-modal .content'));
						TIPS.error(res.message);
					}
				});
			});
		});
		_this.initLoad();
		
	},
	initLoad: function() {
		const _this = this;
		_this.stop = true;
		//滚动到底加载页面
		$(window).scroll(function() {
			if (_this.stop) {
				const scrollTop = $(this).scrollTop();
				const scrollHeight = $(document).height();
				const windowHeight = $(this).height();
				if (scrollTop + windowHeight == scrollHeight - 20) {
					console.log("已经到最底部了！");
				}
			}
		});
	}
};