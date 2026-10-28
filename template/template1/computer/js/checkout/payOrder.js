document.addEventListener('DOMContentLoaded', function () {
	var methodItems = document.querySelectorAll('.pay-method-item');
	var methodInput = document.getElementById('pay-selected-method');
	var payBtn = document.getElementById('btn-confirm-payment');
	var orderIdInput = document.getElementById('pay-order-id');

	for (var i = 0; i < methodItems.length; i++) {
		methodItems[i].addEventListener('click', function () {
			var mType = this.getAttribute('data-method-type');
			for (var j = 0; j < methodItems.length; j++) {
				methodItems[j].classList.remove('selected');
			}
			this.classList.add('selected');
			var radio = this.querySelector('input[type="radio"]');
			if (radio) radio.checked = true;
			if (methodInput) methodInput.value = mType;
		});
	}

	if (payBtn) {
		payBtn.addEventListener('click', function () {
			var orderId = orderIdInput ? parseInt(orderIdInput.value, 10) : 0;
			var method = methodInput ? parseInt(methodInput.value, 10) : 1;
			if (!orderId) {
				Tool.tips('Invalid order reference.');
				return;
			}
			payBtn.disabled = true;
			var origText = payBtn.textContent;
			payBtn.textContent = 'Processing Payment...';

			post('/checkout/payOrderAjax', {
				order_id: orderId,
				method: method
			}, function (res) {
				if (res && res.code === 1) {
					Tool.tips(res.msg || 'Payment successful!');
					var targetUrl = (res.data && res.data.url) ? res.data.url : ('/order/detail.html?id=' + orderId);
					setTimeout(function () {
						window.location.href = targetUrl;
					}, 600);
				} else {
					payBtn.disabled = false;
					payBtn.textContent = origText;
					Tool.tips((res && res.msg) ? res.msg : 'Payment failed. Please try again.');
				}
			});
		});
	}
});
