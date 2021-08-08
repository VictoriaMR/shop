const PRODUCT = {
	init: function(data) {
		const _this = this;
		_this.skuId = data.skuId;
		_this.spuId = data.spuId;
		_this.sku = data.sku;
		_this.skuMap = data.skuMap;
		_this.attrMap = data.attrMap;
		_this.skuInit();
		_this.initQuantityBtn();
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
			_this.initQuantityBtn();
		});
		$('.quantity .num').on('blur', function(){
			_this.initQuantityBtn();
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
		});
		//属性点击
		$('.sku-attr-list .attr-item li').on('click', function(){
			//只有一个属性 必选
			if ($(this).parent().find('li').length === 1) {
				return false;
			}
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
			} else {
				$(this).addClass('active').siblings().removeClass('active');
			}
			_this.skuInit();
		});
	},
	skuInit: function() {
		const obj = $('.sku-attr-list .attr-item li');
		obj.removeClass('disabled').attr('disabled', false);
		let mapkey = selectText = '';
		obj.each(function(){
			if ($(this).hasClass('active')) {
				mapkey += $(this).data('id')+':';
				selectText += $(this).attr('title');
			} else {

			}
		});
		if (mapkey.length > 0) {

		} else {

		}
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
	initQuantityBtn: function() {
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