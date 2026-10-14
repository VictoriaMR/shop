$(function() {
	PAYMENT.init();
});

const PAYMENT = {
	url: '',
	init: function() {
		this.url = $('#payment-index-page').data('url') || '';
		var _this = this;

		// 新增支付账号
		$('#add-data-btn').on('click', function() {
			_this.initForm();
			$('#dealbox').fadeIn(150);
		});

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 状态/沙盒开关切换
		$('#data-list').on('click', '.switch_botton', function() {
			var $this = $(this);
			var id = $this.closest('.payment-card').data('id');
			if (!id) return;

			var currentStatus = parseInt($this.attr('data-status'), 10) === 1 ? 1 : 0;
			var nextStatus = currentStatus === 1 ? 0 : 1;
			var type = $this.data('type');
			var param = { opn: 'modifyInfo', id: id };
			param[type] = nextStatus;

			post(_this.url, param, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$this.switchBtn(nextStatus);
				}
			});
		});

		// 查看秘钥
		$('#data-list').on('click', '.view-keys', function() {
			var $card = $(this).closest('.payment-card');
			var id = $card.data('id');
			if (!id) return;

			var rowData = $card.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch (e) {}
			}

			post(_this.url, { opn: 'getInfo', id: id }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.openViewModal(res.data, rowData);
				} else if (rowData && rowData.payment_id) {
					_this.openViewModal(rowData, rowData);
				} else {
					showTips(res || { code: 0, msg: '获取配置信息失败' });
				}
			});
		});

		// 复制秘钥
		$('#dealbox-view').on('click', '.copy-btn', function() {
			var $btn = $(this);
			var targetName = $btn.data('target');
			var text = $('#dealbox-view [name="' + targetName + '"]').val() || '';
			if (!text) {
				errorTips('当前秘钥为空');
				return;
			}
			_this.copyText(text, function() {
				var oldHtml = $btn.html();
				$btn.html('<i class="fa fa-check"></i> 已复制');
				successTips('已成功复制到剪贴板');
				setTimeout(function() {
					$btn.html(oldHtml);
				}, 1500);
			});
		});

		// 编辑按钮
		$('#data-list').on('click', '.modify', function() {
			var $card = $(this).closest('.payment-card');
			var id = $card.data('id');
			if (!id) return;

			var rowData = $card.data('info') || {};
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch (e) {}
			}

			post(_this.url, { opn: 'getInfo', id: id }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.initForm(res.data);
					$('#dealbox').fadeIn(150);
				} else if (rowData && rowData.payment_id) {
					_this.initForm(rowData);
					$('#dealbox').fadeIn(150);
				} else {
					showTips(res || { code: 0, msg: '获取账户信息失败' });
				}
			});
		});

		// 删除按钮
		$('#data-list').on('click', '.delete', function() {
			var $card = $(this).closest('.payment-card');
			var id = $card.data('id');
			if (!id) return;

			confirm('确认要删除该支付账号吗？删除后关联的站点配置也将失效！', function(btn, modal) {
				post(_this.url, { opn: 'deleteInfo', id: id }, function(res) {
					if (modal) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						$card.fadeOut(250, function() {
							$card.remove();
						});
					}
				});
			});
		});

		// 保存提交
		$('#dealbox').on('click', '.save', function() {
			var $btn = $(this);
			var name = $.trim($('#dealbox [name="name"]').val());
			var appKey = $.trim($('#dealbox [name="app_key"]').val());
			var secretKey = $.trim($('#dealbox [name="secret_key"]').val());
			if (!name) {
				errorTips('账户名称不能为空');
				return false;
			}
			if (!appKey) {
				errorTips('AppKey 不能为空');
				return false;
			}
			if (!secretKey) {
				errorTips('SecretKey 不能为空');
				return false;
			}
			$btn.button('loading');

			post(_this.url, $('#payment-form').serializeArray(), function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$('#dealbox').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 600);
				}
			});
		});
	},

	initForm: function(data) {
		var $box = $('#dealbox');
		if (!data) {
			$box.find('.dealbox-title').text('新增支付账号');
			$box.find('[name="payment_id"]').val(0);
			$box.find('[name="name"]').val('');
			$box.find('[name="type"]').val($box.find('[name="type"] option:first').val() || 1);
			$box.find('[name="app_key"]').val('');
			$box.find('[name="secret_key"]').val('');
			$box.find('[name="webhook_key"]').val('');
			$box.find('[name="remark"]').val('');
			$box.find('[name="is_sandbox"][value="0"]').prop('checked', true);
			$box.find('[name="status"][value="1"]').prop('checked', true);
		} else {
			$box.find('.dealbox-title').text('编辑支付账号');
			$box.find('[name="payment_id"]').val(data.payment_id || 0);
			$box.find('[name="name"]').val(data.name || '');
			$box.find('[name="type"]').val(data.type || 1);
			$box.find('[name="app_key"]').val(data.app_key || '');
			$box.find('[name="secret_key"]').val(data.secret_key || '');
			$box.find('[name="webhook_key"]').val(data.webhook_key || '');
			$box.find('[name="remark"]').val(data.remark || '');
			$box.find('[name="is_sandbox"][value="' + (data.is_sandbox == 1 ? 1 : 0) + '"]').prop('checked', true);
			$box.find('[name="status"][value="' + (data.status == 1 ? 1 : 0) + '"]').prop('checked', true);
		}
	},

	openViewModal: function(info, cardInfo) {
		var $view = $('#dealbox-view');
		var typeName = info.type_name || (cardInfo && cardInfo.type_name) || ('类型#' + (info.type || ''));
		var isSandbox = parseInt(info.is_sandbox, 10) === 1;
		$view.find('.view-account-name').text((info.name || '-') + ' (#' + (info.payment_id || '-') + ')');
		$view.find('.view-account-type').text(typeName);
		$view.find('.view-account-env')
			.removeClass('sandbox prod')
			.addClass(isSandbox ? 'sandbox' : 'prod')
			.text(isSandbox ? '沙盒环境' : '正式环境');
		$view.find('[name="app_key"]').val(info.app_key || '');
		$view.find('[name="secret_key"]').val(info.secret_key || '');
		$view.find('[name="webhook_key"]').val(info.webhook_key || '');
		$view.fadeIn(150);
	},

	copyText: function(text, callback) {
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(callback);
		} else {
			var $temp = $('<textarea>');
			$('body').append($temp);
			$temp.val(text).select();
			document.execCommand('copy');
			$temp.remove();
			if (typeof callback === 'function') callback();
		}
	}
};
