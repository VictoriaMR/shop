$(function() {
	CONTROLLER_INDEX.init();
});

const CONTROLLER_INDEX = {
	url: '',
	allCollapsed: false,
	init: function() {
		var $page = $('#function-list');
		this.url = $page.data('url') || '';
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

		// 点击一级分组卡片折叠/展开对应子功能
		$('#data-list').on('click', '.parent-card', function(e) {
			if ($(e.target).closest('button, input, a, .btn').length > 0) {
				return;
			}
			var $card = $(this);
			var id = $card.data('id');
			var $children = $('#data-list .child-card[data-pid="' + id + '"]');
			if ($card.hasClass('collapsed-group')) {
				$card.removeClass('collapsed-group');
				$children.show();
			} else {
				$card.addClass('collapsed-group');
				$children.hide();
			}
		});

		// 全部折叠/展开
		$('.toggle-all-btn').on('click', function() {
			_this.allCollapsed = !_this.allCollapsed;
			if (_this.allCollapsed) {
				$('#data-list .parent-card').addClass('collapsed-group');
				$('#data-list .child-card').hide();
				$(this).html('<i class="fa fa-expand"></i>');
			} else {
				$('#data-list .parent-card').removeClass('collapsed-group');
				$('#data-list .child-card').show();
				$(this).html('<i class="fa fa-compress"></i>');
			}
		});

		// 新增一级分组
		$('.add-btn').on('click', function() {
			_this.openModal({
				con_id: 0,
				parent_id: 0,
				parent_name: '',
				name: '',
				value: '',
				icon: 'fa-cube',
				sort: 0
			});
		});

		// 新增子功能
		$('#data-list').on('click', '.btn.add', function(e) {
			e.stopPropagation();
			var $card = $(this).closest('.item');
			var rowData = $card.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch (err) {}
			}
			_this.openModal({
				con_id: 0,
				parent_id: $card.data('id'),
				parent_name: rowData.name || $.trim($card.find('.cate_name').text()),
				name: '',
				value: '',
				icon: 'fa-bookmark',
				sort: 0
			});
		});

		// 修改功能
		$('#data-list').on('click', '.btn.modify', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $card = $btn.closest('.item');
			var rowData = $card.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch (err) {}
			}
			if (rowData && rowData.con_id) {
				_this.openModal(rowData);
				return;
			}
			var id = $card.data('id');
			if (!id) return;
			$btn.button('loading');
			post(_this.url, { opn: 'getInfo', id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1) {
					_this.openModal(res.data || res.msg || {});
				} else {
					showTips(res || { code: 0, msg: '获取功能信息失败' });
				}
			});
		});

		// 图标下拉切换预览
		$('#dealbox select[name="icon"]').on('change', function() {
			var val = $(this).val() || 'fa-cube';
			$('#icon-preview').attr('class', 'fa ' + val);
		});

		// 删除功能
		$('#data-list').on('click', '.btn.delete', function(e) {
			e.stopPropagation();
			var $card = $(this).closest('.item');
			var id = $card.data('id');
			var name = $.trim($card.find('.cate_name').text());

			confirm('确认要删除功能【' + name + '】吗？', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteInfo', id: id }, function(res) {
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

		// 保存功能
		$('#dealbox').on('click', '.save-btn', function() {
			var $btn = $(this);
			var $form = $('#func-form');
			var name = $.trim($form.find('[name="name"]').val());
			var value = $.trim($form.find('[name="value"]').val());
			if (name === '') {
				errorTips('功能名称不能为空');
				return false;
			}
			if (value === '') {
				errorTips('控制器路由不能为空');
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
					$('#dealbox').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 500);
				}
			});
		});

		// 排序按钮
		$('#data-list').on('click', '.sort-btn-action', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			if ($btn.is(':disabled') || $btn.hasClass('disabled')) {
				return false;
			}
			var $card = $btn.closest('.item');
			var id = $card.data('id');
			var pid = $card.data('pid');
			var type = $btn.data('type');

			$btn.button('loading');
			post(_this.url, { opn: 'sortInfo', id: id, parent_id: pid, type: type }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					setTimeout(function() {
						window.location.reload();
					}, 400);
				} else {
					$btn.button('reset');
				}
			});
		});
	},

	openModal: function(data) {
		var $box = $('#dealbox');
		var conId = parseInt(data.con_id || 0, 10);
		var parentId = parseInt(data.parent_id || 0, 10);
		var iconVal = data.icon || (parentId > 0 ? 'fa-bookmark' : 'fa-cube');

		if (conId > 0) {
			$box.find('.dealbox-title').text(parentId > 0 ? '编辑子功能' : '编辑一级分组');
		} else {
			$box.find('.dealbox-title').text(parentId > 0 ? '新增子功能' : '新增一级分组');
		}

		if (parentId > 0) {
			$box.find('.parent-group').show();
			$box.find('[name="parent_name"]').val(data.parent_name || ('ID: ' + parentId));
		} else {
			$box.find('.parent-group').hide();
			$box.find('[name="parent_name"]').val('');
		}

		$box.find('[name="con_id"]').val(conId);
		$box.find('[name="parent_id"]').val(parentId);
		$box.find('[name="name"]').val(data.name || '');
		$box.find('[name="value"]').val(data.value || '');
		$box.find('[name="icon"]').val(iconVal);
		if (!$box.find('[name="icon"]').val()) {
			$box.find('[name="icon"]').val(parentId > 0 ? 'fa-bookmark' : 'fa-cube');
		}
		$('#icon-preview').attr('class', 'fa ' + ($box.find('[name="icon"]').val() || 'fa-cube'));
		$box.find('[name="sort"]').val(typeof data.sort !== 'undefined' ? data.sort : 0);
		$box.fadeIn(150);
	}
};
