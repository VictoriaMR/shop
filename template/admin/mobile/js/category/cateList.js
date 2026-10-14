$(function() {
	CATEGORY_LIST.init();
});

const CATEGORY_LIST = {
	url: '',
	init: function() {
		var $page = $('#category-list');
		this.url = $page.data('url') || '/category/cateList';
		var _this = this;

		// 一级品类下拉切换
		$('.parent-cate-select').on('change', function() {
			var targetUrl = $(this).val();
			if (targetUrl) {
				window.location.href = targetUrl;
			}
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

		// 全部折叠 / 展开
		var allExpanded = true;
		$('.toggle-all-btn').on('click', function() {
			if (allExpanded) {
				$('#data-list .item').filter(function() {
					return parseInt($(this).data('lev'), 10) > 1;
				}).hide();
				$(this).html('<i class="fa fa-expand"></i>');
				allExpanded = false;
			} else {
				$('#data-list .item').show();
				$(this).html('<i class="fa fa-compress"></i>');
				allExpanded = true;
			}
		});

		// 点击卡片展开/收起下级
		$('#data-list').on('click', '.item', function(e) {
			if ($(e.target).closest('button, a, input, select, textarea, .btn, .switch_botton, .cate-icon-upload, .lang-trigger').length > 0) {
				return;
			}
			var $cur = $(this);
			var lev = parseInt($cur.data('lev'), 10);
			var $next = $cur.next('.item');
			if ($next.length && parseInt($next.data('lev'), 10) > lev) {
				var shouldHide = $next.is(':visible');
				$cur.nextAll('.item').each(function() {
					if (parseInt($(this).data('lev'), 10) > lev) {
						shouldHide ? $(this).hide() : $(this).show();
					} else {
						return false;
					}
				});
			}
		});

		// 顶部按钮：为当前一级品类新增子类目
		$('.add-top-sub-btn').on('click', function() {
			var cid = $page.data('cid');
			var parentName = $page.data('name') || '当前品类';
			_this.initData({ parent_id: cid, parent_name: parentName });
		});

		// 新增下级分类
		$('#data-list').on('click', '.btn.add', function(e) {
			e.stopPropagation();
			var $item = $(this).closest('.item');
			var id = $item.data('id');
			var parentName = $.trim($item.find('.cate_name').text());
			_this.initData({ parent_id: id, parent_name: parentName });
		});

		// 编辑子类目
		$('#data-list').on('click', '.btn.modify', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $item = $btn.closest('.item');
			var id = $item.data('id');
			if (!id) return;

			$btn.button('loading');
			post(_this.url, { opn: 'getCateInfo', cate_id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.initData(res.data);
				} else {
					showTips(res || { code: 0, msg: '获取分类信息失败' });
				}
			});
		});

		// 保存子类目
		$('#dealbox').on('click', '.save-btn', function() {
			var $form = $('#subcate-form');
			var name = $.trim($form.find('input[name="name"]').val());
			if (!name) {
				errorTips('名称不能为空');
				return false;
			}
			var $btn = $(this);
			$btn.button('loading');
			post(_this.url, $form.serializeArray(), function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$('#dealbox').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 500);
				}
			});
		});

		// 多语言 / SEO 配置弹窗
		$('#data-list').on('click', '.lang-trigger', function(e) {
			e.stopPropagation();
			var $trigger = $(this);
			var $item = $trigger.closest('.item');
			var id = $item.data('id');
			var name = $.trim($item.find('.cate_name').text());
			var type = parseInt($trigger.data('type') || 0, 10);
			var typeLabels = { 0: '名称多语言', 1: 'Keyword (SEO)', 2: 'Description (SEO)' };

			post(_this.url, { opn: 'getCateLanguage', cate_id: id, type: type }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1 && res.data) {
					var $modal = $('#dealbox-language');
					$modal.find('input[name="cate_id"]').val(id);
					$modal.find('input[name="type"]').val(type);
					$modal.find('input[name="cate_name"]').val(name);
					$modal.find('.dealbox-title').html('<i class="fa fa-globe"></i> ' + (typeLabels[type] || '多语言配置'));
					$modal.find('.source-name-text').text(name + ' (#' + id + ')');

					var $list = $modal.find('.language-list').empty();
					$.each(res.data, function(i, row) {
						var $control;
						if (type === 0) {
							$control = $('<input type="text" class="form-control transfer" autocomplete="off">')
								.attr('name', 'language[' + row.lan_id + ']')
								.attr('data-tr_code', row.tr_code || '')
								.attr('placeholder', '请输入 ' + (row.language_name || '') + ' 翻译')
								.val(row.name || '');
						} else {
							$control = $('<textarea rows="2" class="form-control transfer" autocomplete="off"></textarea>')
								.attr('name', 'language[' + row.lan_id + ']')
								.attr('data-tr_code', row.tr_code || '')
								.attr('placeholder', '请输入 ' + (row.language_name || '') + ' 内容')
								.val(row.name || '');
						}
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
			var name = $('#dealbox-language input[name="cate_name"]').val();
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
				post(_this.url, { opn: 'transfer', tr_code: $input.data('tr_code'), name: name }, function(res) {
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
			post(_this.url, $('#cate-lang-form').serializeArray(), function(res) {
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

		// 状态 / 展示开关
		$('#data-list').on('click', '.switch_botton', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $item = $btn.closest('.item');
			var type = $btn.data('type');
			var newStatus = parseInt($btn.data('status') || 0, 10) === 1 ? 0 : 1;
			var param = {
				opn: 'modifyCategory',
				id: $item.data('id')
			};
			param[type] = newStatus;

			post(_this.url, param, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$btn.switchBtn(newStatus);
					if (newStatus === 0) {
						var lev = parseInt($item.data('lev'), 10);
						$item.nextAll('.item').each(function() {
							if (parseInt($(this).data('lev'), 10) > lev) {
								$(this).find('.switch_botton[data-type="' + type + '"]').switchBtn(0);
							} else {
								return false;
							}
						});
					}
				}
			});
		});

		// 排序按钮
		$('#data-list').on('click', '.sort-btn-action', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			if ($btn.is(':disabled') || $btn.hasClass('disabled')) return false;
			var $item = $btn.closest('.item');
			var id = $item.data('id');
			var pid = $item.data('pid');
			var type = $btn.data('type');

			$btn.button('loading');
			post(_this.url, { opn: 'sortCategory', cate_id: id, parent_id: pid, type: type }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					$btn.button('reset');
				}
			});
		});

		// 图标上传
		if ($.fn.imageUpload) {
			$('#data-list .cate-icon-upload').imageUpload('category', function(data, $trigger) {
				var id = $trigger.closest('.item').data('id');
				post(_this.url, { opn: 'modifyCategory', id: id, attach_id: data.attach_id }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (err) {}
					}
					showTips(res);
				});
			});
		}

		// 删除子类目
		$('#data-list').on('click', '.btn.delete', function(e) {
			e.stopPropagation();
			var $item = $(this).closest('.item');
			var id = $item.data('id');
			var name = $.trim($item.find('.cate_name').text());

			confirm('确定要删除子类目【' + name + '】吗？', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteCategory', cate_id: id }, function(res) {
					btn.button('reset');
					if (modal && modal.hide) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (err) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					}
				});
			});
		});
	},

	initData: function(data) {
		data = data || {};
		var $box = $('#dealbox');
		$box.find('input[name="cate_id"]').val(data.cate_id || 0);
		$box.find('input[name="parent_id"]').val(data.parent_id || 0);
		$box.find('input[name="name"]').val(data.name || '');
		$box.find('input[name="name_en"]').val(data.name_en || '');

		if (data.parent_id > 0 && data.parent_name) {
			$box.find('input[name="parent_name"]').val(data.parent_name);
			$box.find('.parent-group').show();
		} else {
			$box.find('input[name="parent_name"]').val('顶级分类');
			$box.find('.parent-group').hide();
		}

		$box.find('.dealbox-title').text(data.cate_id > 0 ? '编辑子类目' : '新增子类目');
		$box.fadeIn(150);
	}
};
