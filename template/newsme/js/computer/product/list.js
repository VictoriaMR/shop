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
		var src = $(this).find('img').attr('src').replace('/400', '/600');
		$(this).parent().prev().find('img').attr('src', src);
		$(this).addClass('selected').siblings().removeClass('selected');
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
			_this.height = _this.maxHeight>640 ? 620 : _this.maxHeight-20;
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
		$.post(URI+'product/getInfoAjax', {pid: this.pid}, function(res){
			if (res.code == '200') {
				_this.info = res.data;
				_this.initPage();
			} else {
				TIPS.error(res.msg);
			}
			_this.show();
		});
	},
	initPage: function() {
		var mainImage = '';
		var tempHtml = '';
		for (var i=0; i<this.info.image.length; i++) {
			if (i === 0) {
				mainImage = this.info.image[i].url.replace('/400', '/600');
			}
			tempHtml += '<li'+(i===0?' class="selected"':'')+'>\
						<div class="image-comtent">\
							<img src="'+this.info.image[i].url+'">\
						</div>\
					</li>';
		}
		var html = '<div class="left w50">\
				<div class="image-comtent">\
					<img src="'+mainImage+'">\
				</div>\
				<ul class="image-list">\
					'+tempHtml+'\
				</ul>\
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
					tempHtml += '<li><img src="'+this.info.attvImage[this.info.attrMap[i][j]].url+'"></li>';
				} else {
					tempHtml += '<li><span>'+this.info.attv[this.info.attrMap[i][j]]+'</span></li>';
				}
			}
			html += '<div class="attr-item">\
						<div class="attr-name-content mb4">\
							<span class="attr-name">'+this.info.attr[i]+'</span>\
						</div>\
						<ul class="attv-list'+(imageAttr?' attv-img':'')+'">\
							'+tempHtml+'\
						</ul>\
					</div>';
		}
		html += '</div>\
				<div class="qty-content mb20">\
					<span class="f16 f600">Qty</span>\
					<span class="ml20 iconfont icon-jianhao"></span>\
					<input type="text" name="qty" value="1">\
					<span class="iconfont icon-jiahao1"></span>\
				</div>\
				<div class="btn-content">\
					<button class="btn btn-black mb10">ADD TO BAG</button>\
					<button class="btn mb10"><span class="iconfont icon-xihuan"></span> Add to Wish List</button>\
				</div>\
				<a href="'+this.info.url+'" class="c9">View Full Details</a>\
			</div>\
			<div class="clear"></div>';
		this.obj.find('.content .body').html(html);
	},
};