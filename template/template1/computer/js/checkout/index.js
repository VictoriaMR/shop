document.addEventListener('DOMContentLoaded', function () {
	var shipInput = document.getElementById('checkout-shipping-addr-id');
	var billInput = document.getElementById('checkout-billing-addr-id');
	var addrCards = document.querySelectorAll('.checkout-addr-item');
	var insCheckbox = document.getElementById('checkout-insurance-check');
	var grandTotalEl = document.getElementById('checkout-grand-total');
	var insRowEl = document.getElementById('checkout-insurance-row');
	var placeOrderBtn = document.getElementById('btn-place-order');
	var skuIdInput = document.getElementById('checkout-direct-sku-id');
	var qtyInput = document.getElementById('checkout-direct-qty');
	var guestEmailInput = document.getElementById('checkout-guest-email');

	// 1. 选择收货地址卡片
	for (var i = 0; i < addrCards.length; i++) {
		addrCards[i].addEventListener('click', function (e) {
			if (e.target.closest && e.target.closest('[data-open-address-modal]')) {
				return;
			}
			var id = this.getAttribute('data-addr-id');
			for (var j = 0; j < addrCards.length; j++) {
				addrCards[j].classList.remove('selected');
			}
			this.classList.add('selected');
			if (shipInput) shipInput.value = id;
			if (billInput) billInput.value = id;
		});
	}

	// 2. 勾选/取消运费险动态重算总额
	if (insCheckbox) {
		insCheckbox.addEventListener('change', function () {
			var ins = this.checked ? 1 : 0;
			if (insRowEl) {
				insRowEl.classList.toggle('hidden', !ins);
			}
			post('/checkout/calculateOrderFee', {
				id: skuIdInput ? skuIdInput.value : 0,
				quantity: qtyInput ? qtyInput.value : 1,
				insurance: ins
			}, function (res) {
				if (res && res.code === 1 && res.data && grandTotalEl) {
					grandTotalEl.textContent = res.data.value_format;
				}
			});
		});
	}

	// 3. 提交订单 (Place Order)
	if (placeOrderBtn) {
		placeOrderBtn.addEventListener('click', function () {
			var shipId = shipInput ? parseInt(shipInput.value, 10) : 0;
			var billId = billInput ? parseInt(billInput.value, 10) : shipId;
			var guestEmail = guestEmailInput ? guestEmailInput.value.trim() : '';

			if (guestEmailInput && !VERIFY.email(guestEmail)) {
				Tool.tips('Please enter a valid contact email address.');
				guestEmailInput.focus();
				return;
			}
			if (!shipId) {
				Tool.tips('Please add or select a shipping address first.');
				return;
			}

			placeOrderBtn.disabled = true;
			var origText = placeOrderBtn.textContent;
			placeOrderBtn.textContent = 'Creating Order...';

			post('/checkout/createOrder', {
				id: skuIdInput ? skuIdInput.value : 0,
				quantity: qtyInput ? qtyInput.value : 1,
				shipping_address_id: shipId,
				billing_address_id: billId || shipId,
				insurance: (insCheckbox && insCheckbox.checked) ? 1 : 0,
				guest_email: guestEmail
			}, function (res) {
				if (res && res.code === 1) {
					Tool.tips(res.msg || 'Order created!');
					var nextUrl = (res.data && res.data.url) ? res.data.url : '/order';
					setTimeout(function () {
						window.location.href = nextUrl;
					}, 500);
				} else {
					placeOrderBtn.disabled = false;
					placeOrderBtn.textContent = origText;
					Tool.tips((res && res.msg) ? res.msg : 'Failed to create order.');
				}
			});
		});
	}
});
