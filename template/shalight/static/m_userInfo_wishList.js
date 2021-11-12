$(function(){
	WISH.init();
});
const WISH = {
	init: function() {
		//删除
		$('#wish-page .product-list').on('click', '.remove-btn', function(event){
			event.stopPropagation();
			const obj = $(this).parent();
			const id = obj.data('id');
			TIPS.loading(obj);
			$.post(URI+'userInfo/wish', {spu_id:id}, function(res){
				TIPS.loadout(obj);
				if (res.code === '200') {
					TIPS.success(res.message);
					obj.fadeOut(200, function(){
						$(this).remove();
					});
				} else {
					TIPS.error(res.message);
				}
			});
			return false;
		});
		//分页加载
		
	},
};