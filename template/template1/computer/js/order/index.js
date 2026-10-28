$(function () {
    $(document).on('click', '.js-order-cancel', function () {
        var id = $(this).data('id');
        if (!id || !confirm('Are you sure you want to cancel this order?')) return;
        $.post('/order/cancel', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                window.location.reload();
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to cancel order.');
            }
        }, 'json');
    });

    $(document).on('click', '.js-order-complete', function () {
        var id = $(this).data('id');
        if (!id || !confirm('Confirm that you have received this order?')) return;
        $.post('/order/complete', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                window.location.reload();
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to update order.');
            }
        }, 'json');
    });

    $(document).on('click', '.js-order-repurchase', function () {
        var id = $(this).data('id');
        if (!id) return;
        $.post('/order/repurchase', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200) && res.data && res.data.url) {
                window.location.href = res.data.url;
            } else {
                alert((res && res.msg) ? res.msg : 'Could not repurchase items.');
            }
        }, 'json');
    });

    $(document).on('click', '.js-order-delete', function () {
        var id = $(this).data('id');
        if (!id || !confirm('Move this order to trash?')) return;
        $.post('/order/delete', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                window.location.reload();
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to delete order.');
            }
        }, 'json');
    });

    $(document).on('click', '.js-order-restore', function () {
        var id = $(this).data('id');
        if (!id) return;
        $.post('/order/restore', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                window.location.reload();
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to restore order.');
            }
        }, 'json');
    });
});
