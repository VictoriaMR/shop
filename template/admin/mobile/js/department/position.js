$(function() {
	POSITION_MOBILE.init();
});

const POSITION_MOBILE = {
	url: '',
	init: function() {
		var $page = $('#position-page');
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

		// 状态下拉自动提交
		$('.filter-select').on('change', function() {
			$(this).closest('form').submit();
		});

		// 新增职位
		$('#add-pos-btn').on('click', function() {
			_this.openModal({
				pos_id: 0,
				name: '',
				remark: '',
				status: 1,
				sort: 0
			});
		});

		// 编辑职位
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
			post(_this.url, { opn: 'modifyPosStatus', pos_id: $card.data('id'), status: newStatus }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$sw.switchBtn(newStatus);
				}
			});
		});

		// 弹窗状态开关
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
				opn: 'sortPos',
				pos_id: $card.data('id'),
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

		// 删除职位
		$('#data-list').on('click', '.btn.delete', function(e) {
			e.stopPropagation();
			var $card = $(this).closest('.item');
			var posId = $card.data('id');
			var posName = $.trim($card.find('.pos-name').text());
			confirm('确定要删除职位【' + posName + '】吗？', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deletePos', pos_id: posId }, function(res) {
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

		// 保存职位
		$('#dealbox').on('click', '.save-btn', function() {
			var $btn = $(this);
			var $form = $('#pos-form');
			var name = $.trim($form.find('[name="name"]').val());
			if (name === '') {
				errorTips('职位名称不能为空');
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
		var posId = parseInt(data.pos_id || 0, 10);
		var status = typeof data.status !== 'undefined' ? (parseInt(data.status, 10) === 1 ? 1 : 0) : 1;

		$box.find('.dealbox-title').text(posId > 0 ? '编辑职位' : '新增职位');
		$box.find('[name="pos_id"]').val(posId);
		$box.find('[name="name"]').val(data.name || '');
		$box.find('[name="remark"]').val(data.remark || '');
		$box.find('[name="sort"]').val(typeof data.sort !== 'undefined' ? data.sort : 0);
		$box.find('.switch_botton').switchBtn(status);
		$box.find('[name="status"]').val(status);
		$box.fadeIn(150);
	}
};
