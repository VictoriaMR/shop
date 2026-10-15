$(function() {
	TRANSFER.init();
});

const TRANSFER = {
	url: '',
	init: function() {
		this.url = $('#transfer-page').data('url') || '/transfer';
		const _this = this;

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 重构缓存按钮
		$('.btn.reload').on('click', function() {
			const $btn = $(this);
			$btn.button('loading');
			post(_this.url, { opn: 'reloadCache' }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
			});
		});

		// 修改按钮
		$('#data-list').on('click', '.btn.modify', function() {
			const $btn = $(this);
			const $item = $btn.closest('.item');
			const id = $item.data('id');
			let rowData = $item.data('info') || null;
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch (e) {}
			}

			$btn.button('loading');
			post(_this.url, { opn: 'getInfo', id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1) {
					const info = (res.data && typeof res.data === 'object') ? res.data : (typeof res.msg === 'object' ? res.msg : rowData);
					_this.initModel(info || rowData);
				} else if (rowData && rowData.tran_id) {
					_this.initModel(rowData);
				} else {
					showTips(res || { code: 0, msg: '获取翻译详情失败' });
				}
			});
		});

		// 保存按钮
		$('#dealbox').on('click', '.btn.save', function() {
			const value = $.trim($('#dealbox [name="value"]').val());
			if (value === '') {
				errorTips('翻译不能为空');
				return false;
			}
			const $btn = $(this);
			$btn.button('loading');
			post(_this.url, $('#transfer-form').serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$('#dealbox').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 600);
				} else {
					$btn.button('reset');
				}
			});
		});

		// 自动翻译
		$('#dealbox').on('click', '.auto-transfer-btn', function() {
			const $btn = $(this);
			const sourceText = $('#dealbox [name="name"]').val();
			const code = $('#dealbox [name="type"]').val();
			$btn.button('loading');
			post(_this.url, { opn: 'autoTransfer', value: sourceText, code: code }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1) {
					const translated = (typeof res.data === 'string' && res.data !== '') ? res.data : (typeof res.msg === 'string' ? res.msg : '');
					if (translated !== '') {
						$('#dealbox [name="value"]').val(translated);
						successTips('自动翻译完成');
					}
				} else {
					showTips(res);
				}
			});
		});
	},
	initModel: function(data) {
		data = data || {
			name: '',
			tran_id: '0',
			type: '',
			value: '',
			type_name: ''
		};
		const $box = $('#dealbox');
		$box.find('[name="tran_id"]').val(data.tran_id || 0);
		$box.find('[name="type"]').val(data.type || '');
		$box.find('[name="name"]').val(data.name || '');
		$box.find('[name="name_display"]').val(data.name || '');
		$box.find('[name="type_name"]').val((data.type || '') + (data.type_name ? ' / ' + data.type_name : ''));
		$box.find('[name="value"]').val(data.value || '');
		$box.fadeIn(150);
	}
};
