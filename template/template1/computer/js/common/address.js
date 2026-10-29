document.addEventListener('DOMContentLoaded', function () {
	var modal = document.getElementById('pc-address-modal');
	var modalTitle = document.getElementById('pc-addr-modal-title');
	var closeBtn = document.getElementById('pc-addr-modal-close');
	var cancelBtn = document.getElementById('pc-addr-modal-cancel');
	var form = document.getElementById('pc-address-form');
	var submitBtn = document.getElementById('pc-addr-modal-submit');

	if (!modal || !form) return;

	function closeModal() {
		modal.classList.add('hidden');
	}

	function openModal(addressId) {
		form.reset();
		var idField = document.getElementById('addr-field-id');
		if (idField) idField.value = addressId || 0;

		if (addressId && parseInt(addressId, 10) > 0) {
			if (modalTitle) modalTitle.textContent = 'Edit Address';
			post('/userInfo/getAddressInfo', { id: addressId }, function (res) {
				if (res && res.code === 1 && res.data) {
					var d = res.data;
					form.querySelector('[name="address_id"]').value = d.address_id || addressId;
					form.querySelector('[name="first_name"]').value = d.first_name || '';
					form.querySelector('[name="last_name"]').value = d.last_name || '';
					form.querySelector('[name="country_code2"]').value = d.country_code2 || 'US';
					var rawPhone = (d.phone || '').replace(/^\+\d+\s*/, '');
					form.querySelector('[name="phone"]').value = rawPhone;
					form.querySelector('[name="address1"]').value = d.address1 || '';
					form.querySelector('[name="address2"]').value = d.address2 || '';
					form.querySelector('[name="company_name"]').value = d.company_name || '';
					form.querySelector('[name="city"]').value = d.city || '';
					form.querySelector('[name="state"]').value = d.state || '';
					form.querySelector('[name="postcode"]').value = d.postcode || '';
					form.querySelector('[name="is_default"]').checked = parseInt(d.is_default, 10) === 1;
					form.querySelector('[name="is_bill"]').checked = parseInt(d.is_bill, 10) === 1;
				}
			});
		} else {
			if (modalTitle) modalTitle.textContent = 'Add New Address';
		}
		modal.classList.remove('hidden');
	}

	if (closeBtn) closeBtn.addEventListener('click', closeModal);
	if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

	window.ShopAddressModal = {
		open: openModal,
		close: closeModal
	};

	document.addEventListener('click', function (e) {
		var trigger = e.target.closest ? e.target.closest('[data-open-address-modal]') : null;
		if (trigger) {
			e.preventDefault();
			var addrId = trigger.getAttribute('data-address-id') || 0;
			openModal(addrId);
			return;
		}
		if (e.target === modal) {
			closeModal();
		}
	});

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var payload = {
			address_id: form.querySelector('[name="address_id"]').value,
			first_name: form.querySelector('[name="first_name"]').value.trim(),
			last_name: form.querySelector('[name="last_name"]').value.trim(),
			country_code2: form.querySelector('[name="country_code2"]').value,
			phone: form.querySelector('[name="phone"]').value.trim(),
			address1: form.querySelector('[name="address1"]').value.trim(),
			address2: form.querySelector('[name="address2"]').value.trim(),
			company_name: form.querySelector('[name="company_name"]').value.trim(),
			city: form.querySelector('[name="city"]').value.trim(),
			state: form.querySelector('[name="state"]').value.trim(),
			postcode: form.querySelector('[name="postcode"]').value.trim(),
			is_default: form.querySelector('[name="is_default"]').checked ? 1 : 0,
			is_bill: form.querySelector('[name="is_bill"]').checked ? 1 : 0
		};

		if (!payload.first_name || !payload.phone || !payload.address1 || !payload.city || !payload.state || !payload.postcode) {
			Tool.tips('Please fill in all required (*) address fields.');
			return false;
		}

		if (submitBtn) {
			submitBtn.disabled = true;
			submitBtn.textContent = 'Saving...';
		}

		post('/userInfo/editAddress', payload, function (res) {
			if (submitBtn) {
				submitBtn.disabled = false;
				submitBtn.textContent = 'Save Address';
			}
			if (res && res.code === 1) {
				Tool.tips(res.msg || 'Address saved!');
				setTimeout(function () {
					window.location.reload();
				}, 500);
			} else {
				Tool.tips((res && res.msg) ? res.msg : 'Failed to save address.');
			}
		});
		return false;
	});
});
