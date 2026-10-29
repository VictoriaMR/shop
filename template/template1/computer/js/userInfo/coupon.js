document.addEventListener('DOMContentLoaded', function () {
	var tabs = document.querySelectorAll('.js-coupon-tab');
	for (var i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener('click', function () {
			for (var j = 0; j < tabs.length; j++) {
				tabs[j].classList.remove('is-active');
			}
			this.classList.add('is-active');
		});
	}

	var form = document.getElementById('js-coupon-redeem-form');
	if (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var input = document.getElementById('js-coupon-code');
			var code = input ? input.value.trim() : '';
			if (!code) {
				Tool.tips('Please enter a promo or voucher code.');
				if (input) input.focus();
				return false;
			}
			Tool.tips('Promo code "' + code + '" is invalid or has expired.');
			return false;
		});
	}
});
