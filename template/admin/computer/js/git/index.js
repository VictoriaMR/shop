$(function(){
	GITINDEX.init();
});

const GITINDEX = {
	init: function() {
		// 同步版本库
		$('.update-git-btn').on('click', function(){
			const btn = $(this);
			const id = btn.data('id');
			btn.button('loading');
			post('', {opn: 'updateGit', id: id}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					setTimeout(function(){
						window.location.reload();
					}, 600);
				} else {
					btn.button('reset');
				}
			});
		});

		// 发布最新版本
		$('.release-latest-btn').on('click', function(){
			const btn = $(this);
			const id = btn.data('id');
			confirm('确定要将最新版本及之前的所有未发布提交标记为已发布吗?', function(confirmBtn, modal){
				btn.button('loading');
				post('', {opn: 'releaseGit', id: id}, function(res){
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						btn.button('reset');
						modal.hide();
					}
				});
			});
		});
	}
};
