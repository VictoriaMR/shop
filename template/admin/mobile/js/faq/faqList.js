$(function() {
	FAQ_LIST_MOBILE.init();
});

const FAQ_LIST_MOBILE = {
	url: '',
	editor: null,
	init: function() {
		var $page = $('#faq-page');
		this.url = $page.data('url') || '/faq/faqList';
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

		// 下拉筛选自动提交
		$('.filter-select').on('change', function() {
			$(this).closest('form').submit();
		});

		// 新增文章
		$('.add-btn').on('click', function() {
			_this.openEditModal('新增文章', {
				faq_id: 0,
				group_id: 0,
				title: '',
				status: 1,
				visit_total: 0
			});
		});

		// 编辑文章
		$('#data-list').on('click', '.btn.modify', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $card = $btn.closest('.item');
			var id = $card.data('id');
			if (!id) return;

			$btn.button('loading');
			post(_this.url, { opn: 'getFaqInfo', faq_id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.openEditModal('编辑文章', res.data);
				} else {
					showTips(res || { code: 0, msg: '获取文章信息失败' });
				}
			});
		});

		// 保存文章
		$('#dealbox-info').on('click', '.save-btn', function() {
			var $btn = $(this);
			var $form = $('#faq-form');
			var title = $.trim($form.find('input[name="title"]').val());
			var groupId = parseInt($form.find('select[name="group_id"]').val() || 0, 10);
			if (!title) {
				errorTips('文章标题不能为空');
				return false;
			}
			if (groupId < 1) {
				errorTips('请选择所属分组');
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

		// 状态开关
		$('#data-list').on('click', '.switch_botton', function(e) {
			e.stopPropagation();
			var $sw = $(this);
			var $card = $sw.closest('.item');
			var id = $card.data('id');
			var status = parseInt($sw.data('status') || 0, 10) === 1 ? 0 : 1;

			post(_this.url, { opn: 'modifyFaqStatus', faq_id: id, status: status }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$sw.switchBtn(status);
					$card.find('.switch-txt').text(status === 1 ? '开启' : '关闭');
				}
			});
		});

		// 删除文章
		$('#data-list').on('click', '.btn.delete', function(e) {
			e.stopPropagation();
			var $card = $(this).closest('.item');
			var id = $card.data('id');
			var title = $.trim($card.find('.faq-title').text());

			confirm('确定要删除文章【' + title + '】吗？', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteFaq', faq_id: id }, function(res) {
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

		// 打开多语言列表弹窗
		$('#data-list').on('click', '.lang-btn', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $card = $btn.closest('.item');
			var id = $card.data('id');
			var title = $.trim($card.find('.faq-title').text());

			$btn.button('loading');
			post(_this.url, { opn: 'getFaqLanguage', faq_id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1) {
					var data = (res.data && res.data.length) ? res.data : (Array.isArray(res.msg) ? res.msg : []);
					var $modal = $('#dealbox-language');
					$modal.find('input[name="faq_id"]').val(id);
					$modal.find('input[name="name"]').val(title);
					$modal.find('.source-name-text').text(title + ' (#' + id + ')');

					var $list = $modal.find('.lang-status-list').empty();
					$.each(data, function(i, row) {
						var $badge = row.edit
							? $('<span class="lang-cfg-badge cfg-yes">已配置</span>')
							: $('<span class="lang-cfg-badge cfg-no">未配置</span>');
						var $editBtn = $('<button type="button" class="btn btn-primary btn-xs edit" title="配置语言内容" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>"><i class="fa fa-pen-to-square"></i></button>');
						$('<div class="lang-status-row">')
							.attr('data-id', row.lan_id)
							.append(
								$('<div class="lang-status-info">')
									.append($('<span class="lang-name">').text(row.language_name || row.lan_id))
									.append($badge)
							)
							.append($editBtn)
							.appendTo($list);
					});
					$modal.fadeIn(150);
				} else {
					showTips(res || { code: 0, msg: '获取多语言列表失败' });
				}
			});
		});

		// 点击某个语言的“配置”按钮
		$('#dealbox-language').on('click', '.btn.edit', function() {
			var $btn = $(this);
			var id = $('#dealbox-language input[name="faq_id"]').val();
			var lanId = $btn.closest('.lang-status-row').data('id');
			if (!id || !lanId) return;

			$btn.button('loading');
			post(_this.url, { opn: 'getFaqLanguage', faq_id: id, lan_id: lanId }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1) {
					var info = (res.data && res.data.faq_id) ? res.data : (res.msg || {});
					_this.openFaqLanguageEditor(info);
				} else {
					showTips(res || { code: 0, msg: '获取语言内容失败' });
				}
			});
		});

		// 保存单语言文章内容
		$('#dealbox-faq-language').on('click', '.save-btn', function() {
			var $btn = $(this);
			if (_this.editor && typeof _this.editor.sync === 'function') {
				_this.editor.sync();
			}
			var $form = $('#faq-lang-edit-form');
			var title = $.trim($form.find('input[name="title"]').val());
			var content = $.trim($form.find('textarea[name="content"]').val());
			if (!title) {
				errorTips('文章标题不能为空');
				return false;
			}
			if (!content) {
				errorTips('文章内容不能为空');
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
					var lanId = $form.find('input[name="lan_id"]').val();
					var $row = $('#dealbox-language .lang-status-row[data-id="' + lanId + '"]');
					$row.find('.lang-cfg-badge').removeClass('cfg-no').addClass('cfg-yes').text('已配置');
					$('#dealbox-faq-language').fadeOut(150);
				}
			});
		});

		// 初始化富文本编辑器（若 KindEditor 可用）
		if (typeof KindEditor !== 'undefined') {
			KindEditor.ready(function(K) {
				_this.editor = K.create('#faq_editor', {
					width: '100%',
					height: '260px',
					items: [
						'bold', 'italic', 'underline', 'forecolor', 'hilitecolor',
						'|', 'justifyleft', 'justifycenter', 'insertorderedlist', 'insertunorderedlist',
						'|', 'link', 'image', 'removeformat', 'source'
					],
					uploadJson: '/faq/faqList?opn=upload',
					allowFileManager: false
				});
			});
		}
	},

	openEditModal: function(title, data) {
		data = data || { faq_id: 0, group_id: 0, title: '', status: 1, visit_total: 0 };
		var $box = $('#dealbox-info');
		$box.find('.dealbox-title').text(title);
		$box.find('[name="faq_id"]').val(data.faq_id || 0);
		$box.find('[name="group_id"]').val(data.group_id || 0);
		$box.find('[name="title"]').val(data.title || '');
		$box.find('[name="status"]').val(typeof data.status !== 'undefined' ? data.status : 1);
		$box.find('[name="visit_total"]').val(data.visit_total || 0);
		$box.fadeIn(150);
	},

	openFaqLanguageEditor: function(data) {
		data = data || {};
		var $box = $('#dealbox-faq-language');
		$box.find('[name="faq_id"]').val(data.faq_id || 0);
		$box.find('[name="lan_id"]').val(data.lan_id || 0);
		$box.find('[name="title"]').val(data.title || '');
		$box.find('[name="content"]').val(data.content || '');
		if (this.editor && typeof this.editor.html === 'function') {
			this.editor.html(data.content || '');
		}
		$box.find('.dealbox-title').text('帮助文章编辑 (' + (data.language_name || '') + ')');
		$box.fadeIn(150);
	}
};
