$(function() {
	SYSINFO_REDIS.init();
});

const SYSINFO_REDIS = {
	currentSec: 'all',
	init: function() {
		const _this = this;

		$('.redis-tab-filter button').on('click', function() {
			const btn = $(this);
			_this.currentSec = btn.data('sec') || 'all';
			btn.addClass('active').siblings().removeClass('active');
			_this.applyFilter();
		});

		$('#redis-keyword-input').on('input', function() {
			_this.applyFilter();
		});
	},

	applyFilter: function() {
		const sec = this.currentSec;
		const kw = $.trim($('#redis-keyword-input').val()).toLowerCase();

		$('.redis-section').each(function() {
			const $sec = $(this);
			const secName = $sec.data('section');
			if (sec !== 'all' && secName !== sec) {
				$sec.hide();
				return;
			}
			let visible = 0;
			$sec.find('.kv-card').each(function() {
				const $card = $(this);
				const text = $card.text().toLowerCase();
				if (kw === '' || text.indexOf(kw) !== -1) {
					$card.show();
					visible++;
				} else {
					$card.hide();
				}
			});
			if (visible > 0) {
				$sec.show();
			} else {
				$sec.hide();
			}
		});
	}
};
