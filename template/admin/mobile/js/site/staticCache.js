$(function() {
	SITECACHE.init();
});

const SITECACHE = {
	url: '',
	init: function() {
		const _this = this;
		_this.url = $('#static-cache-page').data('url') || '';

		// 扩展名类型筛选
		$('.ext-type-filter button').on('click', function() {
			const btn = $(this);
			const ext = btn.data('ext');
			btn.addClass('active').siblings().removeClass('active');
			if (ext === 'all') {
				$('#data-list .static-card').show();
			} else {
				$('#data-list .static-card').each(function() {
					if ($(this).data('ext') === ext) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		});

		// 删除静态缓存
		$('#static-cache-page').on('click', '.btn.delete', function() {
			const obj = $(this);
			const name = obj.data('id');
			const tip = name === 'all' ? '确认要清除全部静态资源缓存文件吗?' : '确认要删除缓存文件 [' + name + '] 吗?';
			confirm(tip, function(btn, modal) {
				obj.button('loading');
				post(_this.url, { opn: 'deleteStaticCache', name: name }, function(res) {
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
