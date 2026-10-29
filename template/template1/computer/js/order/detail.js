document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var btn = e.target.closest ? e.target.closest('.js-detail-repurchase') : null;
		if (!btn) return;
		var id = btn.getAttribute('data-id');
		if (!id) return;
		post('/order/repurchase', { id: id }, function (res) {
			if (res && (res.code === 1 || res.code === 200)) {
				var nextUrl = (res.data && res.data.url) ? res.data.url : '/cart.html';
				window.location.href = nextUrl;
			} else {
				Tool.tips((res && res.msg) ? res.msg : 'Failed to repurchase items.');
			}
		});
	});
});
