document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var cancelBtn = e.target.closest ? e.target.closest('.js-order-cancel') : null;
		if (cancelBtn) {
			var cancelId = cancelBtn.getAttribute('data-id');
			if (!cancelId || !window.confirm('Are you sure you want to cancel this order?')) return;
			post('/order/cancel', { id: cancelId }, function (res) {
				if (res && (res.code === 1 || res.code === 200)) {
					Tool.tips(res.msg || 'Order cancelled.');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to cancel order.');
				}
			});
			return;
		}

		var completeBtn = e.target.closest ? e.target.closest('.js-order-complete') : null;
		if (completeBtn) {
			var completeId = completeBtn.getAttribute('data-id');
			if (!completeId || !window.confirm('Confirm that you have received this order?')) return;
			post('/order/complete', { id: completeId }, function (res) {
				if (res && (res.code === 1 || res.code === 200)) {
					Tool.tips(res.msg || 'Order marked as received.');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to update order.');
				}
			});
			return;
		}

		var repurchaseBtn = e.target.closest ? e.target.closest('.js-order-repurchase') : null;
		if (repurchaseBtn) {
			var repurchaseId = repurchaseBtn.getAttribute('data-id');
			if (!repurchaseId) return;
			post('/order/repurchase', { id: repurchaseId }, function (res) {
				if (res && (res.code === 1 || res.code === 200)) {
					var nextUrl = (res.data && res.data.url) ? res.data.url : '/cart.html';
					window.location.href = nextUrl;
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Could not repurchase items.');
				}
			});
			return;
		}

		var deleteBtn = e.target.closest ? e.target.closest('.js-order-delete') : null;
		if (deleteBtn) {
			var deleteId = deleteBtn.getAttribute('data-id');
			if (!deleteId || !window.confirm('Move this order to trash?')) return;
			post('/order/delete', { id: deleteId }, function (res) {
				if (res && (res.code === 1 || res.code === 200)) {
					Tool.tips(res.msg || 'Order moved to trash.');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to delete order.');
				}
			});
			return;
		}

		var restoreBtn = e.target.closest ? e.target.closest('.js-order-restore') : null;
		if (restoreBtn) {
			var restoreId = restoreBtn.getAttribute('data-id');
			if (!restoreId) return;
			post('/order/restore', { id: restoreId }, function (res) {
				if (res && (res.code === 1 || res.code === 200)) {
					Tool.tips(res.msg || 'Order restored.');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to restore order.');
				}
			});
		}
	});
});
