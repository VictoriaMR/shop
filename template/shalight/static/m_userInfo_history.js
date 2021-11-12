$(function(){
	HISTORY.init();
});
const HISTORY = {
	init: function() {
		//删除
		$('#history-page .product-list').on('click', '.remove-btn', function(event){
			event.stopPropagation();
			const obj = $(this).parent();
			const id = obj.data('key');
			TIPS.loading(obj);
			$.post(URI+'userInfo/deleteHistory', {id:id}, function(res){
				TIPS.loadout(obj);
				if (res.code === '200') {
					TIPS.success(res.message);
					obj.fadeOut(300, function(){
						if ($(this).parent().find('.item').length == 1) {
							$(this).parent().remove();
						} else {
							$(this).remove();
						}
						if ($('#history-page .product-list .item').length == 0) {
							window.location.reload();
						}
					});
				} else {
					TIPS.error(res.message);
				}
			});
			return false;
		});
		//收藏
		$('.product-list').on('click', '.like-block', function(event){
			event.stopPropagation();
			const obj = $(this).parents('.item');
			const _thisObj = $(this);
			const id = obj.data('id');
			TIPS.loading(obj);
			$.post(URI+'userInfo/wish', {spu_id: id}, function(res){
				TIPS.loadout(obj);
				if (res.code === '200') {
					if (res.data === 1) {
						_thisObj.find('.icon-xihuan').removeClass('icon-xihuan').addClass('icon-xihuanfill');
					} else {
						_thisObj.find('.icon-xihuanfill').removeClass('icon-xihuanfill').addClass('icon-xihuan');
					}
				} else if (res.code === '10001') {
					window.location.href = URI+'login.html';
				}
			});
			return false;
		});
		//分页加载
		
	},
};