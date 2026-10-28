$(function () {
    $('#js-contact-form').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#js-contact-submit');
        var $fb = $('#js-contact-feedback');
        var origText = $btn.text();

        $fb.removeClass('success error').hide();
        $btn.prop('disabled', true).text('Sending...');

        $.post('/contact/send', $(this).serialize(), function (res) {
            $btn.prop('disabled', false).text(origText);
            if (res && (res.code == 1 || res.code == 200)) {
                $fb.addClass('success').text(res.msg || 'Your message has been sent!').show();
                $('#js-contact-form')[0].reset();
            } else {
                $fb.addClass('error').text((res && res.msg) ? res.msg : 'Failed to send message.').show();
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false).text(origText);
            $fb.addClass('error').text('Network error. Please try again.').show();
        });
    });
});
