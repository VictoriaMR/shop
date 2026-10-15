$(function(){
	SITECACHE.init();
});

const SITECACHE = {
	init: function() {
		$('.btn.delete').on('click', function(){
			const obj = $(this);
			const name = obj.data('id');
			const tip = name === 'all' ? '确认要清除全部静态资源缓存文件吗?' : '确认要删除缓存文件 [' + name + '] 吗?';
			confirm(tip, function(btn, modal){
				obj.button('loading');
				post('', {opn: 'deleteStaticCache', name: name}, function(res){
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						obj.button('reset');
						modal.hide();
					}
				});
			});
		});
	}
};