$(function() {
	SYSINFO_PHPINFO.init();
});

const SYSINFO_PHPINFO = {
	init: function() {
		$('#phpinfo-keyword-input').on('input', function() {
			const kw = $.trim($(this).val()).toLowerCase();
			const $wrap = $('#phpinfo-content');

			if (kw === '') {
				$wrap.find('table, tr, h1, h2').show();
				return;
			}

			$wrap.find('table').each(function() {
				const $table = $(this);
				let hasMatch = false;
				$table.find('tr').each(function() {
					const $tr = $(this);
					const text = $tr.text().toLowerCase();
					if (text.indexOf(kw) !== -1) {
						$tr.show();
						hasMatch = true;
					} else {
						$tr.hide();
					}
				});
				if (hasMatch) {
					$table.show();
					$table.prev('h2, h1').show();
				} else {
					$table.hide();
				}
			});

			$wrap.find('h2, h1').each(function() {
				const $h = $(this);
				if ($h.text().toLowerCase().indexOf(kw) !== -1) {
					$h.show();
					$h.next('table').show().find('tr').show();
				}
			});
		});
	}
};
