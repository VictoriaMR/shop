document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var btn = e.target.closest ? e.target.closest('.js-remove-wishlist') : null;
		if (!btn) return;
		e.preventDefault();
		var spuId = btn.getAttribute('data-spu-id');
		if (!spuId) return;

		post('/userInfo/wish', { spu_id: spuId }, function (res) {
			if (res && (res.code === 1 || res.code === 200)) {
				var card = btn.closest('.wishlist-card');
				if (card && card.parentNode) {
					card.parentNode.removeChild(card);
				}
				if (document.querySelectorAll('.wishlist-card').length === 0) {
					window.location.reload();
				} else {
					Tool.tips(res.msg || 'Removed from wishlist.');
				}
			} else {
				Tool.tips((res && res.msg) ? res.msg : 'Failed to update wishlist.');
			}
		});
	});
});
