$(function(){
	CARTPAGE.init();
});
const CARTPAGE = {
	init: function(){
		const _this = this;
		$('.cart-list.checked .quantity').each(function(){
			_this.initQuantity($(this));
		});
		$('#cart-page .remove-btn').on('click', function(){
			const obj = $(this).parents('li.item');
			const id = obj.data('id');
			TIPS.loading(obj);
			$.post('/cart/remove', {id: id}, function(res){
				if (res.code === '200') {
					TIPS.success(res.message);
					if (obj.parent().find('li.item').length <= 1) {
						setTimeout(function(){
							window.location.reload();
						}, 200);
					} else {
						setTimeout(function(){
							obj.remove();
							TIPS.loadout(obj);
						}, 200);
					}
				} else {
					TIPS.loadout(obj);
					TIPS.error(res.message);
				}
			});
		});
		$('#cart-page .move-cart').on('click', function(){
			const obj = $(this).parents('li.item');
			const id = obj.data('id');
			const check = $(this).hasClass('save-for-later') ? 0 : 1;
			TIPS.loading(obj);
			$.post('/cart/setChecked', {id: id, check: check}, function(res){
				if (res.code === '200') {
					TIPS.success(res.message);
					setTimeout(function(){
						window.location.reload();
					}, 100);
				} else {
					TIPS.loadout(obj);
					TIPS.error(res.message);
				}
			});
		});
		//收藏
		$('.like-block').on('click', function(){
			const obj = $(this).parents('.item');
			const id = $(this).data('id');
			TIPS.loading(obj);
			$.post('/userInfo/wish',{spu_id:id}, function(res) {
				TIPS.loadout(obj);
				if (res.code === '200') {
					if (res.data === 1) {
						$('.like-block[data-id="'+id+'"] .icon-xihuan').removeClass('icon-xihuan').addClass('icon-xihuanfill');
					} else {
						$('.like-block[data-id="'+id+'"] .icon-xihuanfill').removeClass('icon-xihuanfill').addClass('icon-xihuan');
					}
				} else {
					TIPS.error(res.message);
				}
			});
		});
		//数量加减
		$('.quantity .minus,.quantity .plus').on('click', function(){
			const obj = $(this).parent();
			const stock = parseInt(obj.data('stock'));
			let num = parseInt(obj.find('.num').val());
			const pObj = obj.parents('li.item');
			const id = pObj.data('id');
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
			if (num <= 0) {
                num = 1;
            }
			TIPS.loading(pObj);
			$.post('/cart/updateQuantity', {id:id, quantity: num}, function(res){
				TIPS.loadout(pObj);
				if (res.code === '200') {
					pObj.find('.table').removeClass('opac5').find('.btn-error').remove();
					obj.find('.num').val(num);
					_this.initQuantity(obj);
					_this.initSummary();
				} else {
					TIPS.error(res.message);
				}
			});
		});
		//直接输入更改
		$('.quantity .num').on('blur', function(){
			const obj = $(this).parent();
			const stock = parseInt(obj.data('stock'));
			let num = parseInt(obj.find('.num').val());
			const pObj = obj.parents('li.item');
			const id = pObj.data('id');
			if (stock === 1) {
				num = 1;
			} else if (num >= stock) { 
				num = stock;
			}
			if (num <= 0) {
                num = 1;
            }
			obj.find('.num').val(num);
			TIPS.loading(pObj);
			$.post('/cart/updateQuantity', {id:id, quantity: num}, function(res){
				TIPS.loadout(pObj);
				if (res.code === '200') {
					pObj.find('.table').removeClass('opac5').find('.btn-error').remove();
					_this.initQuantity(obj);
					_this.initSummary();
				} else {
					TIPS.error(res.message);
				}
			});
		});
		//编辑
		$('.cart-list.checked .attr-content').on('click', function(){
			const htmlObj = $('#sku-select-modal .contentfill');
			htmlObj.html('');
			TIPS.loading(htmlObj);
			$('#sku-select-modal').show().find('.mask').show();
			$('#sku-select-modal .dialog').addClass('popup');
			const id = $(this).parents('.item').data('id');
			_this.cart_id = id;
			$.post('/cart/editInfo', {id:id}, function(res){
				TIPS.loadout(htmlObj, true);
				if (res.code === '200') {
					_this.initEditPage(res.data);
				} else {
					$('#sku-select-modal .dialog').removeClass('popup');
					$('#sku-select-modal .mask').fadeOut(300, function(){
						TIPS.start();
						$(this).parent().hide();
					});
					TIPS.error(res.message);
				}
			});
		});
		//关闭弹窗
		$('.m-modal .mask,.m-modal .icon-guanbi2').on('click', function(){
			$('.m-modal .dialog').removeClass('popup');
			$('.m-modal .mask').fadeOut(300, function(){
				TIPS.start();
				$(this).parent().hide();
			});
		});
		//属性点击
		$('#sku-select-modal').on('click', '.sku-attr-list .attr-item li', function(){
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
		//点击确认
		$('#sku-select-modal').on('click', '.confirm-btn', function(){
			if (_this.skuId == _this.oldSkuId) {
				$('.m-modal .dialog').removeClass('popup');
				$('.m-modal .mask').fadeOut(300, function(){
					TIPS.start();
					$(this).parent().hide();
				});
				return false;
			}
			TIPS.loading($('#sku-select-modal .dialog'));
			$.post('/cart/edit', {cart_id: _this.cart_id, sku_id:_this.skuId}, function(res){
				if (res.code === '200') {
					TIPS.success(res.message);
					setTimeout(function(){
						window.location.reload();
					}, 100);
				} else {
					TIPS.loadout($('#sku-select-modal .dialog'));
					TIPS.error(res.message);
					$('.m-modal .dialog').removeClass('popup');
					$('.m-modal .mask').fadeOut(300, function(){
						TIPS.start();
						$(this).parent().hide();
					});
				}
			});
		});
		//checkout
		$('#cart-summary .checkout-btn').on('click', function(){
			if ($('.cart-list.checked .item').length === 0) {
				TIPS.error('Sorry, your selected cart product was empty.');
				return false;
			}
			TIPS.loading();
			$.post('/cart/check', {}, function(res){
				TIPS.loadout();
				if (res.code === '200') {
					window.location.href = res.data;
				} else {
					TIPS.error(res.message);
				}
			});
		});
	},
	initEditPage: function(data){
		if (!data) {
			return false;
		}
		let html = select_attr = '';
		this.oldSkuId = data.sku_id;
		this.sku = data.sku;
		this.skuMap = data.skuMap;
		this.filterMap = data.filterMap;
		const skuInfo = data.sku[data.sku_id];
		const skuAttrSelect = data.skuAttv[data.sku_id];
		for (let i=0; i<skuAttrSelect.length; i++) {
			select_attr += data.attv[skuAttrSelect[i]]+' ';
		}
		//头部图片
		html += '<div class="sku-image-block mt10 f0">\
				<div class="sku-image tcell">\
					<img data-src="'+skuInfo.image+'" src="'+'/image/common/noimg.svg" class="lazyload">\
				</div>\
				<div class="sku-pro-info tcell">\
					<p class="product-price">\
						<span class="price">'+skuInfo.price+'</span>\
						<span class="original_price">'+skuInfo.original_price+'</span>\
					</p>\
					<p class="stock c6">\
						<span>STOCK: </span>\
						<span class="number">'+skuInfo.stock+'</span>\
					</p>\
					<p class="select-text c6">\
						<span>SELECT: </span>\
						<span class="text">'+select_attr+'</span>\
					</p>\
				</div>\
			</div>';
		// 属性选项
		html += '<div class="sku-attr-list mt20">';
		for (const i in data.attrMap){
			html += '<div class="item attr-item" data-id="'+i+'">\
					<p class="title">'+data.attr[i]+'</p>\
					<ul class="mt10">';
						for (let j=0; j<data.attrMap[i].length; j++) {
							if (typeof data.attvImage[data.attrMap[i][j]] === 'undefined' || data.attvImage[data.attrMap[i][j]] === '0') {
								html += '<li class="item-text'+(data.attrMap[i].length===1||skuAttrSelect.indexOf(data.attrMap[i][j])>=0?' active':'')+'" data-id="'+data.attrMap[i][j]+'" title="'+data.attv[data.attrMap[i][j]]+'">'+data.attv[data.attrMap[i][j]]+'</li>'
							} else {
								html += '<li class="item-image'+(data.attrMap[i].length===1||skuAttrSelect.indexOf(data.attrMap[i][j])>=0?' active':'')+'" data-id="'+data.attrMap[i][j]+'" title="'+data.attv[data.attrMap[i][j]]+'">\
											<div class="attv-image tcell">\
												<img data-src="'+data.attvImage[data.attrMap[i][j]].url+'" src="'+'/image/common/noimg.svg" class="lazyload">\
											</div>\
										</li>';
							}
						}
			html += '</ul>\
					<div class="clear"></div>\
				</div>';
		}
		html += '</div>';
		$('#sku-select-modal .contentfill').html(html);
		this.skuInit();
	},
	skuInit: function(){
		const obj = $('#sku-select-modal .sku-attr-list .attr-item');
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
		let image, stock, price, originalPrice;
		if (this.skuMap[skuMapKey]) {
			const skuInfo = this.sku[this.skuMap[skuMapKey]];
			this.skuId = skuInfo.sku_id;
			image = skuInfo.image;
			stock = skuInfo.stock;
			price = skuInfo.price;
			originalPrice = skuInfo.original_price;
		}
		$('#sku-select-modal .quantity').data('stock', stock);
		$('#sku-select-modal .sku-image-block .sku-image img').attr('src', image);
		$('#sku-select-modal .sku-image-block .price').text(price);
		$('#sku-select-modal .sku-image-block .original_price').text(originalPrice);
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
	initQuantity: function(pObj) {
		const stock = parseInt(pObj.data('stock'));
		const num = parseInt(pObj.find('.num').val());
		if (stock <= 1) {
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
	},
	initSummary: function(){
		TIPS.loading($('#cart-summary'));
		$.post('/cart/cartSummary', {}, function(res){
			let html = '';
			if (res.code === '200') {
				for (let i=0; i<res.data.length; i++) {
					html += '<li '+(res.data[i].type === 2 ? 'class="f700 f16"':'')+'>\
								<span>'+res.data[i].name+'</span>\
								<span class="right">'+res.data[i].price_format+'</span>\
							</li>';
				}
			}
			$('#cart-summary .content ul').html(html);
			TIPS.loadout($('#cart-summary'));
		});
	}
};