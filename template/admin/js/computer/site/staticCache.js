$(function(){
	SITECACHE.init();
});
const SITECACHE = {
	init: function() {
		$('.btn.delete').on('click', function(){
			const obj = $(this);
			confirm('确认删除吗?', function(){
				var name = obj.data('id');
				obj.button('loading');
				post(URI+'site/staticCache', {opn: 'deleteStaticCache', name: name}, function(res){
					showTips(res);
					if (res.code == 200) {
						window.location.reload();
					} else {
						obj.button('reset');
					}
				});
			})
		});
	}
};