$(function() {
	SYSINFO_INDEX.init();
});

const SYSINFO_INDEX = {
	currentFilter: 'all',
	init: function() {
		const _this = this;

		$('.sys-tab-filter button').on('click', function() {
			const btn = $(this);
			_this.currentFilter = btn.data('filter') || 'all';
			btn.addClass('active').siblings().removeClass('active');
			_this.applyFilter();
		});

		$('#sys-keyword-input').on('input', function() {
			_this.applyFilter();
		});
	},

	applyFilter: function() {
		const filter = this.currentFilter;
		const kw = $.trim($('#sys-keyword-input').val()).toLowerCase();

		$('.sys-section').each(function() {
			const $sec = $(this);
			const secType = $sec.data('section');
			if (filter === 'env' && secType !== 'env') {
				$sec.hide();
				return;
			}
			if (filter === 'ext' && secType !== 'ext') {
				$sec.hide();
				return;
			}

			let visibleCount = 0;
			$sec.find('.sys-item-card').each(function() {
				const $item = $(this);
				const isOk = String($item.data('ok')) === '1';
				const text = $item.text().toLowerCase();
				const matchFilter = (filter !== 'warn') || !isOk;
				const matchKw = (kw === '') || (text.indexOf(kw) !== -1);

				if (matchFilter && matchKw) {
					$item.show();
					visibleCount++;
				} else {
					$item.hide();
				}
			});

			if (visibleCount > 0) {
				$sec.show();
			} else {
				$sec.hide();
			}
		});
	}
};
