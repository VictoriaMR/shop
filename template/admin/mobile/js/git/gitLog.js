$(function() {
	GITLOG.init();
});

const GITLOG = {
	url: '',
	init: function() {
		this.url = $('#git-log-page').data('url') || '';
		const _this = this;

		// 版本库筛选切换
		$('.lib-filter button').on('click', function() {
			const id = $(this).data('id');
			const form = $(this).closest('form');
			form.find('input[name="id"]').val(id);
			form.submit();
		});

		// 发布状态筛选切换
		$('.status-filter button').on('click', function() {
			const status = $(this).data('status');
			const form = $(this).closest('form');
			form.find('input[name="status"]').val(status);
			form.submit();
		});

		// 同步更新版本库
		$('#update-git').on('click', function() {
			const btn = $(this);
			const id = btn.data('id');
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

		// 发布指定版本
		$('#data-list').on('click', '.release-btn', function() {
			const btn = $(this);
			const id = btn.closest('.item').data('id');
			if (!id) return;

			confirm('确定要发布该版本（包含此版本之前未发布的提交）吗?', function(confirmBtn, modal) {
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
