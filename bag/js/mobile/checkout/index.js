const CHECKOUTINDEX = {
	init: function() {
		const _this = this;
		$('.empty-address').on('click', function(){
			ADDRESSBOOK.setCallback();
			ADDRESSBOOK.show();
		});
		$('.set-billing-as-shipping').on('click', function(){
			if ($(this).find('.icon-fangxingweixuanzhong').length > 0) {
				$(this).find('.iconfont').removeClass('icon-fangxingweixuanzhong').addClass('icon-fangxingxuanzhongfill');
				$('.billing-address-item').hide();
				_this.initBillingAddressId();
			} else {
				$(this).find('.iconfont').removeClass('icon-fangxingxuanzhongfill').addClass('icon-fangxingweixuanzhong');
				$('.billing-address-item').show();
				_this.initBillingAddressId();
			}
		});
		//点击筛选地址
		$('.address .address-info-content').on('click', function(){
			const _thisObj = $(this);
			if ($(this).hasClass('empty-address')) {
				return;
			}
			_this.addressShow(function(id){
				_this.reloadAddress(_thisObj, id);
			});
		});
		//关闭筛选地址
		$('#my-address-list').on('click', '.mask,.close-btn', function(){
			_this.addressClose();
		});
		//滚动到底加载页面
		_this.stop = true;
		$('#my-address-list .address-list').scroll(function() {
			if (_this.stop) {
				const scrollTop = $(this)[0].scrollTop;
				const scrollHeight = $(this)[0].scrollHeight;
				const windowHeight = $(this).height();
				if (scrollTop + windowHeight >= scrollHeight - 20) {
					_this.stop = false;
					$(this).append('<div class="page-loading-block">\
						<div></div>\
						<div></div>\
						<div></div>\
						</div>');
					_this.loadAddress();
				}
			}
		});
		//弹窗点击增加地址
		$('#my-address-list .add-new-address').on('click', function(){
			ADDRESSBOOK.setCallback(function(){
				$('#my-address-list .address-list').data('page', 0).html('');
				ADDRESSBOOK.close();
				_this.loadAddress();
			});
			ADDRESSBOOK.show();
		});
		//弹窗地址选择
		$('#my-address-list .address-list').on('click', '.item', function(){
			_this.addressClose();
			if (_this.callback) {
				_this.callback($(this).data('id'));
			}
		});
	},
	addressShow: function(callback) {
		const _this = this;
		_this.callback = callback;
		$('#my-address-list').show().find('.mask').show();
		setTimeout(function(){
			$('#my-address-list .content').addClass('popup');
			//加载地址
			if ($('#my-address-list .address-list .item').length === 0) {
				_this.loadAddress();
			}
		}, 50);
	},
	addressClose: function() {
		$('#my-address-list .content').removeClass('popup');
		$('#my-address-list .mask').fadeOut(200, function(){
			$('#my-address-list').hide();
		});
	},
	loadAddress: function() {
		const _this = this;
		const obj = $('#my-address-list .address-list');
		const page = parseInt(obj.data('page')) + 1;
		const size = parseInt(obj.data('size'));
		if (page == 1) {
			TIPS.loading(obj);
		}
		$.post(URI+'userInfo/getAddress', {page:page, size:size}, function(res){
			if (res.code === '200') {
				obj.data('page', page);
				obj.find('.page-loading-block').remove();
				if (res.data.length > 0) {
					_this.stop = true;
					let html = '';
					for (let i=0; i<res.data.length;i++) {
						html += '<div class="item" data-id="'+res.data[i].address_id+'">\
									<p>'+res.data[i].first_name+' '+res.data[i].last_name+'</p>\
									<p>'+res.data[i].city+' '+res.data[i].state+' '+res.data[i].country+', '+res.data[i].postcode+'</p>\
									<p>'+res.data[i].address1+' '+res.data[i].address2+'</p>\
									<p>'+res.data[i].phone+'</p>';
									if (res.data[i].tax_number !== '') {
										html += '<p><span class="f12 c6">Tax:&nbsp;</span>'+res.data[i].tax_number+'</p>';
									}
									html += '<span class="iconfont icon-xiangyou1"></span>\
								</div>';
					}
					obj.append(html);
				}
			}
			TIPS.loadout(obj);
		});
	},
	reloadAddress: function(obj, id) {
		const pObj = obj.parent();
		const oldId = pObj.data('id');
		if (oldId == id) {
			return true;
		}
		const _this = this;
		TIPS.loading(pObj);
		$.post(URI+'userInfo/getAddressInfo', {id:id}, function(res){
			TIPS.loadout(pObj);
			if (res.code === '200') {
				let html = '<p>'+res.data.first_name+' '+res.data.last_name+'</p>\
							<p>'+res.data.city+' '+res.data.state+' '+res.data.country+', '+res.data.postcode+'</p>\
							<p>'+res.data.address1+' '+res.data.address2+'</p>\
							<p>'+res.data.phone+'</p>';
				if (res.data.tax_number !== '') {
					html += '<p>'+res.data.tax_number+'</p>';
				}
				obj.find('.address-info').html(html);
				if (pObj.hasClass('shipping-address-item') && $('.set-billing-as-shipping .icon-fangxingxuanzhongfill').length > 0) {
					$('.billing-address-item').data('id', id).find('.address-info').html(html);
				}
				pObj.data('id', id).find('input').val(id);
				_this.initBillingAddressId();
			} else {
				TIPS.error(res.message);
			}
		});
	},
	initBillingAddressId: function() {
		if ($('.set-billing-as-shipping .icon-fangxingxuanzhongfill').length > 0) {
			$('.billing-address-item [name="billing_address_id"]').val($('.shipping-address-item').data('id'));
		} else {
			$('.billing-address-item [name="billing_address_id"]').val($('.billing-address-item').data('id'));
		}
	}
};