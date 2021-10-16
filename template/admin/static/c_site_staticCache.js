var SITECACHE = {
	init: function() {
		$('.btn.delete').on('click', function(){
			var obj = $(this);
			confirm('确认删除吗?', function(){
				var name = obj.data('id');
				obj.button('loading');
				post(URI+'site/staticCache', {opn: 'deleteStaticCache', name: name}, function(){
					window.location.reload();
				});
			})
		});
	}
};