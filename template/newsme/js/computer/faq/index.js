$(function(){
    $('.group-list li').on('click', function(){
        if ($(this).hasClass('active')) {
            return false;
        }
        $(this).addClass('active').siblings().removeClass('active');
        var gid = $(this).data('gid');
        $('.faq-list .item[data-gid="'+gid+'"]').show().siblings().hide();
    })
});