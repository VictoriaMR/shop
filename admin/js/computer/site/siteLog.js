$(function(){
	SITELOG.init();
});
const SITELOG = {
	init: function() {
		$('.btn.delete').on('click', function(){
			const obj = $(this);
			confirm('确认删除吗?', function(){
				const name = obj.data('id');
				obj.button('loading');
				post(URI+'site/siteLog', {opn: 'deleteLog', name: name}, function(){
					window.location.reload();
				});
			})
		});
	}
};