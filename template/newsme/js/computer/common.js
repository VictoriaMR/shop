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
    var meauHeaderHeight = $('.meau-header').outerHeight();
    $(document).scroll(function() {
        if($(document).scrollTop() >= meauHeaderHeight){
            if (!$('.meau-header').addClass('fixed')) {
                $('.meau-header').addClass('fixed');
            }
        }else{
            $('.meau-header').removeClass('fixed');
        }
    });
});