const CHECKOUTINDEX = {
	init: function(data) {
		const _this = this;
		_this.data = data;
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
		//保险点击
		$('.insurance-btn').on('click', function(){
			const obj = $(this).find('.iconfont').eq(0);
			if (obj.hasClass('icon-fangxingweixuanzhong')) {
				obj.removeClass('icon-fangxingweixuanzhong').addClass('icon-fangxingxuanzhongfill');
				$('input[name="insurance"]').val(1);
			} else {
				obj.removeClass('icon-fangxingxuanzhongfill').addClass('icon-fangxingweixuanzhong');
				$('input[name="insurance"]').val(0);
			}
			_this.calculateOrderFee();
		});
		//保险提示
		$('.insurance-btn .icon-tishi').on('click', function(e){
			e.stopPropagation();
			const obj = $(this).parent().find('.help-tips');
			if (obj.is(':visible')) {
				obj.hide();
			} else {
				obj.show();
			}
		});
		//生成订单
		$('#place-order-btn').on('click', function(){
			const obj = $('.order-summary-content');
			const emailObj = $('#checkout-form input[name="email"]');
			if (emailObj.length > 0) {
				if (emailObj.val()) {
					if (!VERIFY.email(emailObj.val())) {
						TIPS.error(_this.data.email_not_match);
						emailObj.focus();
						return false;
					}
				} else {
					TIPS.error(_this.data.email_empty);
					emailObj.focus();
					return false;
				}
			}
			TIPS.loading();
			$.post(URI+'checkout/createOrder', $('#checkout-form').serializeArray(), function(res) {
				TIPS.loadout();
				if (res.code === '200') {
					window.location.href = res.data;
				} else {
					TIPS.error(res.message);
				}
			});
		});
		//email
		$('#checkout-form input[name="email"]').on('focus', function(){
			$(this).removeClass('error').removeClass('success').parent().find('.iconfont').remove();;
		}).on('blur', function(){
			if (!VERIFY.email($(this).val())) {
				TIPS.error(_this.data.email_not_match);
				$(this).focus();
				return false;
			}
			TIPS.loading();
			const _thisObj = $(this);
			$.post('checkout/setGuestEmail', {email: $(this).val()}, function(res){
				TIPS.loadout();
				if (res.code === '200') {
					_thisObj.removeClass('error').addClass('success');
					_thisObj.parent().append('<span class="iconfont icon-yuanxingxuanzhongfill success"></span>');
				} else {
					_thisObj.removeClass('success').addClass('error');
					_thisObj.parent().append('<span class="iconfont icon-tishifill error"></span>');
					TIPS.error(res.message);
				}
			});
		});
		_this.calculateOrderFee();
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
			TIPS.stop();
		}, 50);
	},
	addressClose: function() {
		$('#my-address-list .content').removeClass('popup');
		$('#my-address-list .mask').fadeOut(200, function(){
			$('#my-address-list').hide();
			TIPS.start();
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
			TIPS.loadout(obj, true);
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
				_this.selectLogistics();
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
	},
	calculateOrderFee: function() {
		const obj = $('.order-summary-content');
		TIPS.loading();
		$.post(URI+'checkout/calculateOrderFee', $('#checkout-form').serializeArray(), function(res) {
			TIPS.loadout();
			if (res.code === '200') {
				let html = '';
				fee_list = res.data.fee_list;
				for (let i=0; i<fee_list.length; i++) {
					html += '<div class="row '+(fee_list[i].type===0?'originalprice-row':'')+'">\
								<p class="name left">'+fee_list[i].name+':</p>\
								<p class="value f600 right">'+fee_list[i].value_format+'</p>\
								<p class="clear"></p>\
							</div>';
				}
				html += '<div class="line mt12"></div>\
							<div class="row mt12 f600">\
								<p class="name left">'+res.data.name+':</p>\
								<p class="value f600 right">'+res.data.value_format+'</p>\
								<p class="clear"></p>\
							</div>';
				obj.find('.order-content').html(html);
			}
		});
	},
	selectLogistics: function() {
		if ($('.shipping-method-content .logistics-list').length === 0) {
			return false;
		}
		const obj = $('.shipping-method-content');
		TIPS.loading(obj);
		$.post(URI+'checkout/selectLogistics', $('#checkout-form').serializeArray(), function(res) {
			TIPS.loadout(obj);
			if (res.code === '200') {
				let html = '';
				for (let i=0; i<res.data.length;i++) {
					html += '<div class="item">\
								<span class="iconfont icon-'+(i===0?'yuanxingxuanzhongfill':'yuanxingweixuanzhong')+'"></span>\
								<div class="row f16 f600">\
									<span>'+res.data[i].name+'</span>\
									<span class="ml12">'+res.data[i].fee+'</span>\
								</div>';
								if (res.data[i].tips) {
									html += '<p class="c6 mt4">'+res.data[i].tips+'</p>';
								}
							html += '</div>';
				}
				obj.find('.logistics-list').html(html);
			}
			console.log(res, 'res')
		});
	}
};$(function(){
	ADDRESSBOOK.init();
})
const ADDRESSBOOK = {
	init: function() {
		const _this = this;
		$('#address-book').on('click', '.top-close-btn,.address-book-mask,.cancel-btn', function(){
			_this.close();
		});
		//去除错误样式
		$('#address-book').on('focus', '.input', function(){
			$(this).parent().removeClass('error');
			$(this).parent().find('.error-tips').remove();
		});
		//选择框内部点击事件阻止
		$('#address-book').on('click', '.selection .selector-content', function(event){
			event.stopPropagation();
		});
		//选择框点击打开
		$('#address-book').on('click', '.selection .selector-icon', function(event){
			event.stopPropagation();
			$(this).parents('.item').removeClass('error');
			$(this).parents('.item').find('.error-tips').remove();
			if ($(this).parent().hasClass('open')) {
				$(this).parent().removeClass('open');
			} else {
				$(this).parent().addClass('open');
				//滚动初始化
				const key = $(this).parent().find('.selector li.active').attr('key');
				if (key) {
					$(this).parent().find('.selector').scrollTop((parseInt(key) - 3)*32*(result_font/100));
				}
			}
		});
		//快速筛选
		$('#address-book').on('input propertychange', '.selection .selector-search input', function(){
			const val = $(this).val();
			if (val == '') {
				$(this).parents('.selector-content').find('.selector li').show();
			} else {
				$(this).parents('.selector-content').find('.selector li').each(function(){
					const text = $(this).text();
					if (text.toUpperCase().indexOf(val.toUpperCase()) === -1) {
						$(this).hide();
					} else {
						$(this).show()
					}
				});
				if ($(this).parents('.selector-content').find('.selector li:visible').length === 0) {
					$(this).parent().find('.empty-selector').show();
				} else {
					$(this).parent().find('.empty-selector').hide();
				}
			}
		});
		//选择选项点击
		$('#address-book').on('click', '.selection .selector li', function() {
			const pObj = $(this).parents('.selection');
			$(this).addClass('active').siblings().removeClass('active');
			const val = $(this).attr('value');
			if ($(this).parent().hasClass('country-selector')) {
				$('#address-book form [name=country_code2]').val(val);
				$('#address-book .phone-code').find('.text').text('+'+$(this).attr('code'));
				_this.initZone(val);
			} else {
				$('#address-book form [name=zone_id]').val(val);
				$('#address-book form [name=state]').val($(this).text());
			}
			pObj.find('.selector-icon span').text($(this).text());
			pObj.removeClass('error').removeClass('open');
		});
		//点击外部收起 选择框
		$('body').on('click', function(){
			$('#address-book .selection.open').removeClass('open');
		});
		//点击选择
		$('#address-book').on('click', '.default-btn', function(){
			const obj = $(this).find('.iconfont');
			if (obj.hasClass('icon-fangxingweixuanzhong')) {
				obj.removeClass('icon-fangxingweixuanzhong').addClass('icon-fangxingxuanzhongfill');
				$(this).find('input').val('1');
			} else {
				obj.removeClass('icon-fangxingxuanzhongfill').addClass('icon-fangxingweixuanzhong');
				$(this).find('input').val('0');
			}
		});
		//点击保存
		$('#address-book .save-btn').on('click', function(){
			let check = true;
			$('#address-book form [required="required"]').each(function(){
				if ($(this).val() === '') {
					$(this).parent().addClass('error');
					let text = $(this).parent().find('.title .text').text();
					if (!text) {
						text = $(this).parents('.item').find('.title .text').text();
					}
					$(this).parent().append('<p class="error-tips">\
						<span>'+text+' is required.</span>\
						<span class="triangle"></span>\
					</p>');
					check = false;
				}
			});
			if (!check) {
				const key = parseInt($('#address-book .error').eq(0).attr('key'));
				const top = key * 61 + (key - 1) * 18 * (100/result_font);
				$('#address-book .dialog .content').animate({'scrollTop': top}, 200);
				return false;
			}
			TIPS.loading($('#address-book .dialog'));
			if (_this.saveCallback) {
				_this.saveCallback($('#address-book form').serializeArray());
				return;
			}
			$.post(URI+'userInfo/editAddress', $('#address-book form').serializeArray(), function(res) {
				if (res.code === '200') {
					TIPS.success(res.message);
					if (_this.callback) {
						TIPS.loadout($('#address-book .dialog'));
						_this.callback();
						return;
					} else {
						setTimeout(function(){
							window.location.reload();
						}, 200);
					}
				} else {
					TIPS.loadout($('#address-book .dialog'));
					TIPS.error(res.message);
				}
			});
		});
	},
	initZone: function(countryCode) {
		const data = this.getZoneList(countryCode);
		$('#address-book form .zone-selection').find('.selection, input[name="state"]').remove();
		if (data.length > 0) {
			let html = '<input type="hidden" name="state" required="required" value="" maxlength="32">\
						<div class="selection mt2">\
						<div class="selector-icon">\
							<span class="e1 f14 pr12">'+appT('please_select')+'</span>\
							<i class="iconfont icon-xiangxia1"></i>\
						</div>\
						<div class="selector-content">\
							<div class="selector-search">\
								<button type="button" class="btn"><i class="iconfont icon-sousuo"></i></button>\
								<input type="input" class="input" placeholder="'+appT('quick_find')+'">\
								<div class="clear"></div>\
								<p class="empty-selector tc c6 f12 mt6 hide">'+appT('result_empty')+'</p>\
							</div>\
							<ul class="selector">';
							for (let i=0; i<data.length; i++) {
								html += '<li class="e1" value="'+data[i].zone_id+'" key="'+i+'">'+data[i].name_en+'</li>';
							}
							html += '</ul>\
						</div>\
					</div>';
			$('#address-book form .zone-selection').append(html);
		} else {
			$('#address-book form .zone-selection').append('<input type="text" name="state" class="input mt2" required="required">');
		}
	},
	show: function(data) {
		$('#address-book').show();
		setTimeout(function(){
			$('#address-book .dialog').addClass('popup');
		}, 50);
		$('#address-book .mask').show();
		TIPS.stop();
		this.pageInit(data);
	},
	pageInit: function(data) {
		if (!data) {
			data = {
				address1: '',
				address2: '',
				city: '',
				country_code2: '',
				address_id: 0,
				is_default: '0',
				is_bill: '0',
				first_name: '',
				last_name: '',
				phone: '',
				postcode: '',
				state: '',
				zone_id: 0
			};
		} else {
			data.phone = data.phone.split(' ')[1];
		}
		if (typeof data.is_default === 'undefined') {
			$('#address-book form .default-btn').hide();
		}
		for (const i in data) {
			if (typeof data[i] == 'undefined') data[i] = '';
			const obj = $('#address-book form [name="'+i+'"]');
			obj.val(data[i]);
			switch (i) {
				case 'country_code2':
					$('#address-book form .country-selector li[value="'+data[i]+'"]').trigger('click');
					if (data.zone_id) {
						$('#address-book form .zone-selection').find('li[code="'+data.zone_id+'"]').trigger('click');
					} else {
						$('#address-book form .zone-selection').find('input').attr('value', data.state);
					}
					break;
				case 'is_default':
				case 'is_bill':
					if (data[i] === '1') {
						obj.parent().find('.iconfont').removeClass('icon-fangxingweixuanzhong').addClass('icon-fangxingxuanzhongfill');
					} else {
						obj.parent().find('.iconfont').removeClass('icon-fangxingxuanzhongfill').addClass('icon-fangxingweixuanzhong');
					}
					break;
			}
		}
		if (data.first_name) {
			$('#address-book .list-title .title').text(appT('edit_address'));
		} else {
			$('#address-book .list-title .title').text(appT('add_address'));
		}
	},
	loadData: function(id) {
		const _this = this;
		_this.show();
		TIPS.loading($('#address-book .dialog'));
		$.post(URI+'userInfo/getAddressInfo', {id: id}, function(res){
			if (res.code === '200') {
				_this.pageInit(res.data);
			} else {
				TIPS.message(res.message);
			}
			TIPS.loadout($('#address-book .dialog'), true);
		});
	},
	close: function() {
		$('#address-book .dialog').removeClass('popup');
		$('#address-book .mask').fadeOut(300, function(){
			$('#address-book').hide();
		});
		if (!this.callback) {
			TIPS.start();
		}
	},
	getZoneList: function (countryCode2){
		let tempZoneList = [];
		if (typeof countryCode2 !== 'string') {
			return tempZoneList;
		}
		if (typeof zone_list[countryCode2] !== 'undefined') {
			tempZoneList = zone_list[countryCode2];
		}
		return tempZoneList;
	},
	setCallback: function(callback) {
		this.callback = callback;
	},
	setSaveCallback: function(callback) {
		this.saveCallback = callback;
	},
};