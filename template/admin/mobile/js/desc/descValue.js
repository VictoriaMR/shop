$(function() {
	DESC_VALUE.init();
});

const DESC_VALUE = {
	url: '',
	init: function() {
		this.url = $('#descvalue-list').data('url') || '';
		var _this = this;

		// 状态筛选
		$('.status-filter .btn').on('click', function() {
			var form = $(this).closest('form');
			form.find('input[name="status"]').val($(this).data('status'));
			form.submit();
		});

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 新增描述值
		$('.btn.add').on('click', function() {
			_this.initData();
		});

		// 修改描述值
		$('#data-list').on('click', '.btn.modify', function() {
			var $btn = $(this);
			var $item = $btn.closest('.item');
			var id = $item.data('id');
			if (!id) return;

			$btn.button('loading');
			post(_this.url, { opn: 'getDescValueInfo', id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.initData(res.data);
				} else {
					showTips(res || { code: 0, msg: '获取描述值失败' });
				}
			});
		});

		// 多语言配置
		$('#data-list').on('click', '.btn.language', function() {
			var $btn = $(this);
			var $item = $btn.closest('.item');
			var id = $item.data('id');
			var name = $item.data('name');
			if (!id) return;

			$btn.button('loading');
			post(_this.url, { opn: 'getDescValueLanguage', id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.initLanguage(id, name, res.data);
				} else {
					showTips(res || { code: 0, msg: '获取多语言配置失败' });
				}
			});
		});

		// 智能翻译（仅翻译空白项）
		$('#dealbox-language').on('click', '.translate-all-btn', function() {
			var $btn = $(this);
			var name = $('#dealbox-language input[name="name"]').val();
			var $emptyInputs = $('#dealbox-language .language-list input.lang-input').filter(function() {
				return $.trim($(this).val()) === '';
			});
			var len = $emptyInputs.length;
			if (len === 0) {
				successTips('所有语言均已填写，无需翻译');
				return;
			}
			$btn.button('loading');
			$emptyInputs.each(function() {
				var $input = $(this);
				post(_this.url, { opn: 'transfer', tr_code: $input.data('tr_code'), name: name }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					if (res && res.code == 1) {
						$input.val(res.data);
					} else {
						showTips(res);
					}
					len--;
					if (len <= 0) {
						$btn.button('reset');
					}
				});
			});
		});

		// 保存描述值
		$('#dealbox').on('click', '.save-btn', function() {
			var $form = $('#descv-form');
			var name = $.trim($form.find('input[name="name"]').val());
			if (name === '') {
				errorTips('描述值名称不能为空');
				return false;
			}
			_this.submit($(this), $form);
		});

		// 保存多语言
		$('#dealbox-language').on('click', '.save-btn', function() {
			_this.submit($(this), $('#descv-lang-form'));
		});

		// 删除描述值
		$('#data-list').on('click', '.btn.delete', function() {
			var $item = $(this).closest('.item');
			var id = $item.data('id');
			if (!id) return;

			confirm('确定要删除该描述值吗？删除后关联配置也将被清理！', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteDescValueInfo', id: id }, function(res) {
					btn.button('reset');
					if (modal) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						$item.fadeOut(200, function() {
							$item.remove();
						});
					}
				});
			});
		});
	},

	submit: function($btn, $form) {
		$btn.button('loading');
		post(this.url, $form.serializeArray(), function(res) {
			$btn.button('reset');
			if (typeof res === 'string') {
				try { res = JSON.parse(res); } catch (e) {}
			}
			showTips(res);
			if (res && res.code == 1) {
				$btn.closest('.modal-mask').fadeOut(150);
				setTimeout(function() {
					window.location.reload();
				}, 500);
			}
		});
	},

	initData: function(data) {
		data = data || { descv_id: 0, name: '' };
		var $box = $('#dealbox');
		$box.find('.dealbox-title').text(data.descv_id > 0 ? '编辑描述值' : '新增描述值');
		$box.find('input[name="id"]').val(data.descv_id || 0);
		$box.find('input[name="name"]').val(data.name || '');
		$box.fadeIn(150);
	},

	initLanguage: function(id, name, data) {
		var $box = $('#dealbox-language');
		$box.find('input[name="id"]').val(id);
		$box.find('input[name="name"]').val(name);
		$box.find('.source-name-text').text(name + ' (#' + id + ')');
		var $list = $box.find('.language-list').empty();
		$.each(data, function(lanId, item) {
			var $input = $('<input type="text" class="form-control lang-input" maxlength="500" autocomplete="off">')
				.attr('name', 'language[' + lanId + ']')
				.attr('data-tr_code', item.tr_code || '')
				.attr('placeholder', '请输入 ' + (item.language_name || '') + ' 翻译')
				.val(item.name || '');
			$('<div class="lang-item">')
				.append($('<span class="lang-addon">').text((item.language_name || lanId) + '：'))
				.append($input)
				.appendTo($list);
		});
		$box.fadeIn(150);
	}
};
