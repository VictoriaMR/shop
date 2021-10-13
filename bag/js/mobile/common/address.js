$(function(){
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