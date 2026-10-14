$(function() {
	CATEGORY_INDEX.init();
});

const CATEGORY_INDEX = {
	url: '',
	init: function() {
		this.url = $('#category-page').data('url') || '';
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

		// 新增品类
		$('#add-data-btn').on('click', function() {
			_this.showEditModal();
		});

		// 编辑品类
		$('#data-list').on('click', '.modify', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $item = $btn.closest('.item');
			var id = $item.data('id');
			if (!id) return;

			$btn.button('loading');
			post(_this.url, { opn: 'getCateInfo', cate_id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				if (res && res.code == 1) {
					_this.showEditModal(res.data || {});
				} else {
					showTips(res || { code: 0, msg: '获取品类信息失败' });
				}
			});
		});

		// 状态切换开关
		$('#data-list').on('click', '.switch_botton', function(e) {
			e.stopPropagation();
			var $btn = $(this);
			var $item = $btn.closest('.item');
			var id = $item.data('id');
			var curStatus = parseInt($btn.data('status') || 0, 10);
			var newStatus = curStatus === 1 ? 0 : 1;

			post(_this.url, { opn: 'modifyCategory', id: id, status: newStatus }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (err) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$btn.switchBtn(newStatus);
				}
			});
		});

		// 保存品类
		$('#dealbox').on('click', '.save', function() {
			var $form = $('#cate-form');
			var name = $.trim($form.find('input[name="name"]').val());
			if (!name) {
				errorTips('品类中文名称不能为空');
				$form.find('input[name="name"]').focus();
				return false;
			}

			var $btn = $(this);
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

		// 删除品类
		$('#data-list').on('click', '.delete', function(e) {
			e.stopPropagation();
			var $item = $(this).closest('.item');
			var id = $item.data('id');
			var name = $.trim($item.find('.cate_name').text());

			confirm('确定要删除品类【' + name + '】吗？', function(btn, modal) {
				btn.button('loading');
				post(_this.url, { opn: 'deleteCategory', cate_id: id }, function(res) {
					btn.button('reset');
					if (modal && modal.hide) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (err) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						$item.fadeOut(200, function() {
							$item.remove();
							if ($('#data-list .item').length === 0) {
								window.location.reload();
							}
						});
					}
				});
			});
		});
	},

	showEditModal: function(data) {
		data = data || { cate_id: 0, parent_id: 0, name: '', name_en: '' };
		var $box = $('#dealbox');
		$box.find('input[name="cate_id"]').val(data.cate_id || 0);
		$box.find('input[name="parent_id"]').val(data.parent_id || 0);
		$box.find('input[name="name"]').val(data.name || '');
		$box.find('input[name="name_en"]').val(data.name_en || '');
		$box.find('.dealbox-title').text(data.cate_id > 0 ? '编辑品类' : '新增品类');
		$box.fadeIn(150);
	}
};
