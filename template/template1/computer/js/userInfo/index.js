$(function () {
    var $panel = $('#js-profile-edit-panel');

    $('#js-toggle-edit-profile, #js-cancel-edit-profile').on('click', function () {
        $panel.toggleClass('is-open');
    });

    $('#js-profile-form').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#js-save-profile-btn');
        var origText = $btn.text();
        $btn.prop('disabled', true).text('Saving...');

        $.post('/userInfo/editInfo', $(this).serialize(), function (res) {
            $btn.prop('disabled', false).text(origText);
            if (res && (res.code == 1 || res.code == 200)) {
                window.location.reload();
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to update profile.');
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false).text(origText);
            alert('Network error. Please try again.');
        });
    });
});
