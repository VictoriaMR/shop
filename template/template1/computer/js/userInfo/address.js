$(function () {
    $('#js-add-new-address').on('click', function () {
        if (window.ShopAddressModal) {
            window.ShopAddressModal.open();
        }
    });

    $(document).on('click', '.js-edit-addr', function () {
        var id = $(this).data('id');
        if (window.ShopAddressModal && id) {
            window.ShopAddressModal.open(id);
        }
    });

    $(document).on('click', '.js-delete-addr', function () {
        var id = $(this).data('id');
        if (!id || !confirm('Are you sure you want to delete this address?')) return;
        $.post('/userInfo/deleteAddress', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                window.location.reload();
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to delete address.');
            }
        }, 'json');
    });

    $(document).on('click', '.js-set-default-shipping', function () {
        var id = $(this).data('id');
        if (!id) return;
        $.post('/userInfo/setAddressDefault', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                window.location.reload();
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to update default address.');
            }
        }, 'json');
    });

    $(document).on('click', '.js-set-default-billing', function () {
        var id = $(this).data('id');
        if (!id) return;
        $.post('/userInfo/setAddressBillDefault', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                window.location.reload();
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to update default billing address.');
            }
        }, 'json');
    });
});
