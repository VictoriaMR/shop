$(function(){
	CONTROLLERLIST.init();
});
const CONTROLLERLIST = {
	allCollapsed: false,
	init: function() {
		const _this = this;

		// 点击一级分组行折叠/展开对应的子功能
		$('#data-list').on('click', 'tr.parent-row', function(e){
			if ($(e.target).closest('button, input, a').length > 0) {
				return;
			}
			const tr = $(this);
			const id = tr.data('id');
			const children = $('#data-list tr.child-row[data-pid="' + id + '"]');
			const isHidden = tr.hasClass('collapsed-group');
			if (isHidden) {
				tr.removeClass('collapsed-group');
				tr.find('.toggle-icon').css('transform', 'rotate(0deg)');
				children.show();
			} else {
				tr.addClass('collapsed-group');
				tr.find('.toggle-icon').css('transform', 'rotate(-90deg)');
				children.hide();
			}
		});

		// 全部折叠/展开
		$('.toggle-all-btn').on('click', function(){
			_this.allCollapsed = !_this.allCollapsed;
			if (_this.allCollapsed) {
				$('#data-list tr.parent-row').addClass('collapsed-group').find('.toggle-icon').css('transform', 'rotate(-90deg)');
				$('#data-list tr.child-row').hide();
			} else {
				$('#data-list tr.parent-row').removeClass('collapsed-group').find('.toggle-icon').css('transform', 'rotate(0deg)');
				$('#data-list tr.child-row').show();
			}
		});

		// 新增一级分组
		$('.add-btn').on('click', function(){
			_this.dealboxData({
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
		$('#data-list .btn.add').on('click', function(){
			const tr = $(this).closest('tr');
			let rowData = tr.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch(e){}
			}
			_this.dealboxData({
				con_id: 0,
				parent_id: tr.data('id'),
				parent_name: rowData.name || tr.find('.cate_name').text(),
				name: '',
				value: '',
				icon: 'fa-bookmark',
				sort: 0
			});
		});

		// 编辑功能
		$('#data-list .btn.modify').on('click', function(){
			const tr = $(this).closest('tr');
			let rowData = tr.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch(e){}
			}
			if (rowData && rowData.con_id) {
				_this.dealboxData(rowData);
				return;
			}
			const btn = $(this);
			btn.button('loading');
			post('', {opn: 'getInfo', id: tr.data('id')}, function(res){
				btn.button('reset');
				if (res && res.code) {
					_this.dealboxData(res.data || res.msg || {});
				} else {
					showTips(res);
				}
			});
		});

		// 图标下拉切换预览
		$('#dealbox select[name="icon"]').on('change', function(){
			const val = $(this).val() || 'fa-cube';
			$('#icon-preview').attr('class', 'fa ' + val);
		});

		// 关闭弹窗
		$('#dealbox .close, #dealbox .mask').on('click', function(){
			$('#dealbox').dealboxHide();
		});

		// 删除功能
		$('#data-list .btn.delete').on('click', function(){
			const tr = $(this).closest('tr');
			const id = tr.data('id');
			confirm('确认要删除该功能模块吗？', function(btn, modal){
				btn.button('loading');
				post('', {opn: 'deleteInfo', id: id}, function(res){
					modal.hide();
					showTips(res);
					if (res && res.code) {
						tr.fadeOut(250, function(){
							tr.remove();
						});
					} else {
						btn.button('reset');
					}
				});
			});
		});

		// 保存功能
		$('#dealbox .save-btn').on('click', function(){
			const btn = $(this);
			const form = $('#dealbox form');
			const name = $.trim(form.find('[name="name"]').val());
			const value = $.trim(form.find('[name="value"]').val());
			if (name === '') {
				errorTips('功能名称不能为空');
				return false;
			}
			if (value === '') {
				errorTips('控制器路由不能为空');
				return false;
			}
			btn.button('loading');
			post('', form.serializeArray(), function(res){
				showTips(res);
				if (res && res.code) {
					$('#dealbox').dealboxHide();
					setTimeout(function(){
						window.location.reload();
					}, 800);
				} else {
					btn.button('reset');
				}
			});
		});

		// 单条记录 排序按钮组（置顶、上移、下移、置底）
		$('#data-list').on('click', '.sort-btn-action', function(e){
			e.stopPropagation();
			const btn = $(this);
			if (btn.is(':disabled') || btn.hasClass('disabled')) {
				return false;
			}
			const tr = btn.closest('tr.item');
			const id = tr.data('id');
			const pid = tr.data('pid');
			const type = btn.data('type');

			btn.button('loading');
			post('', {opn: 'sortInfo', id: id, parent_id: pid, type: type}, function(res){
				showTips(res);
				if (res && res.code) {
					setTimeout(function(){
						window.location.reload();
					}, 600);
				} else {
					btn.button('reset');
				}
			});
		});
	},
	dealboxData: function(data) {
		const obj = $('#dealbox');
		const conId = parseInt(data.con_id || 0);
		const parentId = parseInt(data.parent_id || 0);
		const iconVal = data.icon || (parentId > 0 ? 'fa-bookmark' : 'fa-cube');

		if (conId > 0) {
			obj.find('.dealbox-title').text(parentId > 0 ? '编辑子功能' : '编辑一级分组');
		} else {
			obj.find('.dealbox-title').text(parentId > 0 ? '新增子功能' : '新增一级分组');
		}

		if (parentId > 0) {
			obj.find('.parent-group').show();
			obj.find('[name="parent_name"]').val(data.parent_name || ('ID: ' + parentId));
		} else {
			obj.find('.parent-group').hide();
			obj.find('[name="parent_name"]').val('');
		}

		obj.find('[name="con_id"]').val(conId);
		obj.find('[name="parent_id"]').val(parentId);
		obj.find('[name="name"]').val(data.name || '');
		obj.find('[name="value"]').val(data.value || '');
		obj.find('[name="icon"]').val(iconVal);
		if (!obj.find('[name="icon"]').val()) {
			obj.find('[name="icon"]').val(parentId > 0 ? 'fa-bookmark' : 'fa-cube');
		}
		$('#icon-preview').attr('class', 'fa ' + (obj.find('[name="icon"]').val() || 'fa-cube'));
		obj.find('[name="sort"]').val(typeof data.sort !== 'undefined' ? data.sort : 0);
		obj.dealboxShow();
	}
};