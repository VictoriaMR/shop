$(function() {
	FAQ_GROUP_MOBILE.init();
});

const FAQ_GROUP_MOBILE = {
	url: '',
	init: function() {
		var $page = $('#faq-page');
		this.url = $page.data('url') || '/faq/index';
		var _this = this;

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 状态下拉自动查询
		$('.filter-select').on('change', function() {
			$(this).closest('form').submit();
		});

		// 新增分组
		$('.add-btn').on('click', function() {
			_this.openEditModal('新增分组', {
				group_id: 0,
				name: '',
				status: 1
			});
		});

		// 编辑分组
		$('#data-list').on('click', '.btn.modify', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $card = $btn.closest('.item');
			var id = $card.data('id');
			if (!id) return;

			$btn.button('loading');
			post(_this.url, { opn: 'getGroupInfo', group_id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.openEditModal('编辑分组', res.data);
				} else {
					showTips(res || { code: 0, msg: '获取分组信息失败' });
				}
			});
		});

		// 保存分组
		$('#dealbox-info').on('click', '.save-btn', function() {
			var $btn = $(this);
			var $form = $('#group-form');
			var name = $.trim($form.find('input[name="name"]').val());
			if (!name) {
				errorTips('分组名称不能为空');
				return false;
			}
			$btn.button('loading');
			post(_this.url, $form.serializeArray(), function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$('#dealbox-info').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 500);
				}
			});
		});

		// 状态切换开关
		$('#data-list').on('click', '.switch_botton', function(e) {
			e.stopPropagation();
			var $sw = $(this);
			var $card = $sw.closest('.item');
			var id = $card.data('id');
			var status = parseInt($sw.data('status') || 0, 10) === 1 ? 0 : 1;

			post(_this.url, { opn: 'modifyGroupStatus', group_id: id, status: status }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$sw.switchBtn(status);
				}
			});
		});

		// 删除分组
		$('#data-list').on('click', '.btn.delete', function(e) {
			e.stopPropagation();
			var $card = $(this).closest('.item');
			var id = $card.data('id');
			var name = $.trim($card.find('.group-name').text());

			confirm('确定要删除分组【' + name + '】吗？', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteGroup', group_id: id }, function(res) {
					btn.button('reset');
					if (modal && modal.hide) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (err) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						$card.fadeOut(200, function() {
							$card.remove();
							if ($('#data-list .item').length === 0) {
								window.location.reload();
							}
						});
					}
				});
			});
		});

		// 多语言配置弹窗
		$('#data-list').on('click', '.lang-btn', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $card = $btn.closest('.item');
			var id = $card.data('id');
			var name = $.trim($card.find('.group-name').text());

			$btn.button('loading');
			post(_this.url, { opn: 'getGroupLanguage', group_id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1) {
					var data = (res.data && res.data.length) ? res.data : (Array.isArray(res.msg) ? res.msg : []);
					var $modal = $('#dealbox-language');
					$modal.find('input[name="group_id"]').val(id);
					$modal.find('input[name="name"]').val(name);
					$modal.find('.source-name-text').text(name + ' (#' + id + ')');

					var $list = $modal.find('.language-list').empty();
					$.each(data, function(i, row) {
						var $control = $('<textarea rows="2" class="form-control transfer" autocomplete="off"></textarea>')
							.attr('name', 'language[' + row.lan_id + ']')
							.attr('data-tr_code', row.tr_code || '')
							.attr('placeholder', '请输入 ' + (row.language_name || '') + ' 名称')
							.val(row.name || '');
						$('<div class="lang-item">')
							.append($('<span class="lang-addon">').text((row.language_name || row.lan_id) + '：'))
							.append($control)
							.appendTo($list);
					});
					$modal.fadeIn(150);
				} else {
					showTips(res || { code: 0, msg: '获取多语言配置失败' });
				}
			});
		});

		// 智能翻译空白项
		$('#dealbox-language').on('click', '.translate-all-btn', function() {
			var $btn = $(this);
			var name = $('#dealbox-language input[name="name"]').val();
			var $allInputs = $('#dealbox-language .language-list .transfer');
			if (!name && $allInputs.length > 0) {
				name = $.trim($allInputs.first().val());
			}
			var $emptyInputs = $allInputs.filter(function() {
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
				post(_this.url, { opn: 'transfer', tr_code: $input.data('tr_code'), name: name, from_code: 'en' }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (err) {}
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

		// 保存多语言
		$('#dealbox-language').on('click', '.save-btn', function() {
			var $btn = $(this);
			$btn.button('loading');
			post(_this.url, $('#group-lang-form').serializeArray(), function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$('#dealbox-language').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 500);
				}
			});
		});
	},

	openEditModal: function(title, data) {
		data = data || { group_id: 0, name: '', status: 1 };
		var $box = $('#dealbox-info');
		$box.find('.dealbox-title').text(title);
		$box.find('[name="group_id"]').val(data.group_id || 0);
		$box.find('[name="name"]').val(data.name || '');
		$box.find('[name="status"]').val(typeof data.status !== 'undefined' ? data.status : 1);
		$box.fadeIn(150);
	}
};
