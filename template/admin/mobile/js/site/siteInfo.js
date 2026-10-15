$(function() {
	SITEINFO.init();
});

const SITEINFO = {
	id: 0,
	url: '',
	init: function() {
		const _this = this;
		const $page = $('#site-page');
		_this.id = $page.data('id');
		_this.url = $page.data('url') || '';
		if (!_this.id) {
			return;
		}

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 修改站点名称
		$('#site-name').on('click', function() {
			_this.openFieldModal('修改站点名称', '站点名称：', 'name', $.trim($(this).find('b').text()));
		});

		// 修改模板目录
		$('#site-path').on('click', function() {
			_this.openFieldModal('修改模板目录', '模板目录：', 'path', $.trim($(this).find('code').text()));
		});

		// 修改绑定域名
		$('#site-domain-edit').on('click', function() {
			let val = $.trim($(this).find('code').text());
			if (val === '--') val = '';
			_this.openFieldModal('修改绑定域名', '绑定域名：', 'domain', val);
		});

		// 修改联系邮箱
		$('#site-email-edit').on('click', function() {
			let val = $.trim($(this).find('span').text());
			if (val === '--') val = '';
			_this.openFieldModal('修改联系邮箱', '联系邮箱：', 'email', val);
		});

		// 保存基础字段修改
		$('#field-info .save-btn').on('click', function() {
			const btn = $(this);
			const form = btn.closest('form');
			btn.button('loading');
			post(_this.url, form.serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
		});

		// 运行开关切换 (status / debug / view_cache / static_cache)
		$('#site-page .site-switch').on('click', function() {
			const sw = $(this);
			const field = sw.data('field');
			const next = parseInt(sw.attr('data-status'), 10) === 1 ? 0 : 1;
			const param = { opn: 'modifySite', id: _this.id };
			param[field] = next;
			post(_this.url, param, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					sw.switchBtn(next);
				}
			});
		});

		// 打开管理语言弹窗
		$('#add-language').on('click', function() {
			$('#language-info').fadeIn(150);
		});

		// 打开管理货币弹窗
		$('#add-currency').on('click', function() {
			$('#currency-info').fadeIn(150);
		});

		// 添加语言
		$('#language-info').on('click', '.btn.add', function() {
			const btn = $(this);
			const code = btn.closest('[data-code]').data('code');
			btn.button('loading');
			post(_this.url, { opn: 'addLanguage', code: code, site_id: _this.id }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
		});

		// 删除语言
		$('#language-info, #language-table').on('click', '.btn.delete', function() {
			const item = $(this).closest('[data-code]');
			const code = item.data('code');
			const id = item.data('id') || 0;
			confirm('确认要移除站点语言 [' + code + '] 吗?', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteLanguage', id: id, code: code, site_id: _this.id }, function(res) {
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

		// 语言排序（数值输入）
		$('#language-table').on('change', '[name="sort"]', function() {
			const id = $(this).closest('[data-id]').data('id');
			const sort = $(this).val();
			post(_this.url, { opn: 'sortLanguage', id: id, sort: sort }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 语言排序（置顶/上移/下移/置底按钮）
		$('#language-table').on('click', '.sort-lan-btn', function() {
			const btn = $(this);
			if (btn.prop('disabled')) return false;
			const id = btn.closest('[data-id]').data('id');
			const type = btn.data('type');
			btn.button('loading');
			post(_this.url, { opn: 'sortLanguage', id: id, type: type }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
		});

		// 添加货币
		$('#currency-info').on('click', '.btn.add', function() {
			const btn = $(this);
			const code = btn.closest('[data-code]').data('code');
			btn.button('loading');
			post(_this.url, { opn: 'addCurrency', code: code, site_id: _this.id }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
		});

		// 删除货币
		$('#currency-info, #site-currency').on('click', '.btn.delete', function() {
			const item = $(this).closest('[data-code]');
			const code = item.data('code');
			const id = item.data('id') || 0;
			confirm('确认要移除站点货币 [' + code + '] 吗?', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteCurrency', id: id, code: code, site_id: _this.id }, function(res) {
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

		// 货币排序（数值输入）
		$('#site-currency').on('change', '[name="sort"]', function() {
			const id = $(this).closest('[data-id]').data('id');
			const sort = $(this).val();
			post(_this.url, { opn: 'sortCurrency', id: id, sort: sort }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 货币排序（置顶/上移/下移/置底按钮）
		$('#site-currency').on('click', '.sort-cur-btn', function() {
			const btn = $(this);
			if (btn.prop('disabled')) return false;
			const id = btn.closest('[data-id]').data('id');
			const type = btn.data('type');
			btn.button('loading');
			post(_this.url, { opn: 'sortCurrency', id: id, type: type }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
		});

		// 默认关键字/描述失去焦点更新
		$('.text-content textarea').on('blur', function() {
			const value = $(this).val();
			const name = this.name;
			post(_this.url, { opn: 'updateKeyword', value: value, site_id: _this.id, name: name }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
			});
		});

		// 打开多语言文本弹窗
		$('.text-content').on('click', '.lang-config-btn', function() {
			const btn = $(this);
			const item = btn.closest('.item');
			const type = item.data('id');
			const srcText = item.next().find('textarea').val();
			$('#dealbox-language input[name="source_text"]').val(srcText);
			btn.button('loading');
			post(_this.url, { opn: 'getLanguage', site_id: _this.id, type: type }, function(res) {
				btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1) {
					const data = res.data || [];
					const obj = $('#dealbox-language');
					obj.find('input[name="type"]').val(type);
					let html = '';
					for (const i in data) {
						const row = data[i];
						const labelName = row.name2 || row.name || row.code;
						const trText = $('<div>').text(row.tr_text || '').html();
						html += '<div class="lang-field-card">' +
							'<label class="lang-field-label">' + labelName + ' <code>' + row.code + '</code></label>' +
							'<textarea name="language[' + row.code + ']" data-tr_code="' + (row.tr_code || row.code) + '" class="form-control" autocomplete="off" rows="2">' + trText + '</textarea>' +
							'</div>';
					}
					obj.find('#lang-fields-body').html(html);
					obj.find('.dealbox-title').text(type === 'keyword' ? '站点关键词多语言配置' : '站点描述多语言配置');
					obj.fadeIn(150);
				} else {
					showTips(res);
				}
			});
		});

		// 智能翻译空项
		$('#dealbox-language').on('click', '.auto-translate', function() {
			let name = $.trim($('#dealbox-language input[name="source_text"]').val());
			const textareas = $('#dealbox-language #lang-fields-body textarea');
			if (name === '') {
				textareas.each(function() {
					const v = $.trim($(this).val());
					if (v !== '') {
						name = v;
						return false;
					}
				});
			}
			if (name === '') {
				errorTips('请先在默认文本框或任一语言框中填写源文本');
				return false;
			}
			textareas.each(function() {
				const ta = $(this);
				if ($.trim(ta.val()) === '') {
					const trCode = ta.data('tr_code');
					post(_this.url, { opn: 'transfer', tr_code: trCode, name: name }, function(res) {
						if (typeof res === 'string') {
							try { res = JSON.parse(res); } catch (e) {}
						}
						if (res && res.code == 1) {
							ta.val(res.data);
						}
					});
				}
			});
		});

		// 保存多语言文本
		$('#dealbox-language .save-btn').on('click', function() {
			const btn = $(this);
			btn.button('loading');
			post(_this.url, $('#dealbox-language form').serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
			return false;
		});
	},

	openFieldModal: function(title, label, fieldName, value) {
		const modal = $('#field-info');
		modal.find('.dealbox-title').text(title);
		modal.find('#field-label').text(label);
		modal.find('#field-input').attr('name', fieldName).val(value || '');
		modal.fadeIn(150);
	}
};
