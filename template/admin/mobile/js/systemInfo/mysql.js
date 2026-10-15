$(function() {
	SYSINFO_MYSQL.init();
});

const SYSINFO_MYSQL = {
	init: function() {
		$('#mysql-keyword-input').on('input', function() {
			const kw = $.trim($(this).val()).toLowerCase();
			$('.mysql-section').each(function() {
				const $sec = $(this);
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
		});
	}
};
