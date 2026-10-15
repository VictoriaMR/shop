$(function(){
	PERMISSION_MANAGER.init();
});

const PERMISSION_MANAGER = {
	currentType: 1,
	currentId: 0,
	isSuper: false,

	init: function() {
		const _this = this;

		// 切换“按部门 / 按个人”标签
		$('#target-type-tabs .target-type-btn').on('click', function(){
			const btn = $(this);
			const type = parseInt(btn.data('type')) || 1;
			_this.currentType = type;

			$('#target-type-tabs .target-type-btn').removeClass('btn-primary').addClass('btn-default');
			btn.removeClass('btn-default').addClass('btn-primary');

			$('.target-list-wrap').addClass('hidden');
			$('#target-list-' + type).removeClass('hidden');

			const firstVisible = $('#target-list-' + type + ' .perm-target-item:visible').first();
			if (firstVisible.length > 0) {
				firstVisible.trigger('click');
			} else {
				_this.clearSelection();
			}
		});

		// 左侧实时搜索过滤
		$('#target-search').on('input', function(){
			const kw = $.trim($(this).val()).toLowerCase();
			$('#target-list-' + _this.currentType + ' .perm-target-item').each(function(){
				const item = $(this);
				const searchStr = String(item.data('search') || '');
				if (kw === '' || searchStr.indexOf(kw) !== -1) {
					item.show();
				} else {
					item.hide();
				}
			});
		});

		// 点击左侧部门或人员项
		$('.perm-target-item').on('click', function(){
			const item = $(this);
			$('.perm-target-item').removeClass('active');
			item.addClass('active');

			const type = parseInt(item.data('type')) || 1;
			const id = parseInt(item.data('id')) || 0;
			const name = item.data('name') || '';
			const deptName = item.data('dept-name') || '';
			const isSuper = parseInt(item.data('super')) === 1;

			_this.currentType = type;
			_this.currentId = id;
			_this.isSuper = isSuper;

			$('#current-target-badge')
				.text(type === 1 ? '部门' : '个人')
				.attr('class', 'label ' + (type === 1 ? 'label-primary' : 'label-success'));
			$('#current-target-name').text(name + ' (#' + id + ')');

			if (type === 2 && deptName !== '') {
				$('#current-target-dept-hint').html('所属部门：<strong>' + deptName + '</strong>');
			} else {
				$('#current-target-dept-hint').text('');
			}

			_this.loadTargetPerm(type, id);
		});

		// 勾选“整个控制器 (*)”
		$('#perm-matrix-table').on('change', '.chk-ctrl-all', function(){
			const chk = $(this);
			const row = chk.closest('tr.ctrl-row');
			row.find('.chk-method').prop('checked', chk.prop('checked'));
			_this.syncGroupCheckbox(row.data('group'));
		});

		// 勾选单个 public 方法
		$('#perm-matrix-table').on('change', '.chk-method', function(){
			const row = $(this).closest('tr.ctrl-row');
			const totalMethods = row.find('.chk-method').length;
			const checkedMethods = row.find('.chk-method:checked').length;
			if (checkedMethods < totalMethods) {
				row.find('.chk-ctrl-all').prop('checked', false);
			}
			_this.syncGroupCheckbox(row.data('group'));
		});

		// 分组级“全选本组”
		$('#perm-matrix-table').on('change', '.chk-group-all', function(){
			const chk = $(this);
			const groupId = chk.closest('tr.group-header-row').data('group');
			const checked = chk.prop('checked');
			const rows = $('#perm-matrix-table tr.ctrl-row[data-group="' + groupId + '"]');
			rows.find('.chk-ctrl-all, .chk-method').prop('checked', checked);
		});

		// 全选所有控制器
		$('#btn-check-all').on('click', function(){
			if (_this.isSuper || _this.currentId <= 0) return;
			$('#perm-matrix-table .chk-ctrl-all, #perm-matrix-table .chk-method, #perm-matrix-table .chk-group-all').prop('checked', true);
		});

		// 清空勾选
		$('#btn-uncheck-all').on('click', function(){
			if (_this.isSuper || _this.currentId <= 0) return;
			$('#perm-matrix-table .chk-ctrl-all, #perm-matrix-table .chk-method, #perm-matrix-table .chk-group-all').prop('checked', false);
		});

		// 保存权限配置
		$('#btn-save-perm').on('click', function(){
			_this.savePerm($(this));
		});

		// 页面初次加载默认选中第一个部门（若无部门则切换到个人）
		const firstDept = $('#target-list-1 .perm-target-item').first();
		if (firstDept.length > 0) {
			firstDept.trigger('click');
		} else {
			$('#target-type-tabs .target-type-btn[data-type="2"]').trigger('click');
		}
	},

	clearSelection: function() {
		this.currentId = 0;
		this.isSuper = false;
		$('#current-target-badge').text('未选择').attr('class', 'label label-default');
		$('#current-target-name').text('请在左侧选择授权对象');
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
		const rows = $('#perm-matrix-table tr.ctrl-row[data-group="' + groupId + '"]');
		const total = rows.find('.chk-ctrl-all').length;
		const checked = rows.find('.chk-ctrl-all:checked').length;
		$('#perm-matrix-table tr.group-header-row[data-group="' + groupId + '"] .chk-group-all')
			.prop('checked', total > 0 && total === checked);
	},

	syncAllGroupCheckboxes: function() {
		const _this = this;
		$('#perm-matrix-table tr.group-header-row').each(function(){
			_this.syncGroupCheckbox($(this).data('group'));
		});
	},

	loadTargetPerm: function(targetType, targetId) {
		const _this = this;
		_this.resetMatrix();

		post('', {
			opn: 'getTargetPerm',
			target_type: targetType,
			target_id: targetId
		}, function(res){
			if (!res || !res.code) {
				showTips(res);
				return;
			}
			const data = res.data || {};
			const isSuper = !!data.is_super;
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

			// 1. 渲染该对象自身已配置的权限 (own_perms)
			const ownPerms = data.own_perms || [];
			ownPerms.forEach(function(item){
				const ctrl = item.controller;
				const act = item.action;
				const row = $('#perm-matrix-table tr.ctrl-row[data-controller="' + ctrl + '"]');
				if (row.length === 0) return;

				if (act === '*') {
					row.find('.chk-ctrl-all').prop('checked', true);
					row.find('.chk-method').prop('checked', true);
				} else {
					row.find('.chk-method[value="' + act + '"]').prop('checked', true);
				}
			});

			// 2. 如果是个人授权，展示所属部门已授予的权限标记 (dept_perms)
			if (targetType === 2 && Array.isArray(data.dept_perms)) {
				data.dept_perms.forEach(function(item){
					const ctrl = item.controller;
					const act = item.action;
					const row = $('#perm-matrix-table tr.ctrl-row[data-controller="' + ctrl + '"]');
					if (row.length === 0) return;

					if (act === '*') {
						row.find('.dept-inh-ctrl').removeClass('hidden');
						row.find('.dept-inh-method').removeClass('hidden');
					} else {
						row.find('.method-item[data-action="' + act + '"] .dept-inh-method').removeClass('hidden');
					}
				});
			}

			_this.syncAllGroupCheckboxes();
		});
	},

	savePerm: function(btn) {
		const _this = this;
		if (_this.currentId <= 0) {
			errorTips('请先在左侧选择要授权的部门或人员');
			return false;
		}
		if (_this.isSuper) {
			errorTips('超级管理员默认拥有全部权限，无需修改');
			return false;
		}

		const rules = [];
		$('#perm-matrix-table tr.ctrl-row').each(function(){
			const row = $(this);
			const ctrl = row.data('controller');
			if (!ctrl) return;

			if (row.find('.chk-ctrl-all').prop('checked')) {
				rules.push({controller: ctrl, action: '*'});
			} else {
				row.find('.chk-method:checked').each(function(){
					rules.push({controller: ctrl, action: $(this).val()});
				});
			}
		});

		btn.button('loading');
		post('', {
			opn: 'saveTargetPerm',
			target_type: _this.currentType,
			target_id: _this.currentId,
			rules: JSON.stringify(rules)
		}, function(res){
			btn.button('reset');
			showTips(res);
			if (res && res.code) {
				const count = (res.data && typeof res.data.count !== 'undefined') ? res.data.count : rules.length;
				const activeItem = $('.perm-target-item[data-type="' + _this.currentType + '"][data-id="' + _this.currentId + '"]');
				activeItem.find('.perm-cnt-badge').text(count);
			}
		});
	}
};
