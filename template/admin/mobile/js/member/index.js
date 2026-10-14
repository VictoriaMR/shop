$(function(){
	MEMBERLIST.init();
});
const MEMBERLIST = {
	init: function() {
		const _this = this;

		// 状态分组筛选按钮点击
		$('.btn-group button[data-id]').on('click', function(){
			const status = $(this).data('id');
			const form = $(this).closest('form');
			form.find('input[name="status"]').val(status);
			form.submit();
		});

		// 打开新增弹窗
		$('#add-data-btn').on('click', function(){
			_this.dealboxData({});
		});

		// 保存管理员按钮
		$('#dealbox .save').on('click', function(){
			const obj = $(this);
			const form = obj.closest('form');
			if (!form.formFilter()) {
				return false;
			}
			obj.button('loading');
			post('', form.serializeArray(), function(res){
				obj.button('reset');
				showTips(res);
				if (res && res.code) {
					window.location.reload();
				}
			});
		});

		// 弹窗内部状态切换开关
		$('#dealbox .switch_botton').on('click', function(){
			let status = $(this).data('status');
			status = status == 1 ? 0 : 1;
			$(this).switchBtn(status);
			$(this).next().val(status);
		});

		// 列表卡片内部修改状态开关
		$('#data-list .switch_botton').on('click', function(){
			const obj = $(this);
			const item = obj.closest('.member-card, tr');
			const currentStatus = obj.data('status') == 1 ? 1 : 0;
			const newStatus = currentStatus === 1 ? 0 : 1;
			post('', {opn: 'modify', mem_id: item.data('id'), status: newStatus}, function(res) {
				showTips(res);
				if (res && res.code) {
					obj.switchBtn(newStatus);
				}
			});
		});

		// 修改管理员
		$('#data-list .btn.modify').on('click', function(){
			const item = $(this).closest('.member-card, tr');
			const memId = item.data('id');
			let rowData = item.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch(e){}
			}
			if (rowData && (rowData.mem_id || rowData.id)) {
				_this.dealboxData(rowData);
			}
			post('', {opn: 'getInfo', mem_id: memId}, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(e){}
				}
				if (res && res.code) {
					const data = (res.data && (res.data.mem_id || res.data.nick_name || res.data.nickname))
						? res.data
						: (res.msg && (res.msg.mem_id || res.msg.nick_name) ? res.msg : {});
					_this.dealboxData(data);
				} else if (!rowData || !rowData.mem_id) {
					showTips(res || {code: 0, msg: '获取用户信息失败'});
				}
			});
		});

		// 删除管理员
		$('#data-list .btn.delete').on('click', function(){
			const item = $(this).closest('.member-card, tr');
			const memId = item.data('id');
			confirm('确认要删除该人员账号吗？删除后不可恢复', function(btn, modal){
				post('', {opn: 'delete', mem_id: memId}, function(res){
					modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch(e){}
					}
					showTips(res);
					if (res && res.code) {
						item.fadeOut(250, function(){
							item.remove();
						});
					}
				});
			});
		});
	},
	dealboxData: function(data) {
		const obj = $('#dealbox');
		obj.find('input:not(.no_replace)').val('');
		if (data && (data.mem_id || data.id)) {
			const memId = data.mem_id || data.id;
			obj.find('.dealbox-title').text('编辑管理员');
			obj.find('[name="mem_id"]').val(memId);

			let nameVal = '';
			if (data.first_name && data.last_name) {
				nameVal = (data.first_name === data.last_name) ? data.first_name : (data.first_name + ' ' + data.last_name);
			} else {
				nameVal = data.name || data.first_name || data.last_name || '';
			}
			obj.find('[name="name"]').val($.trim(nameVal));

			const nickVal = data.nickname || data.nick_name || '';
			obj.find('[name="nickname"]').val(nickVal);

			if (data.mobile) obj.find('[name="mobile"]').val(data.mobile);
			if (data.email) obj.find('[name="email"]').val(data.email);
		} else {
			obj.find('.dealbox-title').text('新增管理员');
			obj.find('[name="mem_id"]').val(0);
		}
		let status = 0;
		if (data && typeof data.status !== 'undefined') {
			status = parseInt(data.status, 10) || 0;
		}
		obj.find('.switch_botton').switchBtn(status);
		obj.find('[name="status"]').val(status);
		obj.modalShow();
	}
};
