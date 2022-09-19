$(function(){
	//点击切换大图
	$('.spu-page').on('click', '.image-list li', function(){
		if ($(this).hasClass('selected')) {
			return false;
		}
		$(this).addClass('selected').siblings().removeClass('selected');
		PRODUCT_INFO.imageSelected();
	});
	//小图切换
	$('.spu-page').on('click', '.small-image .movement', function(){
		if ($(this).hasClass('disabled')) {
			return false;
		}
		var obj = $('.spu-page .image-list');
		var simgleW = obj.find('li').eq(0).width()+4;
		if ($(this).find('.icon-xiangyou1').length > 0) {
			obj.find('li.selected').next().addClass('selected').siblings().removeClass('selected');
		} else {
			obj.find('li.selected').prev().addClass('selected').siblings().removeClass('selected');
		}
		var index = obj.find('li.selected').index();
		if (index > 2 && index < obj.find('li').length - 2) {
			var left = (index - 2) * (simgleW + 10);
			obj.css({left: -left});
		} else if (index <= 2) {
			obj.css({left: 0});
		}
		PRODUCT_INFO.imageSelected();
	});
	//属性点击
	$('.spu-page').on('click', '.attv-list li', function(){
		if ($(this).hasClass('disabled')) {
			return false;
		}
		if ($(this).hasClass('selected')) {
			$(this).removeClass('selected');
		} else {
			$(this).addClass('selected').siblings().removeClass('selected');
		}
		PRODUCT_INFO.skuInit();
	});
	//数量加减
	$('.spu-page').on('click', '.qty-content .iconfont', function(){
		if ($(this).hasClass('disabled')) {
			return false;
		}
		var obj = $(this).parent().find('input');
		var val = parseInt(obj.val());
		if ($(this).hasClass('icon-jianhao')) {
			val = val > 1 ? val - 1 : 1;
		} else {
			var stock = $(this).parent().data('stock');
			val = val < stock ? val + 1 : stock;
		}
		obj.val(val);
		PRODUCT_INFO.qtyInit();
	});
	//输入数量直接触发
	$('.spu-page').on('blur', '.qty-content input', function(){
		var val = parseInt($(this).val());
		var stock = $(this).parent().data('stock');
		if (stock < val) {
			$(this).val(stock);
		}
		if (val <= 0) {
			$(this).val(1);
		}
		PRODUCT_INFO.qtyInit();
	});
	//加入购物车
	$('.spu-page').on('click', '.btn-add-cart', function(){
		if (PRODUCT_INFO.checkSku()) {
			TIPS.loading(false);
			PRODUCT_INFO.addToCart(function(res){
				CART.count(res.data.cart_count);
				TIPS.loadout(false);
			});
		}
	});
});
var PRODUCT_INFO = {
	init: function(data, spuId, skuId) {
		this.info = data;
		this.spuId = spuId;
		this.skuId = skuId;
		this.obj = $('.spu-page');
	},
	imageSelected: function(){
		var obj = $('.spu-page .small-image');
		$('.spu-page .big-image img').attr('src', obj.find('.image-list li.selected img').attr('src').replace('/200', '/600'));
		var index = obj.find('.image-list li.selected').index();
		if (index > 0) {
			obj.find('.left-movement').removeClass('disabled');
		} else {
			obj.find('.left-movement').addClass('disabled');
		}
		if (index < obj.find('.image-list li').length - 1) {
			obj.find('.right-movement').removeClass('disabled');
		} else {
			obj.find('.right-movement').addClass('disabled');
		}
	},
	skuInit: function() {
		var _this = this;
		var skuMapKey = [];
		var obj = _this.obj.find('.attr-item');
		obj.each(function(){
			var id = $(this).data('id');
			$(this).find('li').each(function(){
				if ($(this).hasClass('selected')) {
					skuMapKey.push(id+':'+$(this).data('id'));
					return false;
				}
			});
		});
		skuMapKey = skuMapKey.join(';')+';';
		_this.skuId = null;
		if (_this.info.skuMap[skuMapKey]) {
			var skuInfo = _this.info.sku[_this.info.skuMap[skuMapKey]];
			console.log(skuInfo, 'skuInfo')
			_this.skuId = skuInfo.sku_id;
			var contentObj = _this.obj.find('.info-content');
			contentObj.find('.name').text(skuInfo.name);
			contentObj.find('.stock').text(skuInfo.stock>0?'In Stock':'Out Of Stock');
			contentObj.find('.num').text('SKU: '+skuInfo.sku_id);
			contentObj.find('.price').text(skuInfo.price_format);
			if (contentObj.find('.original-price').lenght > 0 && skuInfo.original_price > skuInfo.price) {
				contentObj.find('.original-price').text(skuInfo.original_price_format);
				contentObj.find('.discount').text((1-skuInfo.price/skuInfo.original_price).toFixed(2)*100+'% OFF');
			} else {
				contentObj.find('.original-price').remove();
				contentObj.find('.discount').remove();
			}
			contentObj.find('.qty-content').data('stock', skuInfo.stock);
			if (skuInfo.image) {
				_this.obj.find('.big-image img').attr('src', skuInfo.image.replace('/400', '/600'));
				_this.obj.find('.small-image li').removeClass('selected');
			}
			url(skuInfo.url, skuInfo.name);
		}
		//属性按钮初始化
		_this.obj.find('.attv-list li.disabled').removeClass('disabled');
		var attrObj = _this.obj.find('.attv-list li.selected');
		if (attrObj.length > 0) {
			attrObj.each(function(){
				var id = $(this).data('id');
				var filterMap = _this.info.filterMap[id];
				$(this).parents('.attr-item').siblings().find('.attv-list li').each(function(){
					if (filterMap.indexOf($(this).data('id').toString()) < 0) {
						$(this).addClass('disabled');
					}
				});
			});
		}
	},
	qtyInit: function() {
		var obj = this.obj.find('.qty-content');
		var stock = obj.data('stock');
		var val = parseInt(obj.find('input').val());
		if (val <= 1 || stock <= 1) {
			obj.find('.icon-jianhao').addClass('disabled');
		} else {
			obj.find('.icon-jianhao').removeClass('disabled');
		}
		if (val >= stock || stock <= 1) {
			obj.find('.icon-jiahao1').addClass('disabled');
		} else {
			obj.find('.icon-jiahao1').removeClass('disabled');
		}
	},
	checkSku: function() {
		var check = true;
		this.obj.find('.attr-content .attr-item').each(function(){
			if ($(this).find('.attv-list li.selected').length === 0) {
				$(this).find('.attr-name-content').addClass('bounce').append('<p class="error-tips">\
						<span>The '+($(this).find('.attr-name-content .attr-name').text())+' is required.</span>\
						<span class="triangle"></span>\
					</p>');
				check = false;
			}
		});
		if (!check) {
			this.start();
		}
		return check;
	},
	start: function() {
		var _this = this;
		clearInterval(_this.interval);
		_this.interval = setTimeout(function(){
			_this.obj.find('.attr-name-content').removeClass('bounce');
			_this.obj.find('.attr-name-content .error-tips').remove();
			clearInterval(_this.interval);
		}, 1500);
	},
	addToCart: function(callback){
		var _this = this;
		if (!_this.skuId) {
			return false;
		}
		var num = _this.obj.find('.qty-content input').val();
		$.post('/cart/addToCart', {num:num, sku_id:_this.skuId}, function(res){
			if (res.code == '200') {
				TIPS.success(res.msg);
			} else {
				TIPS.error(res.msg);
			}
			if (callback) callback(res);
		});
	}
};