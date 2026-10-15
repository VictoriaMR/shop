$(function(){
	DEPARTMENT.init();
});
const DEPARTMENT = {
	allCollapsed: false,
	init: function() {
		const _this = this;

		// 点击有下级的部门行折叠/展开所有子孙部门
		$('#data-list').on('click', 'tr.item', function(e){
			if ($(e.target).closest('button, input, a, .switch_botton').length > 0) {
				return;
			}
			const tr = $(this);
			const toggleIcon = tr.find('.toggle-icon');
			if (toggleIcon.length === 0) {
				return;
			}
			const currLev = parseInt(tr.data('lev') || 1);
			const isCollapsed = tr.hasClass('collapsed-node');
			let nextTr = tr.next('tr.item');

			if (isCollapsed) {
				tr.removeClass('collapsed-node');
				toggleIcon.css('transform', 'rotate(0deg)');
				while (nextTr.length > 0 && parseInt(nextTr.data('lev') || 1) > currLev) {
					nextTr.removeClass('collapsed-node').show();
					nextTr.find('.toggle-icon').css('transform', 'rotate(0deg)');
					nextTr = nextTr.next('tr.item');
				}
			} else {
				tr.addClass('collapsed-node');
				toggleIcon.css('transform', 'rotate(-90deg)');
				while (nextTr.length > 0 && parseInt(nextTr.data('lev') || 1) > currLev) {
					nextTr.hide();
					nextTr = nextTr.next('tr.item');
				}
			}
		});

		// 全部折叠/展开
		$('.toggle-all-btn').on('click', function(){
			_this.allCollapsed = !_this.allCollapsed;
			if (_this.allCollapsed) {
				$('#data-list tr.item').each(function(){
					const tr = $(this);
					const lev = parseInt(tr.data('lev') || 1);
					tr.find('.toggle-icon').css('transform', 'rotate(-90deg)');
					tr.addClass('collapsed-node');
					if (lev > 1) {
						tr.hide();
					}
				});
			} else {
				$('#data-list tr.item').removeClass('collapsed-node').show().find('.toggle-icon').css('transform', 'rotate(0deg)');
			}
		});

		// 新增顶级部门
		$('.add-top-btn').on('click', function(){
			_this.dealboxData({
				dept_id: 0,
				parent_id: 0,
				name: '',
				remark: '',
				status: 1,
				sort: 0
			});
		});

		// 新增子部门
		$('#data-list').on('click', '.btn.add-sub', function(){
			const tr = $(this).closest('tr.item');
			_this.dealboxData({
				dept_id: 0,
				parent_id: tr.data('id'),
				name: '',
				remark: '',
				status: 1,
				sort: 0
			});
		});

		// 编辑部门
		$('#data-list').on('click', '.btn.modify', function(){
			const tr = $(this).closest('tr.item');
			let rowData = tr.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch(e){}
			}
			_this.dealboxData(rowData);
		});

		// 列表状态开关
		$('#data-list').on('click', '.switch_botton', function(){
			const obj = $(this);
			const tr = obj.closest('tr.item');
			const currentStatus = obj.data('status') == 1 ? 1 : 0;
			const newStatus = currentStatus === 1 ? 0 : 1;
			post('', {opn: 'modifyDeptStatus', dept_id: tr.data('id'), status: newStatus}, function(res){
				showTips(res);
				if (res && res.code) {
					obj.switchBtn(newStatus);
				}
			});
		});

		// 弹窗内状态开关
		$('#dealbox .switch_botton').on('click', function(){
			let status = $(this).data('status') == 1 ? 0 : 1;
			$(this).switchBtn(status);
			$(this).next('input[name="status"]').val(status);
		});

		// 排序按钮组
		$('#data-list').on('click', '.sort-btn-action', function(e){
			e.stopPropagation();
			const btn = $(this);
			if (btn.is(':disabled') || btn.hasClass('disabled')) {
				return false;
			}
			const tr = btn.closest('tr.item');
			btn.button('loading');
			post('', {
				opn: 'sortDept',
				dept_id: tr.data('id'),
				parent_id: tr.data('pid'),
				type: btn.data('type')
			}, function(res){
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

		// 删除部门
		$('#data-list').on('click', '.btn.delete', function(){
			const tr = $(this).closest('tr.item');
			const deptId = tr.data('id');
			confirm('确定要删除该部门吗？', function(btn, modal){
				btn.button('loading');
				post('', {opn: 'deleteDept', dept_id: deptId}, function(res){
					modal.hide();
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
		});

		// 保存部门
		$('#dealbox .save-btn').on('click', function(){
			const btn = $(this);
			const form = $('#dealbox form');
			const name = $.trim(form.find('[name="name"]').val());
			if (name === '') {
				errorTips('部门名称不能为空');
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

		// 关闭弹窗
		$('#dealbox .close, #dealbox .mask').on('click', function(){
			$('#dealbox').dealboxHide();
		});
	},
	dealboxData: function(data) {
		const obj = $('#dealbox');
		const deptId = parseInt(data.dept_id || 0);
		const parentId = parseInt(data.parent_id || 0);
		const status = typeof data.status !== 'undefined' ? (parseInt(data.status) === 1 ? 1 : 0) : 1;

		obj.find('.dealbox-title').text(deptId > 0 ? '编辑部门' : (parentId > 0 ? '新增子部门' : '新增顶级部门'));
		obj.find('[name="dept_id"]').val(deptId);
		obj.find('[name="parent_id"] option').prop('disabled', false);
		if (deptId > 0) {
			obj.find('[name="parent_id"] option[value="' + deptId + '"]').prop('disabled', true);
		}
		obj.find('[name="parent_id"]').val(parentId);
		obj.find('[name="name"]').val(data.name || '');
		obj.find('[name="remark"]').val(data.remark || '');
		obj.find('[name="sort"]').val(typeof data.sort !== 'undefined' ? data.sort : 0);
		obj.find('.switch_botton').switchBtn(status);
		obj.find('[name="status"]').val(status);
		obj.dealboxShow();
	}
};
