const PRODUCT = {
	init: function(data) {
		const _this = this;
		_this.spuId = data.spuId;
		_this.skuId = data.skuId;
		_this.sku = data.sku;
		_this.skuMap = data.skuMap;
		_this.filterMap = data.filterMap
		_this.name = data.name;
		_this.url = data.url;
		_this.stock = data.stock;
		_this.price = data.price;
		_this.originalPrice = data.originalPrice;
		$('.like-block').on('click', function(){
			TIPS.loading();
			$.post(URI+'userInfo/collect', {spu_id: _this.spuId}, function(res){
				TIPS.loadout();
				if (res.code === '200') {
					if (res.data === 1) {
						$('.icon-xihuan').removeClass('icon-xihuan').addClass('icon-xihuanfill');
					} else {
						$('.icon-xihuanfill').removeClass('icon-xihuanfill').addClass('icon-xihuan');
					}
				} else if (res.code === '10001') {
					window.location.href = URI+'login.html';
				}
			});
		});
		//sku点击
		$('#sku-select').on('click', function() {
			$('#sku-select-modal').show().find('.mask').show();
			$('#sku-select-modal .dialog').addClass('popup');
			TIPS.stop();
		});
		//属性点击
		$('#description').on('click', function() {
			$('#description-modal').show().find('.mask').show();
			$('#description-modal .dialog').addClass('popup');
			TIPS.stop();
		});
		//关闭弹窗
		$('.m-modal .mask,.m-modal .icon-guanbi2').on('click', function(){
			$('.m-modal .dialog').removeClass('popup');
			$('.m-modal .mask').fadeOut(300, function(){
				TIPS.start();
				$(this).parent().hide();
			});
		});
		//数量加减
		$('.quantity button').on('click', function(){
			const pObj = $(this).parent();
			const stock = parseInt(pObj.data('stock'));
			let num = parseInt(pObj.find('.num').val());
			if (stock === 1) {
				num = 1;
			} else {
				if ($(this).hasClass('plus')) {
					num = num + 1;
				} else {
					num = num - 1;
				}
				if (num >= stock) {
					num = stock;
				}
			}
			pObj.find('.num').val(num);
			_this.initQuantity();
		});
		$('.quantity .num').on('blur', function(){
			_this.initQuantity();
		});
		//添加购物车
		$('.m-modal .add-to-cart').on('click', function(){
			let check = true;
			$('.sku-attr-list .attr-item').each(function(){
				if ($(this).find('li.active').length === 0) {
					_this.attrError($(this), 'Please select '+$(this).find('.title').text());
					check = false;
				}
			});
			if (!check) {
				_this.errorTipsTimeout();
				return false;
			}
			if (!_this.skuId) {
				return false;
			}
			_this.addToCart($('.quantity .num').val());
		});
		//属性点击
		$('.sku-attr-list .attr-item li').on('click', function(){
			//只有一个属性 必选
			if ($(this).parent().find('li').length === 1) {
				return false;
			}
			if ($(this).hasClass('active')) {
				return false;
			}
			$(this).addClass('active').siblings().removeClass('active');
			_this.skuInit();
		});
		//底部按钮添加购物车
		$('.cart-bottom .add-to-cart').on('click', function(){
			if (!_this.skuId) {
				$('#sku-select').trigger('click');
				return false;
			}
			_this.addToCart(1);
		});
		//checkout 按钮
		$('.checkout-btn').on('click', function(){
			if (!_this.skuId) {
				$('#sku-select').trigger('click');
				return false;
			}
			TIPS.loading();
			const quantity = $('.quantity .num').val();
			$.post(URI+'product/check', {sku_id: _this.skuId, quantity: quantity}, function(res) {
				if (res.code === '200') {
					window.location.href = URI+'checkout?id='+_this.skuId+'&quantity='+quantity;
				} else {
					TIPS.loadout();
					TIPS.error(res.message);
				}
			});
		});
	},
	addToCart: function(num){
		TIPS.loading();
		$.post(URI+'cart/addToCart', {sku_id: this.skuId, num: num}, function(res) {
			TIPS.loadout();
			if (res.code === '200') {
				TIPS.success(res.message);
				CART.init()
			} else if (res.code === '10001') {
				window.location.href = URI+'login.html';
			} else {
				TIPS.error(res.message);
			}
		});
	},
	skuInit: function() {
		const obj = $('.sku-attr-list .attr-item');
		obj.find('li').removeClass('disabled').attr('disabled', false);
		let skuMapKey = [];
		let selectText = [];
		let filterMapKey = [];

		obj.each(function(){
			let selected = false;
			const id = $(this).data('id');
			$(this).find('li').each(function(){
				if ($(this).hasClass('active')) {
					selected = true;
					skuMapKey.push(id+':'+$(this).data('id'));
					selectText.push($(this).attr('title'));
					filterMapKey.push($(this).data('id'));
					return;
				}
			});
			if (!selected) {
				selectText.push($(this).find('.title').text());
			}
		});
		skuMapKey = skuMapKey.join(';')+';';
		selectText = selectText.join(' ');
		filterMapKey = filterMapKey.join(':');
		$('#sku-select-modal .sku-pro-info .select-text .text').text(selectText);
		$('#sku-select .text .attr-text').text(selectText);

		let name = this.name;
		let url = this.url;
		let image = this.image;
		let stock = this.stock;
		let price = this.price;
		let originalPrice = this.originalPrice;
		this.skuId = null;
		if (this.skuMap[skuMapKey]) {
			const skuInfo = this.sku[this.skuMap[skuMapKey]];
			this.skuId = skuInfo['sku_id'];
			name = skuInfo.name;
			url = skuInfo.url;
			image = skuInfo.image;
			stock = skuInfo.stock;
			price = skuInfo.price_format;
			originalPrice = skuInfo.original_price_format;
		}
		$('#sku-select-modal .quantity').data('stock', stock);
		$('#sku-select-modal .sku-image-block .sku-image img').attr('src', image);
		$('#sku-select-modal .sku-image-block .price').text(price);
		$('#sku-select-modal .sku-image-block .original_price').text(originalPrice);
		$('#sku-select-modal .sku-image-block .stock .number').text(stock);
		//数量按钮初始化
		this.initQuantity();
		//url和标题替换
		if (this.skuId) {
			if (window.history.replaceState) {
				window.history.replaceState(name, name, url);
				$('head title').text(name);
			}
		} else {
			if (window.history.pushState) {
				window.history.pushState(name, name, url);
				$('head title').text(name);
			}
		}
		//属性按钮初始化
		const filterMap = this.filterMap[filterMapKey];
		obj.each(function(){
			if ($(this).find('.active').length === 0) {
				$(this).find('li').each(function(){
					const id = $(this).data('id');
					if (filterMap.indexOf(id) >= 0) {
						$(this).removeClass('disabled').attr('disabled', false);
					} else {
						$(this).addClass('disabled').attr('disabled', true);
					}
				});
			}
		});
		return true;
	},
	attrError: function(obj, text){
		if (!text) return false;
		obj.find('.error-tips').remove();
		obj.prepend('<p class="error-tips">\
						<span>'+text+'</span>\
						<span class="triangle"></span>\
					</p>');
	},
	errorTipsTimeout: function(){
		clearTimeout(this.errorTipsTimeoutInterval)
		this.errorTipsTimeoutInterval = setTimeout(function(){
			$('.error-tips').fadeOut(300, function(){
				$(this).remove();
			});
		}, 2000);
	},
	initQuantity: function() {
		const pObj = $('.quantity');
		const stock = parseInt(pObj.data('stock'));
		const num = parseInt(pObj.find('.num').val());
		if (stock <= 1) {
			pObj.find('.num').val(1);
			pObj.find('.plus').attr('disabled', true).addClass('disabled');
			pObj.find('.minus').attr('disabled', true).addClass('disabled');
		} else {
			if (num === 1) {
				pObj.find('.minus').attr('disabled', true).addClass('disabled');
				pObj.find('.plus').attr('disabled', false).removeClass('disabled');
			} else {
				pObj.find('.minus').attr('disabled', false).removeClass('disabled');
				if (num >= stock) {
					pObj.find('.plus').attr('disabled', true).addClass('disabled');
				} else {
					pObj.find('.plus').attr('disabled', false).removeClass('disabled');
				}
			}
		}
	}
};