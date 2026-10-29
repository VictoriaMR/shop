document.addEventListener('DOMContentLoaded', function () {
	var addBtn = document.getElementById('js-add-new-address');
	if (addBtn) {
		addBtn.addEventListener('click', function () {
			if (window.ShopAddressModal) {
				window.ShopAddressModal.open(0);
			}
		});
	}

	document.addEventListener('click', function (e) {
		var editBtn = e.target.closest ? e.target.closest('.js-edit-addr') : null;
		if (editBtn) {
			var editId = editBtn.getAttribute('data-id');
			if (window.ShopAddressModal && editId) {
				window.ShopAddressModal.open(editId);
			}
			return;
		}

		var delBtn = e.target.closest ? e.target.closest('.js-delete-addr') : null;
		if (delBtn) {
			var delId = delBtn.getAttribute('data-id');
			if (!delId || !window.confirm('Are you sure you want to delete this address?')) return;
			post('/userInfo/deleteAddress', { id: delId }, function (res) {
				if (res && (res.code === 1 || res.code === 200)) {
					Tool.tips(res.msg || 'Address deleted.');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to delete address.');
				}
			});
			return;
		}

		var shipBtn = e.target.closest ? e.target.closest('.js-set-default-shipping') : null;
		if (shipBtn) {
			var shipId = shipBtn.getAttribute('data-id');
			if (!shipId) return;
			post('/userInfo/setAddressDefault', { id: shipId }, function (res) {
				if (res && (res.code === 1 || res.code === 200)) {
					Tool.tips(res.msg || 'Default shipping address updated.');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to update default address.');
				}
			});
			return;
		}

		var billBtn = e.target.closest ? e.target.closest('.js-set-default-billing') : null;
		if (billBtn) {
			var billId = billBtn.getAttribute('data-id');
			if (!billId) return;
			post('/userInfo/setAddressBillDefault', { id: billId }, function (res) {
				if (res && (res.code === 1 || res.code === 200)) {
					Tool.tips(res.msg || 'Default billing address updated.');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to update default billing address.');
				}
			});
		}
	});
});
