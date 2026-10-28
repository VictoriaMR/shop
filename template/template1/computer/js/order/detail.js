$(function () {
    $('.js-detail-repurchase').on('click', function () {
        var id = $(this).data('id');
        if (!id) return;
        $.post('/order/repurchase', { id: id }, function (res) {
            if (res && (res.code == 1 || res.code == 200) && res.data && res.data.url) {
                window.location.href = res.data.url;
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to repurchase items.');
            }
        }, 'json');
    });
});
