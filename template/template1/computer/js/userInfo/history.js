$(function () {
    $(document).on('click', '.js-remove-history', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var hisId = $btn.data('id');
        if (!hisId) return;

        $.post('/userInfo/deleteHistory', { id: hisId }, function (res) {
            if (res && (res.code == 1 || res.code == 200)) {
                $btn.closest('.history-card').fadeOut(200, function () {
                    $(this).remove();
                    if ($('.history-card').length === 0) {
                        window.location.reload();
                    }
                });
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to remove item.');
            }
        }, 'json');
    });
});
