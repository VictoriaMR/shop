$(function(){
	PAYMENTUSED.init();
});

const PAYMENTUSED = {
	init: function() {
		const _this = this;

		// 新增使用配置
		$('#add-data-btn').on('click', function(){
			_this.showEditModal();
		});

		// 编辑使用配置
		$('#data-list .modify').on('click', function(){
			const btn = $(this);
			btn.button('loading');
			const tr = btn.closest('tr');
			const id = tr.data('id');

			post('', {opn: 'getUsedInfo', id: id}, function(res){
				btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(e){}
				}
				if (res && (res.code == 1 || res.code == 200)) {
					const data = res.data || res.msg || {};
					_this.showEditModal(data);
				} else {
					showTips(res || {code: 0, msg: '获取配置信息失败'});
				}
			});
		});

		// 保存按钮
		$('#dealbox .save').on('click', function(){
			let check = true;
			$('#dealbox form [required="required"]').each(function(){
				const val = $.trim($(this).val());
				if (!val) {
					check = false;
					const label = $(this).closest('.input-group').find('.input-group-addon span').last().text() || '配置项';
					errorTips(label + ' 为必选项');
					$(this).focus();
					return false;
				}
			});

			if (!check) {
				return false;
			}

			const btn = $(this);
			btn.button('loading');
			post('', $('#dealbox form').serializeArray(), function(res) {
				btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(e){}
				}
				showTips(res);
				if (res && (res.code == 1 || res.code == 200)) {
					$('#dealbox').dealboxHide();
					setTimeout(function(){
						window.location.reload();
					}, 500);
				}
			});
		});

		// 删除配置
		$('#data-list .delete').on('click', function(){
			const tr = $(this).closest('tr');
			const id = tr.data('id');

			confirm('确定要删除该支付账号使用配置吗？', function(btn, modal){
				post('', {opn: 'deleteUsedInfo', id: id}, function(res){
					if (modal && modal.hide) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch(e){}
					}
					showTips(res);
					if (res && (res.code == 1 || res.code == 200)) {
						tr.fadeOut(300, function(){
							tr.remove();
							if ($('#data-list tbody tr').length === 0) {
								window.location.reload();
							}
						});
					}
				});
			});
		});

		// 弹窗关闭
		$('#dealbox').on('click', '.close, .mask', function(){
			$('#dealbox').dealboxHide();
		});
	},

	showEditModal: function(data) {
		const form = $('#dealbox form')[0];
		if (form) form.reset();

		if (!data) {
			data = {
				item_id: 0,
				site_id: '',
				type: '',
				payment_id: '',
			};
		}

		const obj = $('#dealbox');
		for (const i in data) {
			obj.find('[name="' + i + '"]').val(data[i]);
		}
		obj.find('.dealbox-title').text(data.item_id ? '编辑账户使用' : '新增账户使用');
		obj.dealboxShow();
	}
};