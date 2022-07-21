(function ($) {
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

	var slidingState = 0;
	var startClientX = 0;
	var startPixelOffset = 0;
	var currentSlide = 0;
	var pixelOffset = 0;
	var slideCount = 0;
	var slidesWidth = 0;
	var allowSwipe = true;
	var animationDelayID = undefined;
	var allowSlideSwitch = true;

	(function init() {
		slidesWidth = slider.width();
		$(window).resize(resizeSlider);

		slider.find('.slider:last-child').clone().prependTo(slider);
		slider.find('.slider:nth-child(2)').clone().appendTo(slider);
		slideCount = slider.find('.slider').length;

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
		var pointerData;
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
			var touchPixelRatio = 1;
			if ((currentSlide == 0 && pointerData.clientX > startClientX) ||
			(currentSlide == slideCount - 1 && pointerData.clientX < startClientX)) {
				touchPixelRatio = 3;
		    }
		    pixelOffset = startPixelOffset + deltaSlide / touchPixelRatio;
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
		translateX(-currentSlide * slidesWidth);
		popchange();
		if(currentSlide == 0) {
			window.setTimeout(jumpToEnd, transitionDuration);
		} else if (currentSlide == slideCount - 1) {
			window.setTimeout(jumpToStart, transitionDuration);
		}
	}
	function popchange(){
		var index = currentSlide;
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
		currentSlide = slideNumber;
		translateX(-slidesWidth * currentSlide);
	}
	function translateX(distance) {
		slider.stop().css('left',distance+'px');
	}
	return slideContainer;
}
}(jQuery));