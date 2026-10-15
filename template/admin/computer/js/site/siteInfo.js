$(function(){
	SITEINFO.init();
});

const SITEINFO = {
	id: 0,
	init: function() {
		const _this = this;
		_this.id = $('#site-page').data('id');
		if (!_this.id) {
			return;
		}

		// 关闭弹窗
		$('.dealbox .close, .dealbox .mask').on('click', function(){
			$(this).closest('.dealbox').dealboxHide();
		});

		// 修改站点名称
		$('#site-name').on('click', function(){
			_this.openFieldModal('修改站点名称', '站点名称：', 'name', $.trim($(this).find('b').text()));
		});

		// 修改模板目录
		$('#site-path').on('click', function(){
			_this.openFieldModal('修改模板目录', '模板目录：', 'path', $.trim($(this).find('code').text()));
		});

		// 修改绑定域名
		$('#site-domain-edit').on('click', function(){
			let val = $.trim($(this).find('code').text());
			if (val === '--') val = '';
			_this.openFieldModal('修改绑定域名', '绑定域名：', 'domain', val);
		});

		// 修改联系邮箱
		$('#site-email-edit').on('click', function(){
			let val = $.trim($(this).find('span').text());
			if (val === '--') val = '';
			_this.openFieldModal('修改联系邮箱', '联系邮箱：', 'email', val);
		});

		// 保存基础字段修改
		$('#field-info .save-btn').on('click', function(){
			const btn = $(this);
			const form = btn.closest('form');
			btn.button('loading');
			post('', form.serializeArray(), function(res){
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
		});

		// 运行开关切换 (status / debug / view_cache / static_cache)
		$('#site-page .site-switch').on('click', function(){
			const sw = $(this);
			const field = sw.data('field');
			const next = parseInt(sw.attr('data-status')) === 1 ? 0 : 1;
			const param = {opn: 'modifySite', id: _this.id};
			param[field] = next;
			post('', param, function(res){
				showTips(res);
				if (res && res.code == 1) {
					sw.switchBtn(next);
				}
			});
		});

		// 打开管理语言弹窗
		$('#add-language').on('click', function(){
			$('#language-info').dealboxShow();
		});

		// 打开管理货币弹窗
		$('#add-currency').on('click', function(){
			$('#currency-info').dealboxShow();
		});

		// 添加语言
		$('#language-info .btn.add').on('click', function(){
			const btn = $(this);
			const code = btn.closest('tr').data('code');
			btn.button('loading');
			post('', {opn: 'addLanguage', code: code, site_id: _this.id}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
		});

		// 删除语言
		$('#language-info .btn.delete, #language-table .btn.delete').on('click', function(){
			const tr = $(this).closest('tr');
			const code = tr.data('code');
			const id = tr.data('id') || 0;
			confirm('确认要移除站点语言 [' + code + '] 吗?', function(btn, modal){
				btn.button('loading');
				post('', {opn: 'deleteLanguage', id: id, code: code, site_id: _this.id}, function(res){
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

		// 语言排序（数值输入）
		$('#language-table [name="sort"]').on('change', function(){
			const id = $(this).closest('tr').data('id');
			const sort = $(this).val();
			post('', {opn: 'sortLanguage', id: id, sort: sort}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 语言排序（置顶/上移/下移/置底按钮）
		$('#language-table .sort-lan-btn').on('click', function(){
			const btn = $(this);
			if (btn.prop('disabled')) return false;
			const id = btn.closest('tr').data('id');
			const type = btn.data('type');
			post('', {opn: 'sortLanguage', id: id, type: type}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 添加货币
		$('#currency-info .btn.add').on('click', function(){
			const btn = $(this);
			const code = btn.closest('tr').data('code');
			btn.button('loading');
			post('', {opn: 'addCurrency', code: code, site_id: _this.id}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					btn.button('reset');
				}
			});
		});

		// 删除货币
		$('#currency-info .btn.delete, #site-currency .btn.delete').on('click', function(){
			const tr = $(this).closest('tr');
			const code = tr.data('code');
			const id = tr.data('id') || 0;
			confirm('确认要移除站点货币 [' + code + '] 吗?', function(btn, modal){
				btn.button('loading');
				post('', {opn: 'deleteCurrency', id: id, code: code, site_id: _this.id}, function(res){
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

		// 货币排序（数值输入）
		$('#site-currency [name="sort"]').on('change', function(){
			const id = $(this).closest('tr').data('id');
			const sort = $(this).val();
			post('', {opn: 'sortCurrency', id: id, sort: sort}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 货币排序（置顶/上移/下移/置底按钮）
		$('#site-currency .sort-cur-btn').on('click', function(){
			const btn = $(this);
			if (btn.prop('disabled')) return false;
			const id = btn.closest('tr').data('id');
			const type = btn.data('type');
			post('', {opn: 'sortCurrency', id: id, type: type}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 默认关键字/描述失去焦点更新
		$('.text-content textarea').on('blur', function(){
			const value = $(this).val();
			const name = this.name;
			post('', {opn: 'updateKeyword', value: value, site_id: _this.id, name: name}, function(res){
				showTips(res);
			});
		});

		// 打开多语言文本弹窗
		$('.text-content .btn').on('click', function(){
			const btn = $(this);
			const type = btn.closest('.item').data('id');
			const srcText = btn.closest('.item').next().find('textarea').val();
			$('#dealbox-language input[name="source_text"]').val(srcText);
			post('', {opn: 'getLanguage', site_id: _this.id, type: type}, function(res){
				if (res && res.code == 1) {
					const data = res.data || [];
					const obj = $('#dealbox-language');
					obj.find('input[name="type"]').val(type);
					let html = '<tr>\
									<th width="100">语言名称</th>\
									<th>\
										<span>多语言文本</span>\
										<a href="javascript:;" class="right f12 auto-translate"><i class="glyphicon glyphicon-transfer"></i> 一键智能翻译空项</a>\
									</th>\
								</tr>';
					for (const i in data) {
						const row = data[i];
						html += '<tr>\
									<th><span>' + (row.name2 || row.name || row.code) + '</span></th>\
									<td class="p0">\
										<textarea name="language[' + row.code + ']" data-tr_code="' + (row.tr_code || row.code) + '" class="form-control" autocomplete="off" rows="3">' + (row.tr_text || '') + '</textarea>\
									</td>\
								</tr>';
					}
					obj.find('table tbody').html(html);
					obj.dealboxShow(type === 'keyword' ? '站点关键词多语言配置' : '站点描述多语言配置');
				} else {
					showTips(res);
				}
			});
		});

		// 智能翻译空项
		$('#dealbox-language').on('click', '.auto-translate', function(){
			let name = $.trim($('#dealbox-language input[name="source_text"]').val());
			const table = $(this).closest('table');
			const textareas = table.find('textarea');
			if (name === '') {
				textareas.each(function(){
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
			textareas.each(function(){
				const ta = $(this);
				if ($.trim(ta.val()) === '') {
					const trCode = ta.data('tr_code');
					post('', {opn: 'transfer', tr_code: trCode, name: name}, function(res){
						if (res && res.code == 1) {
							ta.val(res.data);
						}
					});
				}
			});
		});

		// 保存多语言文本
		$('#dealbox-language .save-btn').on('click', function(){
			const btn = $(this);
			btn.button('loading');
			post('', $('#dealbox-language form').serializeArray(), function(res){
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
		modal.find('#field-label').text(label);
		modal.find('#field-input').attr('name', fieldName).val(value || '');
		modal.dealboxShow(title);
	}
};