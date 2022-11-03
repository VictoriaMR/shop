$(function(){
    CARTPAGE.init();
});
var CARTPAGE = {
    init: function() {
        var _this = this;
        //加减数量
        $('.quantity-content .item').on('click', function(){
            if ($(this).attr('disabled') == 'disabled') {
                return false;
            }
            var obj = $(this).parent();
            var stock = parseInt(obj.data('stock'));
            var numObj = obj.find('input');
            var num = parseInt(numObj.val());
            var pObj = obj.parents('tr');
            var maskObj = obj.parents('tr').find('td').eq(0);
            var id = pObj.data('id');
            if (stock === 1) {
                num = 1;
            } else {
                if ($(this).hasClass('plus')) {
                    num = num + 1;
                } else {
                    num = num - 1;
                }
                if (num >= stock) {
                    num = stock;
                }
            }
            if (num <= 0) {
                num = 1;
            }
            TIPS.loading(maskObj, true);
            $.post('/cart/updateQuantity', {id:id, quantity: num}, function(res){
                TIPS.loadout(maskObj);
                if (res.code === '200') {
                    numObj.val(num);
                    _this.initQuantity(obj);
                    _this.initSummary();
                } else {
                    TIPS.error(res.message);
                }
            });
        });
        //直接输入更改
        $('.quantity-content input').on('blur', function(){
            var obj = $(this).parents('.quantity-content');
            var stock = parseInt(obj.data('stock'));
            var num = parseInt($(this).val());
            var pObj = obj.parents('tr');
            var maskObj = obj.parents('tr').find('td').eq(0);
            var id = pObj.data('id');
            if (stock === 1) {
                num = 1;
            } else if (num >= stock) { 
                num = stock;
            }
            if (num <= 0) {
                num = 1;
            }
            $(this).val(num);
            TIPS.loading(maskObj, true);
            $.post('/cart/updateQuantity', {id:id, quantity: num}, function(res){
                TIPS.loadout(maskObj);
                if (res.code === '200') {
                    pObj.find('.table').removeClass('opac5').find('.btn-error').remove();
                    _this.initQuantity(obj);
                    _this.initSummary();
                } else {
                    TIPS.error(res.message);
                }
            });
        });
        //收藏
        $('.btn-content .wish').on('click', function(){
            var _thisObj = $(this);
            var obj = _thisObj.parents('tr');
            var maskObj = obj.find('td').eq(0);
            var id = obj.data('pid');
            TIPS.loading(maskObj, true);
            $.post('/userInfo/wish',{spu_id:id}, function(res) {
                TIPS.loadout(maskObj);
                if (res.code === '200') {
                    _thisObj.text(res.data==1?'Move from wishlist':'Save for wishlist');
                } else {
                    TIPS.error(res.message);
                }
            });
        });
        //删除
        $('.btn-content .delete').on('click', function(){
            var obj = $(this).parents('tr');
            var maskObj = obj.find('td').eq(0);
            var id = obj.data('id');
            TIPS.loading(maskObj, true);
            $.post('/cart/remove', {id: id}, function(res){
                if (res.code === '200') {
                    if ($('.list-left table>tbody>tr').length <= 1) {
                        setTimeout(function(){
                            window.location.reload();
                        }, 200);
                    } else {
                        setTimeout(function(){
                            obj.remove();
                            TIPS.loadout(obj);
                            _this.initSummary();
                        }, 200);
                    }
                } else {
                    TIPS.loadout(maskObj);
                    TIPS.error(res.message);
                }
            });
        });
        //编辑
        $('.cart-list.checked .attr-content').on('click', function(){
            const htmlObj = $('#sku-select-modal .contentfill');
            htmlObj.html('');
            TIPS.loading(htmlObj);
            $('#sku-select-modal').show().find('.mask').show();
            $('#sku-select-modal .dialog').addClass('popup');
            const id = $(this).parents('.item').data('id');
            _this.cart_id = id;
            $.post('/cart/editInfo', {id:id}, function(res){
                TIPS.loadout(htmlObj, true);
                if (res.code === '200') {
                    _this.initEditPage(res.data);
                } else {
                    $('#sku-select-modal .dialog').removeClass('popup');
                    $('#sku-select-modal .mask').fadeOut(300, function(){
                        TIPS.start();
                        $(this).parent().hide();
                    });
                    TIPS.error(res.message);
                }
            });
        });
    },
    initQuantity: function(pObj) {
        var stock = parseInt(pObj.data('stock'));
        var num = parseInt(pObj.find('input').val());
        if (stock <= 1) {
            pObj.find('.plus').attr('disabled', true).addClass('disabled');
            pObj.find('.minus').attr('disabled', true).addClass('disabled');
        } else {
            if (num === 1) {
                pObj.find('.minus').attr('disabled', true).addClass('disabled');
                pObj.find('.plus').attr('disabled', false).removeClass('disabled');
            } else {
                pObj.find('.minus').attr('disabled', false).removeClass('disabled');
                if (num >= stock) {
                    pObj.find('.plus').attr('disabled', true).addClass('disabled');
                } else {
                    pObj.find('.plus').attr('disabled', false).removeClass('disabled');
                }
            }
        }
    },
    initSummary: function(){
        var maskObj = $('.summary-right .relative');
        TIPS.loading(maskObj, true);
        $.post('/cart/cartSummary', {}, function(res){
            var html = '';
            var priceTotal = 0;
            if (res.code === '200') {
                for (var i=0; i<res.data.length; i++) {
                    if (res.data[i].type == 2) {
                        priceTotal = res.data[i].price;
                    }
                    html += '<div class="item">\
                            <p data-type="'+res.data[i].type+'">\
                                <span>'+res.data[i].name+'</span>\
                                <span class="right">'+res.data[i].price_format+'</span>\
                            </p>\
                        </div>';
                }
            }
            $('.summary-right .summary-content').html(html);
            TIPS.loadout(maskObj);
            priceChange(priceTotal);
        });
    }
};