$(function() {
	PAYMENTUSED.init();
});

const PAYMENTUSED = {
	url: '',
	init: function() {
		this.url = $('#payment-used-page').data('url') || '';
		var _this = this;

		// 新增按钮
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

		// 编辑按钮
		$('#data-list').on('click', '.modify', function() {
			var id = $(this).closest('.used-card').data('id');
			if (!id) return;

			post(_this.url, { opn: 'getUsedInfo', id: id }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.initForm(res.data);
					$('#dealbox').fadeIn(150);
				} else {
					showTips(res || { code: 0, msg: '获取配置失败' });
				}
			});
		});

		// 删除按钮
		$('#data-list').on('click', '.delete', function() {
			var $card = $(this).closest('.used-card');
			var id = $card.data('id');
			if (!id) return;

			confirm('确认要删除该条支付账号使用配置吗？', function(btn, modal) {
				post(_this.url, { opn: 'deleteUsedInfo', id: id }, function(res) {
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

		// 保存按钮
		$('#dealbox').on('click', '.save', function() {
			var $btn = $(this);
			var siteId = parseInt($('#dealbox [name="site_id"]').val(), 10);
			var type = parseInt($('#dealbox [name="type"]').val(), 10);
			var paymentId = parseInt($('#dealbox [name="payment_id"]').val(), 10);

			if (!siteId || siteId <= 0) {
				errorTips('请选择站点');
				return false;
			}
			if (!type || type <= 0) {
				errorTips('请选择支付类型');
				return false;
			}
			if (!paymentId || paymentId <= 0) {
				errorTips('请选择支付账号');
				return false;
			}
			$btn.button('loading');

			post(_this.url, $('#used-form').serializeArray(), function(res) {
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
			$box.find('.dealbox-title').text('新增账号使用');
			$box.find('[name="item_id"]').val(0);
			$box.find('[name="site_id"]').val(0);
			$box.find('[name="type"]').val(0);
			$box.find('[name="payment_id"]').val(0);
		} else {
			$box.find('.dealbox-title').text('编辑账号使用');
			$box.find('[name="item_id"]').val(data.item_id || 0);
			$box.find('[name="site_id"]').val(data.site_id || 0);
			$box.find('[name="type"]').val(data.type || 0);
			$box.find('[name="payment_id"]').val(data.payment_id || 0);
		}
	}
};
