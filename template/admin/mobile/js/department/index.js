$(function() {
	DEPARTMENT_MOBILE.init();
});

const DEPARTMENT_MOBILE = {
	url: '',
	allCollapsed: false,
	init: function() {
		var $page = $('#department-page');
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

		// 点击有下级的部门卡片折叠/展开所有子孙部门
		$('#data-list').on('click', '.item', function(e) {
			if ($(e.target).closest('button, input, a, .btn, .switch_botton').length > 0) {
				return;
			}
			var $card = $(this);
			if ($card.find('.toggle-icon').length === 0) {
				return;
			}
			var currLev = parseInt($card.data('lev') || 1, 10);
			var isCollapsed = $card.hasClass('collapsed-node');
			var $next = $card.next('.item');

			if (isCollapsed) {
				$card.removeClass('collapsed-node');
				while ($next.length > 0 && parseInt($next.data('lev') || 1, 10) > currLev) {
					$next.removeClass('collapsed-node').show();
					$next = $next.next('.item');
				}
			} else {
				$card.addClass('collapsed-node');
				while ($next.length > 0 && parseInt($next.data('lev') || 1, 10) > currLev) {
					$next.hide();
					$next = $next.next('.item');
				}
			}
		});

		// 全部折叠/展开
		$('.toggle-all-btn').on('click', function() {
			_this.allCollapsed = !_this.allCollapsed;
			if (_this.allCollapsed) {
				$('#data-list .item').each(function() {
					var $card = $(this);
					var lev = parseInt($card.data('lev') || 1, 10);
					$card.addClass('collapsed-node');
					if (lev > 1) {
						$card.hide();
					}
				});
				$(this).html('<i class="fa fa-expand"></i>');
			} else {
				$('#data-list .item').removeClass('collapsed-node').show();
				$(this).html('<i class="fa fa-compress"></i>');
			}
		});

		// 新增顶级部门
		$('.add-top-btn').on('click', function() {
			_this.openModal({
				dept_id: 0,
				parent_id: 0,
				name: '',
				remark: '',
				status: 1,
				sort: 0
			});
		});

		// 新增子部门
		$('#data-list').on('click', '.btn.add-sub', function(e) {
			e.stopPropagation();
			var $card = $(this).closest('.item');
			_this.openModal({
				dept_id: 0,
				parent_id: $card.data('id'),
				name: '',
				remark: '',
				status: 1,
				sort: 0
			});
		});

		// 编辑部门
		$('#data-list').on('click', '.btn.modify', function(e) {
			e.stopPropagation();
			var $card = $(this).closest('.item');
			var rowData = $card.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch (err) {}
			}
			_this.openModal(rowData);
		});

		// 列表状态开关
		$('#data-list').on('click', '.switch_botton', function(e) {
			e.stopPropagation();
			var $sw = $(this);
			var $card = $sw.closest('.item');
			var currentStatus = parseInt($sw.data('status') || 0, 10) === 1 ? 1 : 0;
			var newStatus = currentStatus === 1 ? 0 : 1;
			post(_this.url, { opn: 'modifyDeptStatus', dept_id: $card.data('id'), status: newStatus }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$sw.switchBtn(newStatus);
				}
			});
		});

		// 弹窗内状态开关
		$('#dealbox').on('click', '.switch_botton', function() {
			var status = parseInt($(this).data('status') || 0, 10) === 1 ? 0 : 1;
			$(this).switchBtn(status);
			$(this).next('input[name="status"]').val(status);
		});

		// 排序按钮组
		$('#data-list').on('click', '.sort-btn-action', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			if ($btn.is(':disabled') || $btn.hasClass('disabled')) {
				return false;
			}
			var $card = $btn.closest('.item');
			$btn.button('loading');
			post(_this.url, {
				opn: 'sortDept',
				dept_id: $card.data('id'),
				parent_id: $card.data('pid'),
				type: $btn.data('type')
			}, function(res) {
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

		// 删除部门
		$('#data-list').on('click', '.btn.delete', function(e) {
			e.stopPropagation();
			var $card = $(this).closest('.item');
			var deptId = $card.data('id');
			var deptName = $.trim($card.find('.dept-name').text());
			confirm('确定要删除部门【' + deptName + '】吗？', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteDept', dept_id: deptId }, function(res) {
					btn.button('reset');
					if (modal && modal.hide) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (err) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						setTimeout(function() {
							window.location.reload();
						}, 400);
					}
				});
			});
		});

		// 保存部门
		$('#dealbox').on('click', '.save-btn', function() {
			var $btn = $(this);
			var $form = $('#dept-form');
			var name = $.trim($form.find('[name="name"]').val());
			if (name === '') {
				errorTips('部门名称不能为空');
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
	},

	openModal: function(data) {
		var $box = $('#dealbox');
		var deptId = parseInt(data.dept_id || 0, 10);
		var parentId = parseInt(data.parent_id || 0, 10);
		var status = typeof data.status !== 'undefined' ? (parseInt(data.status, 10) === 1 ? 1 : 0) : 1;

		$box.find('.dealbox-title').text(deptId > 0 ? '编辑部门' : (parentId > 0 ? '新增子部门' : '新增顶级部门'));
		$box.find('[name="dept_id"]').val(deptId);
		$box.find('[name="parent_id"] option').prop('disabled', false);
		if (deptId > 0) {
			$box.find('[name="parent_id"] option[value="' + deptId + '"]').prop('disabled', true);
		}
		$box.find('[name="parent_id"]').val(parentId);
		$box.find('[name="name"]').val(data.name || '');
		$box.find('[name="remark"]').val(data.remark || '');
		$box.find('[name="sort"]').val(typeof data.sort !== 'undefined' ? data.sort : 0);
		$box.find('.switch_botton').switchBtn(status);
		$box.find('[name="status"]').val(status);
		$box.fadeIn(150);
	}
};
