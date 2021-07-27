var animationDelayID = undefined;
swipeslider = function (slideContainer,options) {
	var slider = slideContainer.find('.sw-slides');
	var defaultSettings = {
		transitionDuration: 500,
		autoPlay: true,
		autoPlayTimeout: 4000,
		timingFunction: 'ease-out',
		swipe: true,
	};
	var settings = $.extend(defaultSettings, options);
	var slidingState = 0;
	var startClientX = 0;
	var startPixelOffset = 0;
	var pixelOffset = 0;
	var currentSlide = 0;
	var slideCount = 0;
	var slidesWidth = 0;
	var allowSwipe = true;
	var transitionDuration = settings.transitionDuration;
	var swipe = settings.swipe;
	var autoPlayTimeout = settings.autoPlayTimeout;

	(function init() {
		currentSlide = 0;
		disableAutoPlay();
		slidesWidth = slider.width();
		$(window).resize(resizeSlider);

		slider.find('.sw-slide:last-child').clone().prependTo(slider);
		slider.find('.sw-slide:nth-child(2)').clone().appendTo(slider);
		slideCount = slider.find('.sw-slide').length;
		setTransitionDuration(transitionDuration);
		setTimingFunction(settings.timingFunction);
		setTransitionProperty('all');

		if(swipe) {
			slider.on('mousedown touchstart', swipeStart);
			$('html').on('mouseup touchend', swipeEnd);
			$('html').on('mousemove touchmove', swiping);
		}
		jumpToStart();
		startAutoPlay();
	})();
	function resizeSlider(){
		slidesWidth = slider.width();
		switchSlide();
	}
	function swipeStart(event){
		if(!allowSwipe) return;
		disableAutoPlay();
		if (event.originalEvent.touches)
			event = event.originalEvent.touches[0];

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
		var deltaSlide = pointerData.clientX - startClientX;

		if (slidingState == 1 && deltaSlide != 0) {
			slidingState = 2;
			startPixelOffset = currentSlide * -slidesWidth;
		}
		if (slidingState == 2) {
			event.preventDefault();
			var touchPixelRatio = 1;
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
            startAutoPlay();
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
        window.clearTimeout(animationDelayID);
    }
    function startAutoPlay() {
        disableAutoPlay();
        animationDelayID = window.setTimeout(performAutoPlay, autoPlayTimeout);
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
        $('.image-list ol li.active').removeClass('active');
        if (currentSlide == slideCount - 1){
            $('.image-list ol li').eq(0).addClass('active');
        }else{
            $('.image-list ol li').eq(currentSlide-1).addClass('active');
        }
        translateX(-currentSlide * slidesWidth);
        
        if(currentSlide == 0) {
            window.setTimeout(jumpToEnd, transitionDuration);
        } else if (currentSlide == slideCount - 1) {
            window.setTimeout(jumpToStart, transitionDuration);
        }
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
    // 是否允许过渡
    function enableTransition(enable) {
        if (enable) {
            setTransitionProperty('all');
        } else {
            setTransitionProperty('none');
        }
    }

    function translateX(distance) {
        slider.css('transform','translateX(' + distance + 'px)');
    }
	function setTransitionDuration(duration) {
		slider.css('transition-duration', duration + 'ms');
	}

	function setTimingFunction(functionDescription) {
		slider.css('transition-timing-function', functionDescription);
	}

	function setTransitionProperty(property) {
		slider.css('transition-property', property);
	}
	return slideContainer;
}