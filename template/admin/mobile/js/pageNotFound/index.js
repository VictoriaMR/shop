$(function() {
	PAGENOTFOUND.init();
});

const PAGENOTFOUND = {
	init: function() {
		const homeUrl = $('#page-not-found').data('home') || '/';
		$('.btn-back').on('click', function() {
			if (window.history.length > 1) {
				window.history.back();
			} else {
				window.location.href = homeUrl;
			}
		});
	}
};
