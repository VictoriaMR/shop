$(function(){
    // 菜单栏点击
    $('#meau-modal-icon,#meau-modal-name').on('click', function(){
        var obj = $('#meau-modal');
        obj.fadeIn(50);
        obj.find('.popper').animate({'left':'0'}, 200, 'linear');
    });
    //弹窗点击收起
    $('.modal .mask,.modal .close').on('click', function(){
        var obj = $(this).parents('.modal');
        var width = obj.find('.popper').width();
        obj.find('.popper').animate({'left': -width}, 200, 'linear', function(){
            obj.fadeOut(100);
        });
    });
    //滚动导航栏
    var meauHeaderTop = $('.meau-header').offset().top;
    var leftFilterTop = $('#left-filter').offset().top;
    var rightListHeight = $('#right-list').height();
    $(document).scroll(function() {
        var scroll = $(document).scrollTop();
        if (meauHeaderTop) {
            if(scroll >= meauHeaderTop){
                $('.meau-header').addClass('fixed');
            }else{
                $('.meau-header').removeClass('fixed');
            }
        }
        if (leftFilterTop && rightListHeight > 700) {
            if (scroll + 50 >= leftFilterTop) {
                $('#left-filter .content').addClass('fixed');
            } else {
                $('#left-filter .content').removeClass('fixed');
            }
        }
    });
});