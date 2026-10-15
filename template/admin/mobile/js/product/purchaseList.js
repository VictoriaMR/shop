$(function() {
	OPERATELIST.init();
});

const OPERATELIST = {
	url: '',
	init: function() {
		const _this = this;
		this.url = $('#purchase-list-page').data('url') || '';

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 编辑状态按钮
		$('#data-list').on('click', '.btn-edit', function() {
			const $card = $(this).closest('.item');
			_this.initModal({
				id: $card.data('id'),
				status: $card.data('status')
			});
		});

		// 保存状态
		$('.edit-status-modal .btn-save').on('click', function() {
			const statusVal = parseInt($('.edit-status-modal [name="status"]').val(), 10);
			if (isNaN(statusVal) || statusVal < 0) {
				errorTips('请选择状态');
				return false;
			}
			const $btn = $(this);
			$btn.button('loading');
			post(_this.url, $('.edit-status-modal').serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$('.edit-status-mask').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 400);
				} else {
					$btn.button('reset');
				}
			});
		});
	},

	initModal: function(data) {
		const $form = $('.edit-status-modal');
		for (const i in data) {
			$form.find('[name="' + i + '"]').val(data[i]);
		}
		$('.edit-status-mask').fadeIn(150);
	}
};
