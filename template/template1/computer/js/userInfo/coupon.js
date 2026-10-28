$(function () {
    $('.js-coupon-tab').on('click', function () {
        $('.js-coupon-tab').removeClass('is-active');
        $(this).addClass('is-active');
    });

    $('#js-coupon-redeem-form').on('submit', function (e) {
        e.preventDefault();
        var code = $.trim($('#js-coupon-code').val());
        if (!code) {
            alert('Please enter a promo or voucher code.');
            return;
        }
        alert('Promo code "' + code + '" is invalid or has expired.');
    });
});
