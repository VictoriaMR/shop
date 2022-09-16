$(function(){
	//左侧点击
	$('#left-filter .item .title').on('click', function(){
		var nextObj = $(this).parent().next();
		if (nextObj.hasClass('open')) {
			nextObj.removeClass('open');
			$(this).next().removeClass('icon-xiangshang2').addClass('icon-xiangxia2');
		} else {
			nextObj.addClass('open');
			$(this).next().removeClass('icon-xiangxia2').addClass('icon-xiangshang2');
		}
	});
	//左侧自动滚动到中间
	$('#left-filter .item .icon-fangxingxuanzhongfill').each(function(){
		var index = $(this).parent().index();
		if (index > 5) {
			$(this).parent().parent().scrollTop((index-3)*48);
		}
	});
	//排序框点击
	$('#right-list .sortby').on('click', function(){
		var obj = $(this).find('.show-sort .iconfont');
		if (obj.hasClass('icon-xiangxia2')) {
			obj.removeClass('icon-xiangxia2').addClass('icon-xiangshang2');
			$(this).find('.sort-list').slideDown(200);
		} else {
			obj.removeClass('icon-xiangshang2').addClass('icon-xiangxia2');
			$(this).find('.sort-list').slideUp(200);
		}
	});
	//快速预览点击
	$('#right-list .product-list li .quickview').on('click', function(){
		var obj = $(this).parent();
		var pid = obj.data('pid');
		TIPS.loading(obj);
		PRODUCT_MODAL.init(pid);
	});
	//收起弹窗
	$('#quickview-modal .content .header, #quickview-modal .mask').on('click', function(){
		PRODUCT_MODAL.close();
	});
	//点击切换大图
	$('#quickview-modal .content').on('click', '.image-list li', function(){
		if ($(this).hasClass('selected')) {
			return false;
		}
		$(this).addClass('selected').siblings().removeClass('selected');
		PRODUCT_MODAL.imageSelected();
	});
	//小图切换
	$('#quickview-modal .content').on('click', '.small-image .movement', function(){
		if ($(this).hasClass('disabled')) {
			return false;
		}
		var obj = $('#quickview-modal .content .image-list');
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
		PRODUCT_MODAL.imageSelected();
	});
	//属性点击
	$('#quickview-modal .content').on('click', '.attv-list li', function(){
		if ($(this).hasClass('disabled')) {
			return false;
		}
		if ($(this).hasClass('selected')) {
			$(this).removeClass('selected');
		} else {
			$(this).addClass('selected').siblings().removeClass('selected');
		}
		PRODUCT_MODAL.skuInit();
	});
	//数量加减
	$('#quickview-modal .content').on('click', '.qty-content .iconfont', function(){
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
		PRODUCT_MODAL.qtyInit();
	});
	//输入数量直接触发
	$('#quickview-modal .content').on('blur', '.qty-content input', function(){
		var val = parseInt($(this).val());
		var stock = $(this).parent().data('stock');
		if (stock < val) {
			$(this).val(stock);
		}
		if (val <= 0) {
			$(this).val(1);
		}
		PRODUCT_MODAL.qtyInit();
	});
	//加入购物车
	$('#quickview-modal .content').on('click', '.btn-add-cart', function(){
		var pObj = $('#quickview-modal .content');
		if (PRODUCT_MODAL.checkSku()) {
			TIPS.loading(pObj);
			PRODUCT_MODAL.addToCart(function(res){
				TIPS.loadout(pObj, true);
			});
		}
	});
});
var PRODUCT_MODAL = {
	init: function(pid) {
		var _this = this;
		_this.pid = pid;
		if (!_this.init_status) {
			_this.maxWidth = $(window).width();
			_this.maxHeight = $(window).height();
			_this.width = _this.maxWidth>1020 ? 1000 : _this.maxWidth-20;
			_this.height = _this.maxHeight>660 ? 640 : _this.maxHeight-20;
			_this.initNum = 10;
			_this.obj = $('#quickview-modal');
			_this.init_status = true;
		}
		_this.getInfo();
	},
	show: function() {
		var _this = this;
		_this.obj.css({width:_this.initNum,maxHeight:_this.initNum,display:'block',left:(_this.maxWidth - _this.initNum)/2,top:(_this.maxHeight - _this.initNum)/2});
		_this.obj.animate({width:_this.width, maxHeight:_this.height,left: (_this.maxWidth - _this.width)/2, top: (_this.maxHeight - _this.height)/2}, 200, 'swing', function(){
			_this.obj.addClass('show');
			TIPS.loadout($('#right-list .product-list li[data-pid="'+_this.pid+'"]'), true);
		});
	},
	close: function() {
		var _this = this;
		_this.obj.removeClass('show').animate({width:_this.initNum,maxHeight:_this.initNum, left:(_this.maxWidth - _this.initNum)/2,top:(_this.maxHeight - _this.initNum)/2}, 300, 'swing', function(){
			_this.obj.hide();
			TIPS.start();
		});
	},
	getInfo: function() {
		var _this = this;
		$.post('/product/getInfoAjax', {pid: this.pid}, function(res){
			if (res.code == '200') {
				_this.info = res.data;
				_this.initPage();
				_this.show();
			} else {
				TIPS.error(res.msg);
			}
		});
	},
	imageSelected: function(){
		var obj = $('#quickview-modal .content .small-image');
		$('#quickview-modal .content .big-image img').attr('src', obj.find('.image-list li.selected img').attr('src').replace('/400', '/600'));
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
	initPage: function() {
		var tempHtml = '';
		for (var i=0; i<this.info.image_list.length; i++) {
			tempHtml += '<li'+(i===0?' class="selected"':'')+'>\
						<div class="image-comtent">\
							<img src="'+this.info.image_list[i]+'">\
						</div>\
					</li>';
		}
		var html = '<div class="left w50">\
				<div class="image-comtent big-image">\
					<img src="'+this.info.image.replace('/400', '/600')+'">\
				</div>\
				<div class="small-image relative'+(this.info.image_list.length>5?' padding':'')+'">';
		html += '<ul class="image-list">\
					'+tempHtml+'\
				</ul>';
		if (this.info.image_list.length > 5) {
			html += '<div class="movement left-movement disabled"><span class="iconfont icon-xiangzuo1"></span></div>';
			html += '<div class="movement right-movement"><span class="iconfont icon-xiangyou1"></span></div>';
		}
		html += '</div>\
			</div>\
			<div class="left w50 info-content">\
				<p class="name mb20">'+this.info.name+'</p>\
				<div class="info mb10">\
					<span class="stock">In Stock</span>\
					<span class="num">SPU: '+this.info.spu_id+'</span>\
				</div>\
				<div class="price-content mb20">\
					<span class="price">'+this.info.min_price_format+'</span>';
		if (this.info.show_price && this.info.original_price > this.info.min_price){
			html += '<span class="original-price">'+this.info.original_price_format+'</span>\
					<span class="discount">'+(1-this.info.min_price/this.info.original_price).toFixed(2)*100+'% OFF</span>';
		}
		html += '</div>\
				<div class="attr-content mb20">';
		var imageAttr = false;
		for (var i in this.info.attrMap){
			tempHtml = '';
			imageAttr = false;
			for (var j=0; j<this.info.attrMap[i].length; j++){
				if (this.info.attvImage[this.info.attrMap[i][j]] != '0') {
					imageAttr = true;
					tempHtml += '<li title="'+this.info.attv[this.info.attrMap[i][j]]+'" data-id="'+this.info.attrMap[i][j]+'"><img src="'+this.info.attvImage[this.info.attrMap[i][j]]+'"></li>';
				} else {
					tempHtml += '<li title="'+this.info.attv[this.info.attrMap[i][j]]+'" data-id="'+this.info.attrMap[i][j]+'"><span>'+this.info.attv[this.info.attrMap[i][j]]+'</span></li>';
				}
			}
			html += '<div class="attr-item" data-id="'+i+'">\
						<div class="attr-name-content">\
							<span class="attr-name">'+this.info.attr[i]+'</span>\
						</div>\
						<ul class="attv-list'+(imageAttr?' attv-img':'')+'">\
							'+tempHtml+'\
						</ul>\
					</div>';
		}
		html += '</div>\
				<div class="qty-content mb20" data-stock="99">\
					<span class="f16 f600">Qty</span>\
					<span class="ml20 iconfont icon-jianhao disabled"></span>\
					<input type="text" name="qty" value="1">\
					<span class="iconfont icon-jiahao1"></span>\
				</div>\
				<div class="btn-content">\
					<button class="btn btn-black mb10 btn-add-cart">ADD TO BAG</button>\
					<button class="btn mb10"><span class="iconfont icon-xihuan"></span> Add to Wish List</button>\
				</div>\
				<a href="'+this.info.url+'" class="c9">View Full Details</a>\
			</div>\
			<div class="clear"></div>';
		this.obj.find('.content .body').html(html);
	},
	skuInit: function() {
		var _this = this;
		var skuMapKey = [];
		var obj = _this.obj.find('.content .attr-item');
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
				CART.count(res.data.cart_count);
				TIPS.success(res.msg);
			} else {
				TIPS.error(res.msg);
			}
			if (callback) callback(res);
		});
	}
};