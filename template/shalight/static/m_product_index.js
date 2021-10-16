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
			$.post(URI+'userInfo/wish', {spu_id: _this.spuId}, function(res){
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
			const quantity = $('.quantity .num').val();
			if (quantity == 0) {
				TIPS.error('Sorry, That product was out of stock.');
				return false;
			}
			_this.addToCart(quantity);
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
			const quantity = $('.quantity .num').val();
			if (quantity == 0) {
				TIPS.error('Sorry, That product was out of stock.');
				return false;
			}
			_this.addToCart(quantity);
		});
		//checkout 按钮
		$('.checkout-btn').on('click', function(){
			if (!_this.skuId && !$('#sku-select-modal').is(':visible')) {
				$('#sku-select').trigger('click');
				return false;
			}
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
			pObj.find('.num').val(stock);
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
};(function ($) {
$.fn.slider = function (options) {
	const slideContainer = this;
	const slider = this.find('ul'); // 轮播图节点
	const defaultSettings = {
		transitionDuration: 500,
		autoPlay: true,
		autoPlayTimeout: 4000,
		timingFunction: 'ease-out',
		swipe: true,
	};
	const settings = $.extend(defaultSettings, options);
	const transitionDuration = settings.transitionDuration;
	const swipe = settings.swipe;
	const autoPlayTimeout = settings.autoPlayTimeout;
	const autoPlay = settings.autoPlay;

	let slidingState = 0;
	let startClientX = 0;
	let startPixelOffset = 0;
	let currentSlide = 0;
	let pixelOffset = 0;
	let slideCount = 0;
	let slidesWidth = 0;
	let allowSwipe = true;
	let animationDelayID = undefined;
	let allowSlideSwitch = true;

	(function init() {
		slidesWidth = slider.width();
		$(window).resize(resizeSlider);

		slider.find('.slider:last-child').clone().prependTo(slider);
		slider.find('.slider:nth-child(2)').clone().appendTo(slider);
		slideCount = slider.find('.slider').length;

		setTransitionDuration(transitionDuration);
		setTimingFunction(settings.timingFunction);
		setTransitionProperty('all');

		if(swipe) {
			slider.on('mousedown touchstart', swipeStart);
			$('html').on('mouseup touchend', swipeEnd);
			$('html').on('mousemove touchmove', swiping);
		}
		jumpToSlide(1);
		enableAutoPlay();
	})();
	function resizeSlider(){
		slidesWidth = slider.width();
		switchSlide();
	}
	function swipeStart(event) {
		if(!allowSwipe) return;
		disableAutoPlay();
		if (event.originalEvent.touches) {
			event = event.originalEvent.touches[0];
		}

		if (slidingState == 0) {
			slidingState = 1;
			startClientX = event.clientX;
		}
	}
	function swiping(event) {
		let pointerData;
		if (event.originalEvent.touches) {
			pointerData = event.originalEvent.touches[0];
		} else {
			pointerData = event;
		}
		const deltaSlide = pointerData.clientX - startClientX;

		if (slidingState == 1 && deltaSlide != 0) {
			slidingState = 2;
			startPixelOffset = currentSlide * -slidesWidth;
		}
		if (slidingState == 2) {
			let touchPixelRatio = 1;
			if ((currentSlide == 0 && pointerData.clientX > startClientX) ||
			(currentSlide == slideCount - 1 && pointerData.clientX < startClientX)) {
				touchPixelRatio = 3;
		    }
		    pixelOffset = startPixelOffset + deltaSlide / touchPixelRatio;
			enableTransition(false);
			translateX(pixelOffset);
		}
	}
	function swipeEnd(event) {
		if (slidingState == 2) {
			slidingState = 0;
			currentSlide = pixelOffset < startPixelOffset ? currentSlide + 1 : currentSlide -1;
			currentSlide = Math.min(Math.max(currentSlide, 0), slideCount - 1);
			pixelOffset = currentSlide * -slidesWidth;
			disableSwipe();
			switchSlide();
			enableAutoPlay();
		}
		slidingState = 0;
	} 
	function disableSwipe() {
		allowSwipe = false;
		window.setTimeout(enableSwipe, transitionDuration)
	}
	function enableSwipe() {
		allowSwipe = true;
	}
	function disableAutoPlay() {
		allowSlideSwitch = false;
		window.clearTimeout(animationDelayID);
	}
	function enableAutoPlay() {
		if(autoPlay) {
			allowSlideSwitch = true;
			startAutoPlay();
		}
	}
	function startAutoPlay() {
		if(allowSlideSwitch) {
			animationDelayID = window.setTimeout(performAutoPlay, autoPlayTimeout);
		}
	}
	function performAutoPlay() {
		switchForward();
		startAutoPlay();
	}
	function switchForward() {
		currentSlide += 1;
		switchSlide();
	}
	function switchBackward() {
		currentSlide -= 1;
		switchSlide();
	}
	function switchSlide() {
		enableTransition(true);
		translateX(-currentSlide * slidesWidth);
		popchange();
		if(currentSlide == 0) {
			window.setTimeout(jumpToEnd, transitionDuration);
		} else if (currentSlide == slideCount - 1) {
			window.setTimeout(jumpToStart, transitionDuration);
		}
	}
	function popchange(){
		let index = currentSlide;
		if (currentSlide == slideCount - 1) {
			index = 1;
		} else if (currentSlide == slideCount - 2) {
			index = slideCount - 2;
		}
		slideContainer.find('.pop-content li').eq(index-1).addClass('active').siblings().removeClass('active');
	}
	function jumpToStart() {
		jumpToSlide(1);
	}
	function jumpToEnd() {
		jumpToSlide(slideCount - 2);
	}
	function jumpToSlide(slideNumber) {
		enableTransition(false);
		currentSlide = slideNumber;
		translateX(-slidesWidth * currentSlide);
		window.setTimeout(returnTransitionAfterJump, 50);
	}
	function returnTransitionAfterJump() {
		enableTransition(true);
	}
	function enableTransition(enable) {
		if (enable) {
			setTransitionProperty('all');
		} else {
			setTransitionProperty('none');
		}
	}
	function translateX(distance) {
		slider.stop().css('transform','translateX(' + distance + 'px)');
	}
	function setTransitionDuration(duration) {
		slider.stop().css('transition-duration', duration + 'ms');
	}
	function setTimingFunction(functionDescription) {
		slider.stop().css('transition-timing-function', functionDescription);
	}
	function setTransitionProperty(property) {
		slider.stop().css('transition-property', property);
	}
	return slideContainer;
}
}(jQuery));