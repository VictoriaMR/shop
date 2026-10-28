$(function () {
    $('#js-reset-password-form').on('submit', function (e) {
        e.preventDefault();
        var pwd = $('#rp-password').val();
        var cpwd = $('#rp-confirm').val();
        if (!pwd || pwd.length < 6) {
            alert('Password must be at least 6 characters.');
            return;
        }
        if (pwd !== cpwd) {
            alert('Passwords do not match.');
            return;
        }
        var $btn = $('#js-reset-submit');
        $btn.prop('disabled', true).text('Updating...');
        $.post(window.location.href, $(this).serialize(), function (res) {
            $btn.prop('disabled', false).text('Update Password');
            if (res && (res.code == 1 || res.code == 200)) {
                alert(res.msg || 'Password updated successfully.');
                window.location.href = (res.data && res.data.url) ? res.data.url : '/login';
            } else {
                alert((res && res.msg) ? res.msg : 'Failed to reset password.');
            }
        }, 'json');
    });
});
