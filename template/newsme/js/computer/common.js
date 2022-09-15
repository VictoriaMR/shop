var MEMBER = {
    init: function(data) {
        if (data.mem_id) {
            $('#meau-modal .email-empty').addClass('hidden');
            if (data.email) {
                $('#meau-modal .email').removeClass('hidden').text(data.email);
            }
            $('#meau-modal .login').addClass('hidden');
            $('#meau-modal .logout').removeClass('hidden');
            $('.userinfo .register').addClass('hidden');
            $('.userinfo .member').removeClass('hidden').text(data.nickname ? data.nickname : data.email);
        }
    }
};
$(function(){
    // 菜单栏点击
    $('#meau-modal-icon,#meau-modal-name').on('click', function(){
        var obj = $('#meau-modal');
        obj.fadeIn(50);
        obj.find('.popper').animate({'left':'0'}, 200, 'linear');
        TIPS.stop();
    });
    //弹窗点击收起
    $('.modal .mask,.modal .close').on('click', function(){
        var obj = $(this).parents('.modal');
        var width = obj.find('.popper').width();
        obj.find('.popper').animate({'left': -width}, 200, 'linear', function(){
            obj.fadeOut(100);
            TIPS.start();
        });
    });
    //滚动导航栏
    var meauHeaderObj = $('.meau-header');
    var leftFilterObj = $('#left-filter');
    var rightListObj = $('#right-list');
    var meauHeaderTop, leftFilterTop, leftFilterLeft, rightListHeight;
    if (meauHeaderObj.length>0) {
        meauHeaderTop = meauHeaderObj.offset().top
    }
    if (leftFilterObj.length>0) {
        leftFilterTop = leftFilterObj.offset().top;
        leftFilterLeft = leftFilterObj.offset().left;
        leftFilterObj.find('.content').css({left: leftFilterLeft+6});
    }
    if (rightListObj.length>0) {
        rightListHeight = rightListObj.height();
    }
    //搜索按钮点击
    $('.meau-header .search-cell .icon-sousuo').on('click', function(){
        var obj = $('.meau-header [name="keyword"]');
        if(obj.val()) {
            obj.parent().submit();
        } else {
            obj.focus();
        }
    });
    $(document).scroll(function() {
        var scroll = $(document).scrollTop();
        if (meauHeaderTop) {
            if(scroll >= meauHeaderTop){
                meauHeaderObj.addClass('fixed');
            }else{
                meauHeaderObj.removeClass('fixed');
            }
        }
        if (leftFilterTop && rightListHeight > 700) {
            if (scroll + 50 >= leftFilterTop) {
                leftFilterObj.find('.content').addClass('fixed');
            } else {
                leftFilterObj.find('.content').removeClass('fixed');
            }
        }
    });
});