$(function () {
    $(document).on('click', '.js-faq-trigger', function () {
        var $item = $(this).closest('.faq-accordion-item');
        $item.toggleClass('is-open');
    });
});
