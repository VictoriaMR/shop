(function ($) {
$.fn.slider = function (options) {
	var slideContainer = this;
	var slider = this.find('ul'); // 轮播图节点

	var slidesWidth = 0;
	var slideCount = 0;
	var optCount = 0;
	var interval;
	var sliderIndex = 1;
	var optIndex = 0;
	var clickable = true;

	(function init() {
		slidesWidth = slider.width();
		$(window).resize(resize);
		optCount = slider.find('li').length;
		popInit(optCount);
		slider.find('li:last-child').clone().prependTo(slider);
		slider.find('li:nth-child(2)').clone().appendTo(slider);
		slider.css({'left': -slidesWidth+'px'});
		slideCount = optCount+2;
		start();
		slideContainer.on('mouseover', stop);
		slideContainer.on('mouseleave', start);
		//向右点击
		slideContainer.find('.pop-opt span').on('click', function(){
			if (!clickable) {
				return false;
			}
			clickable = false;
			if ($(this).hasClass('t-right')) {
				sliderIndex++;
				optIndex++;
			} else {
				sliderIndex--;
				optIndex--;
			}
			translateX();
			setTimeout(function(){
				clickable = true;
			}, 750);
		});
		//导航图标点击
		slideContainer.find('ol li').on('click', function(){
			if (!clickable || $(this).hasClass('active')) {
				return false;
			}
			clickable = false;
			optIndex = $(this).index();
			sliderIndex = optIndex + 1;
			translateX();
			setTimeout(function(){
				clickable = true;
			}, 750);
		});
	})();
	//边框伸缩
	function resize(){
		slidesWidth = slider.width();
	}
	//导航图标
	function popInit(count){
		var html = '<ol style="margin-left:-'+(count*5.5+3)+'px;">';
		for (var i=0; i<count; i++) {
			html += '<li '+(i===0?'class="active"':'')+'>\
						<span></span>\
					</li>';
		}
		html +='</ol>';
		html += '<div class="pop-opt">\
					<span class="t-left"><</span>\
					<span class="t-right">></span>\
				</div>';
		slideContainer.append(html);
	}
	function start(){
		stop();
		interval = window.setInterval(function(){
			sliderIndex++;
			optIndex++;
			translateX();
		}, 3000);
	}
	function stop(){
		window.clearInterval(interval);
	}
	function translateX() {
		//图片轮播
		slider.stop().css({'left': -sliderIndex*slidesWidth + 'px', 'transition': 'all, linear, 0.75s'});
		if (sliderIndex === optCount) {
			sliderIndex = 0;
			setTimeout(function(){
				slider.stop().css({'transition': '', 'left': 0});
			}, 750);
		} else if (sliderIndex===0) {
			sliderIndex = optCount;
			setTimeout(function(){
				slider.stop().css({'transition': '', 'left': -sliderIndex*slidesWidth + 'px'});
			}, 750);
		}
		//按钮轮动
		if (optIndex == optCount) {
			optIndex = 0;
		} else if(optIndex<0) {
			optIndex = optCount-1;
		}
		slideContainer.find('ol>li').eq(optIndex).addClass('active').siblings().removeClass('active');
	}
	return slideContainer;
}
}(jQuery));