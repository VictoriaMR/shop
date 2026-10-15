$(function() {
	SITELOG.init();
});

const SITELOG = {
	url: '',
	init: function() {
		const _this = this;
		_this.url = $('#site-log-page').data('url') || '';

		// 日志类型前端快速筛选
		$('.log-type-filter button').on('click', function() {
			const btn = $(this);
			const type = btn.data('type');
			btn.addClass('active').siblings().removeClass('active');
			if (type === 'all') {
				$('#data-list .log-row').show();
			} else {
				$('#data-list .log-row').each(function() {
					if ($(this).data('type') === type) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		});

		// 删除日志
		$('#site-log-page').on('click', '.btn.delete', function() {
			const obj = $(this);
			const name = obj.data('id');
			const tip = name === 'all' ? '确认要清除全部运行日志文件吗?' : '确认要删除日志文件 [' + name + '] 吗?';
			confirm(tip, function(btn, modal) {
				obj.button('loading');
				post(_this.url, { opn: 'deleteLog', name: name }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						obj.button('reset');
						if (modal) modal.hide();
					}
				});
			});
		});
	}
};
