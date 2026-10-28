document.addEventListener('DOMContentLoaded', function () {
	var checkAllBox = document.getElementById('cart-check-all');
	var itemCheckboxes = document.querySelectorAll('.cart-item-check');
	var btnCheckout = document.getElementById('btn-cart-checkout');

	function refreshCartPage() {
		window.location.reload();
	}

	// 1. 全选 / 取消全选
	if (checkAllBox) {
		checkAllBox.addEventListener('change', function () {
			var checked = this.checked ? 1 : 0;
			post('/cart/setChecked', { id: -1, check: checked }, function (res) {
				if (res && res.code === 1) {
					refreshCartPage();
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to update selection.');
				}
			});
		});
	}

	// 2. 单项勾选切换
	for (var i = 0; i < itemCheckboxes.length; i++) {
		itemCheckboxes[i].addEventListener('change', function () {
			var cartId = this.getAttribute('data-cart-id');
			var checked = this.checked ? 1 : 0;
			post('/cart/setChecked', { id: cartId, check: checked }, function (res) {
				if (res && res.code === 1) {
					refreshCartPage();
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to update item.');
				}
			});
		});
	}

	// 3. 数量增减与删除事件代理
	document.addEventListener('click', function (e) {
		var qtyBtn = e.target.closest ? e.target.closest('[data-cart-qty-dir]') : null;
		if (qtyBtn) {
			var row = qtyBtn.closest('.cart-item-row');
			if (!row) return;
			var cartId = row.getAttribute('data-cart-id');
			var input = row.querySelector('.cart-qty-input');
			if (!input || !cartId) return;
			var cur = parseInt(input.value, 10) || 1;
			var max = parseInt(input.getAttribute('max'), 10) || 999;
			var dir = qtyBtn.getAttribute('data-cart-qty-dir');
			var next = dir === 'plus' ? cur + 1 : cur - 1;
			if (next < 1 || next > max) return;
			input.value = next;
			post('/cart/updateQuantity', { id: cartId, quantity: next }, function (res) {
				if (res && res.code === 1) {
					refreshCartPage();
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Unable to update quantity.');
				}
			});
			return;
		}

		var removeBtn = e.target.closest ? e.target.closest('[data-cart-remove]') : null;
		if (removeBtn) {
			var rId = removeBtn.getAttribute('data-cart-remove');
			if (!rId) return;
			post('/cart/remove', { id: rId }, function (res) {
				if (res && res.code === 1) {
					refreshCartPage();
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to remove item.');
				}
			});
			return;
		}
	});

	// 4. 去结算校验 (Proceed to Checkout)
	if (btnCheckout) {
		btnCheckout.addEventListener('click', function () {
			btnCheckout.disabled = true;
			btnCheckout.textContent = 'Checking bag...';
			post('/cart/check', {}, function (res) {
				if (res && res.code === 1) {
					var targetUrl = (res.data && res.data.url) ? res.data.url : (typeof res.msg === 'string' && res.msg.indexOf('http') === 0 ? res.msg : '/checkout');
					window.location.href = targetUrl;
				} else {
					btnCheckout.disabled = false;
					btnCheckout.textContent = 'Proceed to Checkout';
					Tool.tips((res && res.msg) ? res.msg : 'Please select at least one in-stock item.');
				}
			});
		});
	}
});
