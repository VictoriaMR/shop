$(function () {
    $(document).on('click', '.js-remove-wishlist', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var spuId = $btn.data('spu-id');
        if (!spuId) return;

        $.post('/userInfo/wish', { spu_id: spuId }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                $btn.closest('.wishlist-card').fadeOut(200, function () {
                    $(this).remove();
                    if ($('.wishlist-card').length === 0) {
                        window.location.reload();
                    }
                });
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to update wishlist.');
            }
        }, 'json');
    });
});
