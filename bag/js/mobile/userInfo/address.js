const ADDRESS = {
	init: function() {
		const _this = this;
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
		//新增地址
		$('#address-page .add-new-address').on('click', function(){
			ADDRESSBOOK.show();
		});
		//编辑地址
		$('.address-list').on('click', '.item .edit-btn', function(){
			const id = $(this).parents('.item').data('id');
			ADDRESSBOOK.loadData(id);
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
		if ($('.address-list').length > 0 && $('.address-list').height() > $(window).height()) {
			_this.initLoad();
		}
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
				if (scrollTop + windowHeight >= scrollHeight - 20) {
					_this.stop = false;
					$('.address-content').append('<div class="page-loading-block">\
						<div></div>\
						<div></div>\
						<div></div>\
						</div>');
					_this.getPgae();
				}
			}
		});
	},
	getPgae: function() {
		const _this = this;
		const obj = $('.address-list');
		const page = parseInt(obj.data('page')) + 1;
		const size = parseInt(obj.data('size'));
		$.post(URI+'userInfo/getAddress', {page:page, size:size}, function(res){
			if (res.code === '200') {
				obj.data('page', page);
				$('.address-content').find('.page-loading-block').remove();
				if (res.data.length > 0) {
					_this.stop = true;
					let html = '';
					for (let i=0; i<res.data.length;i++) {
						html += '<li class="item'+(res.data[i].is_default==='1'?' active':'')+'" data-id="'+res.data[i].address_id+'">\
							<div class="info">\
								<p class="e2">'+res.data[i].first_name+' '+res.data[i].last_name+'</p>\
								<p class="e2">'+res.data[i].phone+'</p>\
								<p class="e2">'+res.data[i].address1+' '+res.data[i].address2+'</p>\
								<p class="e2">'+res.data[i].city+' '+res.data[i].state+' '+res.data[i].country+' '+res.data[i].postcode+'</p>';
								if (res.data[i].tax_number){
									html += '<p class="e2">'+res.data[i].tax_number+'</p>';
								}
						html += '<button class="btn24 default-btn'+(res.data[i].is_default==='1'?' active':'')+'">DEFAULT</button>\
							</div>\
							<div class="btn-content mt14">\
								<button class="btn24 btn-black edit-btn">Edit</button>\
								<button class="btn24 ml16 delete-btn">Delete</button>\
							</div>\
						</li>';
					}
					obj.append(html);
				}
			}
		});
	}
};