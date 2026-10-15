$(function(){
	POSITION.init();
});
const POSITION = {
	init: function() {
		const _this = this;

		// 状态筛选按钮点击
		$('.btn-group button[data-id]').on('click', function(){
			const status = $(this).data('id');
			const form = $(this).closest('form');
			form.find('input[name="status"]').val(status);
			form.submit();
		});

		// 新增职位
		$('#add-pos-btn').on('click', function(){
			_this.dealboxData({
				pos_id: 0,
				name: '',
				remark: '',
				status: 1,
				sort: 0
			});
		});

		// 编辑职位
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
			post('', {opn: 'modifyPosStatus', pos_id: tr.data('id'), status: newStatus}, function(res){
				showTips(res);
				if (res && res.code) {
					obj.switchBtn(newStatus);
				}
			});
		});

		// 弹窗状态开关
		$('#dealbox .switch_botton').on('click', function(){
			let status = $(this).data('status') == 1 ? 0 : 1;
			$(this).switchBtn(status);
			$(this).next('input[name="status"]').val(status);
		});

		// 排序按钮组
		$('#data-list').on('click', '.sort-btn-action', function(){
			const btn = $(this);
			if (btn.is(':disabled') || btn.hasClass('disabled')) {
				return false;
			}
			const tr = btn.closest('tr.item');
			btn.button('loading');
			post('', {
				opn: 'sortPos',
				pos_id: tr.data('id'),
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

		// 删除职位
		$('#data-list').on('click', '.btn.delete', function(){
			const tr = $(this).closest('tr.item');
			const posId = tr.data('id');
			confirm('确定要删除该职位吗？', function(btn, modal){
				btn.button('loading');
				post('', {opn: 'deletePos', pos_id: posId}, function(res){
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

		// 保存职位
		$('#dealbox .save-btn').on('click', function(){
			const btn = $(this);
			const form = $('#dealbox form');
			const name = $.trim(form.find('[name="name"]').val());
			if (name === '') {
				errorTips('职位名称不能为空');
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
		const posId = parseInt(data.pos_id || 0);
		const status = typeof data.status !== 'undefined' ? (parseInt(data.status) === 1 ? 1 : 0) : 1;

		obj.find('.dealbox-title').text(posId > 0 ? '编辑职位' : '新增职位');
		obj.find('[name="pos_id"]').val(posId);
		obj.find('[name="name"]').val(data.name || '');
		obj.find('[name="remark"]').val(data.remark || '');
		obj.find('[name="sort"]').val(typeof data.sort !== 'undefined' ? data.sort : 0);
		obj.find('.switch_botton').switchBtn(status);
		obj.find('[name="status"]').val(status);
		obj.dealboxShow();
	}
};
