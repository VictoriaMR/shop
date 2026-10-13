$(function(){
	PAYMENT.init();
});

const PAYMENT = {
	init: function(){
		const _this = this;

		// 打开新增账号弹窗
		$('#add-data-btn').on('click', function(){
			_this.showEditModal();
		});

		// 查看配置秘钥信息
		$('#data-list .view-keys').on('click', function(){
			const btn = $(this);
			const tr = btn.closest('tr');
			const id = tr.data('id');
			btn.button('loading');
			post('', {opn: 'getInfo', id: id}, function(res){
				btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(e){}
				}
				if (res && (res.code == 1 || res.code == 200)) {
					const data = res.data || res.msg || {};
					_this.showViewModal(data);
				} else {
					showTips(res || {code: 0, msg: '获取配置信息失败'});
				}
			});
		});

		// 复制秘钥功能
		$('#dealbox-view .copy-btn').on('click', function(){
			const target = $(this).data('target');
			const val = $('#dealbox-view textarea[name="' + target + '"]').val();
			if (!val) {
				errorTips('内容为空，无需复制');
				return;
			}
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(val).then(function(){
					successTips('已成功复制到剪贴板');
				}).catch(function(){
					PAYMENT.fallbackCopy(val);
				});
			} else {
				PAYMENT.fallbackCopy(val);
			}
		});

		// 编辑账号
		$('#data-list .modify').on('click', function(){
			const btn = $(this);
			const tr = btn.closest('tr');
			const id = tr.data('id');
			let rowData = tr.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch(e){}
			}

			// 优先使用当前行基本数据即时回显，消除等待延迟
			if (rowData && rowData.payment_id) {
				_this.showEditModal(rowData);
			}

			btn.button('loading');
			post('', {opn: 'getInfo', id: id}, function(res){
				btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(e){}
				}
				if (res && (res.code == 1 || res.code == 200)) {
					const data = res.data || res.msg || {};
					_this.showEditModal(data);
				} else if (!rowData || !rowData.payment_id) {
					showTips(res || {code: 0, msg: '获取账户信息失败'});
				}
			});
		});

		// 保存账号配置
		$('#dealbox .save').on('click', function(){
			const btn = $(this);
			const form = $('#dealbox form');
			if (!form.formFilter()) {
				return false;
			}

			btn.button('loading');
			post('', form.serializeArray(), function(res){
				btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(e){}
				}
				showTips(res);
				if (res && (res.code == 1 || res.code == 200)) {
					$('#dealbox').dealboxHide();
					setTimeout(function(){
						window.location.reload();
					}, 600);
				}
			});
		});

		// 删除账号配置
		$('#data-list .delete').on('click', function(){
			const tr = $(this).closest('tr');
			const id = tr.data('id');
			confirm('确定要删除该支付账号吗？删除后相关站点的绑定将被解绑且不可恢复', function(btn, modal){
				post('', {opn: 'deleteInfo', id: id}, function(res){
					modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch(e){}
					}
					showTips(res);
					if (res && (res.code == 1 || res.code == 200)) {
						tr.fadeOut(300, function(){
							tr.remove();
						});
					}
				});
			});
		});

		// 状态/沙盒开关快速切换
		$('#data-list .switch_botton').on('click', function(){
			const btn = $(this);
			const currentStatus = btn.data('status') == 1 ? 1 : 0;
			const newStatus = currentStatus === 1 ? 0 : 1;
			const type = btn.data('type');
			const id = btn.closest('tr').data('id');

			post('', {opn: 'modifyInfo', id: id, [type]: newStatus}, function(res){
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(e){}
				}
				showTips(res);
				if (res && (res.code == 1 || res.code == 200)) {
					btn.switchBtn(newStatus);
				}
			});
		});

		// 弹窗关闭按钮
		$('#dealbox-view .close-btn, #dealbox-view .close, #dealbox-view .mask').on('click', function(){
			$('#dealbox-view').dealboxHide();
		});
		$('#dealbox .close, #dealbox .mask').on('click', function(){
			$('#dealbox').dealboxHide();
		});
	},

	fallbackCopy: function(text) {
		const tempTextarea = $('<textarea>').val(text).appendTo('body').select();
		try {
			document.execCommand('copy');
			successTips('已成功复制到剪贴板');
		} catch(err) {
			errorTips('复制失败，请手动选取复制');
		}
		tempTextarea.remove();
	},

	showViewModal: function(data) {
		if (!data) return false;
		const obj = $('#dealbox-view');

		// 头部账号信息单独一行展示
		let nameText = data.name || '-';
		if (data.remark) {
			nameText += ' (' + data.remark + ')';
		}
		obj.find('.view-account-name').text(nameText);

		// 账号类型
		const typeName = data.type == 1 ? 'PayPal' : (data.type == 2 ? 'Stripe' : '其他类型');
		obj.find('.view-account-type').text(typeName);

		// 运行环境
		const isSandbox = (data.is_sandbox == 1);
		const envElem = obj.find('.view-account-env');
		if (isSandbox) {
			envElem.removeClass('label-success').addClass('label-warning').text('沙盒测试');
		} else {
			envElem.removeClass('label-warning').addClass('label-success').text('正式生产');
		}

		// 秘钥字段填充
		obj.find('textarea[name="app_key"]').val(data.app_key || '');
		obj.find('textarea[name="secret_key"]').val(data.secret_key || '');
		obj.find('textarea[name="webhook_key"]').val(data.webhook_key || '');
		obj.dealboxShow('配置信息秘钥');
	},

	showEditModal: function(data) {
		const obj = $('#dealbox');
		obj.find('input:not(.no_replace)').val('');
		obj.find('select').prop('selectedIndex', 0);

		if (data && data.payment_id && parseInt(data.payment_id) > 0) {
			obj.find('.dealbox-title').text('编辑支付账号');
			obj.find('input[name="payment_id"]').val(data.payment_id);
			obj.find('input[name="name"]').val(data.name || '');
			obj.find('select[name="type"]').val(data.type || '');
			obj.find('input[name="app_key"]').val(data.app_key || '');
			obj.find('input[name="secret_key"]').val(data.secret_key || '');
			obj.find('input[name="webhook_key"]').val(data.webhook_key || '');
			obj.find('select[name="is_sandbox"]').val(typeof data.is_sandbox !== 'undefined' ? data.is_sandbox : '0');
			obj.find('select[name="status"]').val(typeof data.status !== 'undefined' ? data.status : '1');
			obj.find('input[name="remark"]').val(data.remark || '');
		} else {
			obj.find('.dealbox-title').text('添加支付账号');
			obj.find('input[name="payment_id"]').val(0);
			obj.find('select[name="is_sandbox"]').val('0');
			obj.find('select[name="status"]').val('1');
		}

		obj.dealboxShow();
	}
};