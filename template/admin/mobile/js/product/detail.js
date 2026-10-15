$(function() {
	PRODUCT_DETAIL.init();
});

const PRODUCT_DETAIL = {
	url: '/product/detail',
	spuId: 0,
	init: function() {
		const _this = this;
		const $page = $('.detail-page');
		this.spuId = $page.data('id');
		this.url = $page.data('url') || '/product/detail';

		// 弹窗关闭
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 状态弹窗
		$('.status-btn').on('click', function() {
			$('#status-dealbox').fadeIn(150);
		});

		// 分类弹窗
		$('.category-btn').on('click', function() {
			$('#category-dealbox').fadeIn(150);
		});

		// 性别弹窗
		$('.gender-btn').on('click', function() {
			$('#gender-dealbox').fadeIn(150);
		});

		// 扩展数据弹窗 (邮费、体积、重量)
		$('.data-btn').on('click', function() {
			const $btn = $(this);
			const $modal = $('#dealbox-data');
			const label = $btn.data('label') || '扩展数据';
			const fieldName = $btn.data('name');
			const val = $btn.data('val') !== undefined ? $btn.data('val') : '';
			$modal.find('.dealbox-title').text('修改' + label);
			$modal.find('.data-field-label').text(label);
			$modal.find('[name="name"]').val(fieldName);
			$modal.find('[name="value"]').val(val);
			$modal.fadeIn(150);
		});

		// 弹窗通用保存按钮
		$('.modal-panel .save-btn').on('click', function() {
			const $btn = $(this);
			const $form = $btn.closest('form');
			$form.find('input.batch-spu-hidden').remove();
			if ($btn.hasClass('batch-save-btn')) {
				$form.append('<input type="hidden" class="batch-spu-hidden" name="spu_id" value="' + _this.spuId + '">');
			}
			$btn.button('loading');
			post(_this.url, $form.serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					if ($form.find('[name="opn"]').val() !== 'editSpuLanguage') {
						window.location.reload();
					} else {
						$btn.closest('.modal-mask').fadeOut(150);
						$btn.button('reset');
					}
				} else {
					$btn.button('reset');
				}
			});
			return false;
		});

		// 免运费开关
		$('.switch_botton.free-ship').on('click', function() {
			const $sw = $(this);
			const status = parseInt($sw.data('status') || 0, 10) === 0 ? 1 : 0;
			post(_this.url, { spu_id: _this.spuId, free_ship: status, opn: 'editInfo' }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$sw.switchBtn(status);
				}
			});
		});

		// 名称多语言弹窗
		$('.name-trans-btn').on('click', function() {
			const $btn = $(this);
			const sourceName = $.trim($('.spu-name-box .name').text());
			$btn.button('loading');
			post(_this.url, { opn: 'getSpuNameLanguage', id: _this.spuId }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1 && res.data) {
					const data = res.data;
					const $modal = $('#dealbox-language');
					$modal.find('input[name="name"]').val(sourceName);
					const $list = $modal.find('.language-list').empty();
					for (const i in data) {
						const row = data[i];
						const $item = $('<div class="lang-item">');
						$item.append($('<span class="lang-label">').text((row.language_name || i) + '：'));
						$item.append(
							$('<textarea rows="2" class="form-control" autocomplete="off"></textarea>')
								.attr('name', 'language[' + i + ']')
								.attr('data-tr_code', row.tr_code || '')
								.val(row.name || '')
						);
						$list.append($item);
					}
					$modal.fadeIn(150);
				} else {
					showTips(res || { code: 0, msg: '获取多语言失败' });
				}
			});
		});

		// 智能翻译
		$('#dealbox-language').on('click', '.translate-all-btn', function() {
			const $btn = $(this);
			const name = $('#dealbox-language input[name="name"]').val();
			const $emptyList = $('#dealbox-language textarea').filter(function() {
				return $.trim($(this).val()) === '' && $(this).data('tr_code');
			});
			let len = $emptyList.length;
			if (len === 0) {
				successTips('所有语言均已填写');
				return;
			}
			$btn.button('loading');
			$emptyList.each(function() {
				const $textarea = $(this);
				const tr_code = $textarea.data('tr_code');
				post(_this.url, { opn: 'transfer', tr_code: tr_code, name: name }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					len--;
					if (res && res.code == 1) {
						$textarea.val(res.data);
					} else {
						showTips(res);
					}
					if (len <= 0) {
						$btn.button('reset');
					}
				});
			});
		});

		// 更改SPU图片排序
		$('.spu-image input[name="sort"]').on('change', function() {
			const $input = $(this);
			const attachId = $input.closest('.spu-image').data('id');
			post(_this.url, { spu_id: _this.spuId, attach_id: attachId, sort: $input.val(), opn: 'modifySpuImage' }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 删除SPU图片
		$('.spu-image .delete-spu-img-btn').on('click', function() {
			const itemId = $(this).closest('.spu-image').data('item_id');
			confirm('确定要删除该图片吗?', function($confirmBtn) {
				$confirmBtn.button('loading');
				post(_this.url, { spu_id: _this.spuId, item_id: itemId, opn: 'deleteSpuImage' }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						$confirmBtn.button('reset');
					}
				});
			});
		});

		// 设置SPU主图
		$('.spu-image .spu-btn').on('click', function() {
			const attachId = $(this).closest('.spu-image').data('id');
			confirm('确定设置该图片为主图吗?', function($confirmBtn) {
				$confirmBtn.button('loading');
				post(_this.url, { spu_id: _this.spuId, attach_id: attachId, opn: 'editInfo' }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						$confirmBtn.button('reset');
					}
				});
			});
		});

		// 设置SKU主图
		$('.spu-image .sku-btn').on('click', function() {
			const attachId = $(this).closest('.spu-image').data('id');
			const $modal = $('#dealbox-sku-image');
			$modal.find('input[name="attach_id"]').val(attachId);
			$modal.find('.item').each(function() {
				if (parseInt($(this).data('id'), 10) === parseInt(attachId, 10)) {
					$(this).find('input').prop('checked', true).prop('disabled', true);
				} else {
					$(this).find('input').prop('checked', false).prop('disabled', false);
				}
			});
			$modal.fadeIn(150);
		});

		// 上传SPU图片
		if ($.fn.imageUpload) {
			$('.upload-image').imageUpload('product', function(data, $obj) {
				$obj.button('loading');
				post(_this.url, { opn: 'addSpuImage', spu_id: _this.spuId, attach_id: data.attach_id }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						$obj.button('reset');
					}
				});
			});
		}

		// SKU状态开关
		$('.switch_botton.sku-status').on('click', function() {
			const $sw = $(this);
			const status = parseInt($sw.data('status') || 0, 10) === 0 ? 1 : 0;
			const skuId = $sw.closest('.item-row').data('id');
			post(_this.url, { sku_id: skuId, status: status, opn: 'editSkuInfo' }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$sw.switchBtn(status);
				}
			});
		});

		// 点击修改SKU价格/库存/体积/重量
		$('.can-edit').on('click', function() {
			const $cell = $(this);
			const $modal = $('#dealbox-sku-info');
			const skuId = $cell.closest('.item-row').data('id');
			const value = $.trim($cell.find('.m-val').text());
			const name = $cell.data('name');
			const textMap = {
				price: '售价',
				original_price: '原价',
				cost_price: '成本价',
				stock: '库存',
				volume: '体积(mm)',
				weight: '重量(g)'
			};
			const label = textMap[name] || name;
			$modal.find('.name').val(value).attr('name', name);
			$modal.find('[name="sku_id"]').val(skuId);
			$modal.find('.batch-spu-hidden').remove();
			$modal.find('.dealbox-title').text('SKU' + label + '管理 (#' + skuId + ')');
			$modal.find('.sku-field-label').text(label);
			$modal.fadeIn(150);
		});

		// SKU属性图片上传
		if ($.fn.imageUpload) {
			$('.sku-attr-image').imageUpload('product', function(data, $obj) {
				const $card = $obj.closest('.attr-son-card');
				const param = {
					spu_id: $card.data('spu_id'),
					attrn_id: $card.data('attrn_id'),
					attrv_id: $card.data('attrv_id'),
					attach_id: data.attach_id,
					opn: 'modifySkuAttrImage'
				};
				post(_this.url, param, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					}
				});
			});
		}

		// 删除SKU属性图片
		$('.delete-attr-image-btn').on('click', function() {
			const $card = $(this).closest('.attr-son-card');
			const param = {
				spu_id: $card.data('spu_id'),
				attrn_id: $card.data('attrn_id'),
				attrv_id: $card.data('attrv_id'),
				attach_id: 0,
				opn: 'modifySkuAttrImage'
			};
			confirm('确定要删除该属性图片吗?', function($confirmBtn) {
				$confirmBtn.button('loading');
				post(_this.url, param, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						$confirmBtn.button('reset');
					}
				});
			});
		});

		// 描述排序
		$('#sku-desc-content [name="sort"]').on('change', function() {
			const id = $(this).closest('.desc-item-card').data('id');
			const sort = $(this).val();
			post(_this.url, { opn: 'modifySpuDesc', item_id: id, sort: sort }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 删除描述文本
		$('.delete-desc-btn').on('click', function() {
			const id = $(this).closest('.desc-item-card').data('id');
			confirm('确定要删除该描述文本吗?', function($confirmBtn) {
				$confirmBtn.button('loading');
				post(_this.url, { opn: 'deleteSpuDesc', item_id: id }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						$confirmBtn.button('reset');
					}
				});
			});
		});

		// 新增描述文本
		$('.add-desc-btn').on('click', function() {
			_this.initDescShow();
		});

		// 编辑描述文本
		$('.edit-desc-btn').on('click', function() {
			const $btn = $(this);
			const id = $btn.closest('.desc-item-card').data('id');
			$btn.button('loading');
			post(_this.url, { opn: 'getSpuDescInfo', item_id: id }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.initDescShow(res.data);
				} else {
					showTips(res || { code: 0, msg: '获取描述信息失败' });
				}
			});
		});

		// 描述选中切换
		$('#sku-desc-content').on('click', '.desc-check-icon', function() {
			$(this).toggleClass('selected');
			_this.initDescSelect();
		});

		// 分组按钮点击
		$('#sku-desc-content .desc-group-btn').on('click', function() {
			if ($('#sku-desc-content .desc-check-icon.selected').length === 0) {
				errorTips('请先点击勾选需要分组的描述项');
				return false;
			}
			$('#desc-group-dealbox').fadeIn(150);
		});

		// 描述图片排序
		$('.spu-introduce-image [name="sort"]').on('change', function() {
			const $input = $(this);
			const itemId = $input.closest('.image-btn').data('id');
			const attachId = $input.closest('.spu-introduce-image').data('id');
			post(_this.url, { item_id: itemId, attach_id: attachId, sort: $input.val(), opn: 'modifySpuIntroduceImage' }, function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 删除描述图片
		$('.spu-introduce-image .delete-introduce-btn').on('click', function() {
			const itemId = $(this).closest('.image-btn').data('id');
			confirm('确定删除该描述图片吗?', function($confirmBtn) {
				$confirmBtn.button('loading');
				post(_this.url, { item_id: itemId, opn: 'deleteSpuIntroduceImage' }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						$confirmBtn.button('reset');
					}
				});
			});
		});

		// 上传描述图片
		if ($.fn.imageUpload) {
			$('.upload-introduce-image').imageUpload('introduce', function(data, $obj) {
				$obj.button('loading');
				post(_this.url, { opn: 'addSpuIntroduceImage', spu_id: _this.spuId, attach_id: data.attach_id }, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (e) {}
					}
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						$obj.button('reset');
					}
				});
			});
		}
	},

	initDescShow: function(data) {
		const $modal = $('#dealbox-desc');
		if (data && data.item_id) {
			$modal.find('.dealbox-title').text('编辑描述文本');
		} else {
			data = {
				item_id: 0,
				name: '',
				value: '',
				sort: 0
			};
			$modal.find('.dealbox-title').text('增加描述文本');
		}
		for (const i in data) {
			$modal.find('[name="' + i + '"]').val(data[i]);
		}
		$modal.fadeIn(150);
	},

	initDescSelect: function() {
		const $modal = $('#desc-group-dealbox');
		$modal.find('[name="id[]"]').remove();
		$('#sku-desc-content .desc-check-icon.selected').each(function() {
			const id = $(this).closest('.desc-item-card').data('id');
			$modal.find('form').append('<input type="hidden" name="id[]" value="' + id + '">');
		});
	}
};
