$(function() {
	SITE.init();
});

const SITE = {
	url: '',
	init: function() {
		const _this = this;
		_this.url = $('#site-page').data('url') || '';

		// 状态筛选
		$('.status-filter button').on('click', function() {
			const status = $(this).data('status');
			const form = $(this).closest('form');
			form.find('input[name="status"]').val(status);
			form.submit();
		});

		// 新增按钮
		$('#site-page .add-btn').on('click', function() {
			_this.initInfo('新增站点');
		});

		// 编辑按钮（优先读取卡片 data-info，零额外请求）
		$('#data-list').on('click', '.modify', function() {
			const card = $(this).closest('.item');
			let rowData = card.data('info') || null;
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch (e) {}
			}
			if (rowData && rowData.site_id) {
				_this.initInfo('编辑站点', rowData);
				return;
			}
			const obj = $(this);
			obj.button('loading');
			post(_this.url, { opn: 'getInfo', id: card.data('id') }, function(res) {
				obj.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1) {
					_this.initInfo('编辑站点', res.data);
				} else {
					showTips(res);
				}
			});
		});

		// 弹窗内状态开关
		$('#dealbox-info .modal-status-switch').on('click', function() {
			const sw = $(this);
			const next = parseInt(sw.attr('data-status'), 10) === 1 ? 0 : 1;
			sw.switchBtn(next);
			$('#dealbox-info input[name="status"]').val(next);
		});

		// 关闭弹窗
		$(document).on('click', '#dealbox-info .modal-close', function() {
			$('#dealbox-info').fadeOut(150);
		});
		$(document).on('click', '#dealbox-info', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 保存按钮
		$('#dealbox-info .save-btn').on('click', function() {
			const obj = $(this);
			const form = $('#dealbox-info form');
			const name = $.trim(form.find('[name="name"]').val());
			const path = $.trim(form.find('[name="path"]').val());
			if (name === '' || path === '') {
				errorTips('站点名称和模板目录不能为空');
				return false;
			}
			obj.button('loading');
			post(_this.url, form.serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$('#dealbox-info').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 600);
				} else {
					obj.button('reset');
				}
			});
		});

		// 列表状态开关
		$('#data-list').on('click', '.site-status-switch', function() {
			const _thisobj = $(this);
			const id = _thisobj.closest('.item').data('id');
			const status = parseInt(_thisobj.attr('data-status'), 10) === 0 ? 1 : 0;
			post(_this.url, { opn: 'modifySite', id: id, status: status }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					_thisobj.switchBtn(status);
				}
			});
		});
	},

	initInfo: function(title, data) {
		if (!data) {
			data = {
				site_id: 0,
				name: '',
				type: 'home',
				domain: '',
				path: '',
				email: '',
				view_cache: 1,
				static_cache: 1,
				debug: 0,
				status: 1
			};
		}
		const obj = $('#dealbox-info');
		obj.find('.dealbox-title').text(title);
		obj.find('[name="site_id"]').val(data.site_id || 0);
		obj.find('[name="name"]').val(data.name || '');
		obj.find('[name="type"]').val(data.type || 'home');
		obj.find('[name="domain"]').val(data.domain || '');
		obj.find('[name="path"]').val(data.path || '');
		obj.find('[name="email"]').val(data.email || '');
		obj.find('[name="view_cache"]').prop('checked', parseInt(data.view_cache, 10) === 1);
		obj.find('[name="static_cache"]').prop('checked', parseInt(data.static_cache, 10) === 1);
		obj.find('[name="debug"]').prop('checked', parseInt(data.debug, 10) === 1);

		const st = parseInt(data.status, 10) === 1 ? 1 : 0;
		obj.find('[name="status"]').val(st);
		obj.find('.modal-status-switch').switchBtn(st);

		obj.fadeIn(150);
		return true;
	}
};
