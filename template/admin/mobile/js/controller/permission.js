$(function() {
	PERMISSION_MOBILE.init();
});

const PERMISSION_MOBILE = {
	url: '',
	currentType: 1,
	currentId: 0,
	isSuper: false,

	init: function() {
		var $page = $('#perm-page');
		this.url = $page.data('url') || '';
		var _this = this;

		// 切换“按部门 / 按个人”标签
		$('#target-type-tabs .target-type-btn').on('click', function() {
			var $btn = $(this);
			var type = parseInt($btn.data('type'), 10) || 1;
			_this.currentType = type;

			$('#target-type-tabs .target-type-btn').removeClass('active');
			$btn.addClass('active');

			$('.target-list-wrap').addClass('hidden');
			$('#target-list-' + type).removeClass('hidden');

			var $firstVisible = $('#target-list-' + type + ' .perm-target-item:visible').first();
			if ($firstVisible.length > 0) {
				$firstVisible.trigger('click');
			} else {
				_this.clearSelection();
			}
		});

		// 实时搜索过滤
		$('#target-search').on('input', function() {
			var kw = $.trim($(this).val()).toLowerCase();
			$('#target-list-' + _this.currentType + ' .perm-target-item').each(function() {
				var $item = $(this);
				var searchStr = String($item.data('search') || '');
				if (kw === '' || searchStr.indexOf(kw) !== -1) {
					$item.show();
				} else {
					$item.hide();
				}
			});
		});

		// 点击选择部门或人员
		$('.perm-target-item').on('click', function() {
			var $item = $(this);
			$('.perm-target-item').removeClass('active');
			$item.addClass('active');

			var type = parseInt($item.data('type'), 10) || 1;
			var id = parseInt($item.data('id'), 10) || 0;
			var name = $item.data('name') || '';
			var deptName = $item.data('dept-name') || '';
			var isSuper = parseInt($item.data('super'), 10) === 1;

			_this.currentType = type;
			_this.currentId = id;
			_this.isSuper = isSuper;

			$('#current-target-badge')
				.text(type === 1 ? '部门' : '个人')
				.attr('class', 'target-badge ' + (type === 1 ? 'badge-primary' : 'badge-success'));
			$('#current-target-name').text(name + ' (#' + id + ')');

			if (type === 2 && deptName !== '') {
				$('#current-target-dept-hint').html('所属部门：<strong>' + deptName + '</strong>');
			} else {
				$('#current-target-dept-hint').text('');
			}

			_this.loadTargetPerm(type, id);
		});

		// 分组折叠/展开
		$('#perm-matrix-table').on('click', '.group-header-row', function(e) {
			if ($(e.target).closest('label, input').length > 0) {
				return;
			}
			$(this).closest('.perm-group-card').toggleClass('collapsed');
		});

		// 勾选“整个控制器 (*)”
		$('#perm-matrix-table').on('change', '.chk-ctrl-all', function() {
			var $chk = $(this);
			var $row = $chk.closest('.ctrl-row');
			$row.find('.chk-method').prop('checked', $chk.prop('checked'));
			_this.syncGroupCheckbox($row.data('group'));
		});

		// 勾选单个 public 方法
		$('#perm-matrix-table').on('change', '.chk-method', function() {
			var $row = $(this).closest('.ctrl-row');
			var totalMethods = $row.find('.chk-method').length;
			var checkedMethods = $row.find('.chk-method:checked').length;
			if (checkedMethods < totalMethods) {
				$row.find('.chk-ctrl-all').prop('checked', false);
			}
			_this.syncGroupCheckbox($row.data('group'));
		});

		// 分组级“全选本组”
		$('#perm-matrix-table').on('change', '.chk-group-all', function() {
			var $chk = $(this);
			var groupId = $chk.closest('.group-header-row').data('group');
			var checked = $chk.prop('checked');
			var $rows = $('#perm-matrix-table .ctrl-row[data-group="' + groupId + '"]');
			$rows.find('.chk-ctrl-all, .chk-method').prop('checked', checked);
		});

		// 全选所有控制器
		$('#btn-check-all').on('click', function() {
			if (_this.isSuper || _this.currentId <= 0) return;
			$('#perm-matrix-table .chk-ctrl-all, #perm-matrix-table .chk-method, #perm-matrix-table .chk-group-all').prop('checked', true);
		});

		// 清空勾选
		$('#btn-uncheck-all').on('click', function() {
			if (_this.isSuper || _this.currentId <= 0) return;
			$('#perm-matrix-table .chk-ctrl-all, #perm-matrix-table .chk-method, #perm-matrix-table .chk-group-all').prop('checked', false);
		});

		// 保存权限配置
		$('#btn-save-perm').on('click', function() {
			_this.savePerm($(this));
		});

		// 初次加载默认选中第一项
		var $firstDept = $('#target-list-1 .perm-target-item').first();
		if ($firstDept.length > 0) {
			$firstDept.trigger('click');
		} else {
			$('#target-type-tabs .target-type-btn[data-type="2"]').trigger('click');
		}
	},

	clearSelection: function() {
		this.currentId = 0;
		this.isSuper = false;
		$('#current-target-badge').text('未选择').attr('class', 'target-badge');
		$('#current-target-name').text('请选择授权对象');
		$('#current-target-dept-hint').text('');
		this.resetMatrix();
	},

	resetMatrix: function() {
		$('#perm-matrix-table .chk-ctrl-all, #perm-matrix-table .chk-method, #perm-matrix-table .chk-group-all')
			.prop('checked', false)
			.prop('disabled', false);
		$('#perm-matrix-table .dept-inh-ctrl, #perm-matrix-table .dept-inh-method').addClass('hidden');
	},

	syncGroupCheckbox: function(groupId) {
		var $rows = $('#perm-matrix-table .ctrl-row[data-group="' + groupId + '"]');
		var total = $rows.find('.chk-ctrl-all').length;
		var checked = $rows.find('.chk-ctrl-all:checked').length;
		$('#perm-matrix-table .group-header-row[data-group="' + groupId + '"] .chk-group-all')
			.prop('checked', total > 0 && total === checked);
	},

	syncAllGroupCheckboxes: function() {
		var _this = this;
		$('#perm-matrix-table .group-header-row').each(function() {
			_this.syncGroupCheckbox($(this).data('group'));
		});
	},

	loadTargetPerm: function(targetType, targetId) {
		var _this = this;
		_this.resetMatrix();

		post(_this.url, {
			opn: 'getTargetPerm',
			target_type: targetType,
			target_id: targetId
		}, function(res) {
			if (typeof res === 'string') {
				try { res = JSON.parse(res); } catch (err) {}
			}
			if (!res || res.code != 1) {
				showTips(res || { code: 0, msg: '获取权限失败' });
				return;
			}
			var data = res.data || {};
			var isSuper = !!data.is_super;
			_this.isSuper = isSuper;

			if (isSuper) {
				$('#super-admin-notice').removeClass('hidden');
				$('#member-union-notice').addClass('hidden');
				$('#perm-toolbar-btns button').prop('disabled', true);
				$('#perm-matrix-table .chk-ctrl-all, #perm-matrix-table .chk-method, #perm-matrix-table .chk-group-all')
					.prop('checked', true)
					.prop('disabled', true);
				return;
			}

			$('#super-admin-notice').addClass('hidden');
			$('#perm-toolbar-btns button').prop('disabled', false);
			if (targetType === 2) {
				$('#member-union-notice').removeClass('hidden');
			} else {
				$('#member-union-notice').addClass('hidden');
			}

			// 1. 渲染自身权限 (own_perms)
			var ownPerms = data.own_perms || [];
			ownPerms.forEach(function(item) {
				var ctrl = item.controller;
				var act = item.action;
				var $row = $('#perm-matrix-table .ctrl-row[data-controller="' + ctrl + '"]');
				if ($row.length === 0) return;

				if (act === '*') {
					$row.find('.chk-ctrl-all').prop('checked', true);
					$row.find('.chk-method').prop('checked', true);
				} else {
					$row.find('.chk-method[value="' + act + '"]').prop('checked', true);
				}
			});

			// 2. 渲染个人所属部门已授权限标记 (dept_perms)
			if (targetType === 2 && Array.isArray(data.dept_perms)) {
				data.dept_perms.forEach(function(item) {
					var ctrl = item.controller;
					var act = item.action;
					var $row = $('#perm-matrix-table .ctrl-row[data-controller="' + ctrl + '"]');
					if ($row.length === 0) return;

					if (act === '*') {
						$row.find('.dept-inh-ctrl').removeClass('hidden');
						$row.find('.dept-inh-method').removeClass('hidden');
					} else {
						$row.find('.method-item[data-action="' + act + '"] .dept-inh-method').removeClass('hidden');
					}
				});
			}

			_this.syncAllGroupCheckboxes();
		});
	},

	savePerm: function($btn) {
		var _this = this;
		if (_this.currentId <= 0) {
			errorTips('请先选择要授权的部门或人员');
			return false;
		}
		if (_this.isSuper) {
			errorTips('超级管理员默认拥有全部权限，无需修改');
			return false;
		}

		var rules = [];
		$('#perm-matrix-table .ctrl-row').each(function() {
			var $row = $(this);
			var ctrl = $row.data('controller');
			if (!ctrl) return;

			if ($row.find('.chk-ctrl-all').prop('checked')) {
				rules.push({ controller: ctrl, action: '*' });
			} else {
				$row.find('.chk-method:checked').each(function() {
					rules.push({ controller: ctrl, action: $(this).val() });
				});
			}
		});

		$btn.button('loading');
		post(_this.url, {
			opn: 'saveTargetPerm',
			target_type: _this.currentType,
			target_id: _this.currentId,
			rules: JSON.stringify(rules)
		}, function(res) {
			$btn.button('reset');
			if (typeof res === 'string') {
				try { res = JSON.parse(res); } catch (err) {}
			}
			showTips(res);
			if (res && res.code == 1) {
				var count = (res.data && typeof res.data.count !== 'undefined') ? res.data.count : rules.length;
				var $activeItem = $('.perm-target-item[data-type="' + _this.currentType + '"][data-id="' + _this.currentId + '"]');
				$activeItem.find('.perm-cnt-badge').text(count);
			}
		});
	}
};
