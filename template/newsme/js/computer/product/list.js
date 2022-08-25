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
});
var PRODUCT_MODAL = {
	init: function(pid) {
		var _this = this;
		_this.pid = pid;
		if (!_this.init_status) {
			_this.maxWidth = $(window).width();
			_this.maxHeight = $(window).height();
			_this.width = _this.maxWidth>1020 ? 1000 : _this.maxWidth-20;
			_this.height = _this.maxHeight>620 ? 600 : _this.maxHeight-20;
			_this.initNum = 10;
			_this.obj = $('#quickview-modal');
			_this.init_status = true;
		}
		_this.getInfo();
	},
	show: function() {
		var _this = this;
		_this.obj.css({width:_this.initNum,height:_this.initNum,display:'block',left:(_this.maxWidth - _this.initNum)/2,top:(_this.maxHeight - _this.initNum)/2});
		_this.obj.animate({width:_this.width, height:_this.height,left: (_this.maxWidth - _this.width)/2, top: (_this.maxHeight - _this.height)/2}, 200, 'swing', function(){
			TIPS.loadout($('#right-list .product-list li[data-pid="'+_this.pid+'"]'), true);
		});
	},
	close: function() {
		var _this = this;
		_this.obj.animate({width:_this.initNum,height:_this.initNum, left:(_this.maxWidth - _this.initNum)/2,top:(_this.maxHeight - _this.initNum)/2}, 300, 'swing', function(){
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

	},
};