document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var btn = e.target.closest ? e.target.closest('.js-remove-history') : null;
		if (!btn) return;
		e.preventDefault();
		var hisId = btn.getAttribute('data-id');
		if (!hisId) return;

		post('/userInfo/deleteHistory', { id: hisId }, function (res) {
			if (res && (res.code === 1 || res.code === 200)) {
				var card = btn.closest('.history-card');
				if (card && card.parentNode) {
					card.parentNode.removeChild(card);
				}
				if (document.querySelectorAll('.history-card').length === 0) {
					window.location.reload();
				} else {
					Tool.tips(res.msg || 'History item removed.');
				}
			} else {
				Tool.tips((res && res.msg) ? res.msg : 'Failed to remove item.');
			}
		});
	});
});
