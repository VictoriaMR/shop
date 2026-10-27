document.addEventListener('DOMContentLoaded', function () {
	// 1. 首页轮播主图控制 (Hero Carousel)
	var carousel = document.getElementById('hero-carousel');
	if (carousel) {
		var slides = carousel.querySelectorAll('.hero-slide');
		var dots = carousel.querySelectorAll('.carousel-dot');
		var prevBtn = document.getElementById('hero-prev');
		var nextBtn = document.getElementById('hero-next');
		var currentIndex = 0;
		var slideCount = slides.length;
		var intervalMs = parseInt(carousel.getAttribute('data-autoplay'), 10) || 5500;
		var timerId = null;

		function goToSlide(index) {
			if (slideCount <= 1) return;
			currentIndex = (index + slideCount) % slideCount;
			for (var i = 0; i < slideCount; i++) {
				var isActive = i === currentIndex;
				slides[i].classList.toggle('active', isActive);
				slides[i].setAttribute('aria-hidden', isActive ? 'false' : 'true');
				if (dots[i]) {
					dots[i].classList.toggle('active', isActive);
					dots[i].setAttribute('aria-current', isActive ? 'true' : 'false');
				}
			}
		}

		function startAutoPlay() {
			if (slideCount <= 1) return;
			stopAutoPlay();
			timerId = setInterval(function () {
				goToSlide(currentIndex + 1);
			}, intervalMs);
		}

		function stopAutoPlay() {
			if (timerId) {
				clearInterval(timerId);
				timerId = null;
			}
		}

		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				goToSlide(currentIndex - 1);
				startAutoPlay();
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				goToSlide(currentIndex + 1);
				startAutoPlay();
			});
		}

		for (var d = 0; d < dots.length; d++) {
			dots[d].addEventListener('click', function () {
				var targetIdx = parseInt(this.getAttribute('data-slide'), 10);
				if (!isNaN(targetIdx)) {
					goToSlide(targetIdx);
					startAutoPlay();
				}
			});
		}

		carousel.addEventListener('mouseenter', stopAutoPlay);
		carousel.addEventListener('mouseleave', startAutoPlay);
		startAutoPlay();
	}
});
