$(function() {
	GITINDEX.init();
});

const GITINDEX = {
	url: '',
	init: function() {
		this.url = $('#git-list').data('url') || '';
		const _this = this;

		// 同步版本库
		$('#data-list').on('click', '.update-git-btn', function() {
			const btn = $(this);
			const id = btn.data('id');
			if (!id) return;

			btn.button('loading');
			post(_this.url, { opn: 'updateGit', id: id }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					setTimeout(function() {
						window.location.reload();
					}, 600);
				} else {
					btn.button('reset');
				}
			});
		});

		// 发布最新版本
		$('#data-list').on('click', '.release-latest-btn', function() {
			const btn = $(this);
			const id = btn.data('id');
			if (!id) return;

			confirm('确定要将最新版本及之前的所有未发布提交标记为已发布吗?', function(confirmBtn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'releaseGit', id: id }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						btn.button('reset');
						if (modal) modal.hide();
					}
				});
			});
		});
	}
};
