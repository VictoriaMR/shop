$(function() {
	SITE_CATEGORY.init();
});

const SITE_CATEGORY = {
	init: function() {
		var _this = this;

		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		$('.add-btn').on('click', function() {
			_this.initData();
		});

		$('#dealbox').on('click', '.save-btn', function() {
			var $btn = $(this);
			var $form = $('#site-cate-form');
			var siteId = parseInt($form.find('[name="site_id"]').val(), 10);
			var cateId = parseInt($form.find('[name="cate_id"]').val(), 10);
			if (siteId < 1) {
				errorTips('站点不能为空');
				return false;
			}
			if (cateId < 1) {
				errorTips('分类不能为空');
				return false;
			}
			$btn.button('loading');
			post('/category/siteCategory', $form.serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				} else {
					$btn.button('reset');
				}
			});
		});

		$('#data-list').on('click', '.delete', function() {
			var $item = $(this).closest('.item');
			var id = $item.data('id');
			confirm('确定要删除该站点分类吗？', function(btn, modal) {
				btn.button('loading');
				post('/category/siteCategory', { opn: 'deleteSiteCategory', id: id }, function(res) {
					if (modal && modal.hide) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						btn.button('reset');
					}
				});
			});
		});

		if ($.fn.imageUpload) {
			$('#data-list .cate-icon-upload').imageUpload('category', function(data, $trigger) {
				var id = $trigger.closest('.item').data('id');
				post('/category/siteCategory', { opn: 'modifySiteCategory', id: id, attach_id: data.attach_id }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
				});
			});
		}

		$('#data-list').on('blur', 'input', function() {
			var id = $(this).closest('.item').data('id');
			var name = this.name;
			var value = $(this).val();
			var param = { opn: 'modifySiteCategory', id: id };
			param[name] = value;
			post('/category/siteCategory', param, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1 && name === 'sort') {
					window.location.reload();
				}
			});
		});
	},

	initData: function(data) {
		data = data || { item_id: '0', site_id: '-1', cate_id: '-1' };
		var $box = $('#dealbox');
		$.each(data, function(k, v) {
			$box.find('[name="' + k + '"]').val(v);
		});
		$box.fadeIn(150);
	}
};
