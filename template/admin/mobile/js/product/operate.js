$(function() {
	OPERATE.init();
});

const OPERATE = {
	url: '',
	init: function() {
		const _this = this;
		this.url = $('#add-product-page').data('url') || '';
		this.updateSkuStats();
		this.updateDescCount();

		// 行内直接编辑属性名：实时同步到SKU列表
		$('.attr-info-content').on('input change', '.inline-attr-name', function() {
			const $input = $(this);
			const fromName = String($input.attr('data-from-name') || '');
			const toName = $.trim($input.val()) || fromName;
			if (fromName) {
				_this.mapAttrName(fromName, toName, true);
			}
		});

		// 行内直接编辑属性值：实时同步到SKU列表
		$('.attr-info-content').on('input change', '.inline-attr-val', function() {
			const $input = $(this);
			const fromVal = String($input.attr('data-from-val') || '');
			const mainVal = $.trim($input.val()) || fromVal;
			const $item = $input.closest('.attr-value');
			const extObj = _this.parseExt($item.attr('data-ext')) || {};
			if (fromVal) {
				_this.mapAttrValue(fromVal, mainVal, extObj, true);
			}
		});

		// 点击属性标签或"+"按钮打开映射弹窗
		$('.attr-info-content').on('click', '.attr-item', function(e) {
			if ($(e.target).hasClass('attr-val-check') || $(e.target).is('input')) {
				return;
			}
			const $chip = $(this);
			const type = $chip.hasClass('attr-name') ? '1' : '2';
			const fromName = String($chip.attr('data-name') || '');
			const $row = $chip.closest('.attr-map-row');
			const rawAttrName = String($row.find('.attr-name').attr('data-name') || '');
			const mappedAttrName = $.trim($row.find('.inline-attr-name').val()) || rawAttrName;

			let toName = '';
			if (type === '1') {
				toName = mappedAttrName || fromName;
			} else {
				toName = $.trim($chip.find('.inline-attr-val').val()) || $chip.attr('data-mapped') || fromName;
			}

			let extHtml = '';
			if (type === '2') {
				let extObj = _this.parseExt($chip.attr('data-ext'));
				let setValue = true;
				if (!extObj) {
					setValue = false;
					const $siblingSuccess = $chip.closest('.attr-row-vals').find('.attr-value.success').first();
					if ($siblingSuccess.length) {
						extObj = _this.parseExt($siblingSuccess.attr('data-ext'));
					}
				}
				if (extObj) {
					for (const k in extObj) {
						if (Object.prototype.hasOwnProperty.call(extObj, k) && k !== mappedAttrName) {
							extHtml += _this.buildExtItemHtml(k, setValue ? extObj[k] : '');
						}
					}
				}
			}

			let html = '<div class="input-group">' +
				'<div class="input-group-addon"><span>' + _this.escapeHtml(type === '1' ? '属性名' : mappedAttrName) + '</span></div>' +
				'<input class="form-control" type="text" name="name" placeholder="请输入映射值" value="' + _this.escapeHtml(toName) + '">' +
				'</div>' +
				'<div class="add-map-content">' + extHtml + '</div>' +
				'<div class="map-modal-actions">';
			if (type === '2') {
				html += '<button type="button" class="btn btn-info btn-xs btn-add">新增衍生属性</button>';
			}
			html += '<button type="button" class="btn btn-default btn-xs btn-raw-save">使用原值</button>' +
				'<button type="button" class="btn btn-success btn-xs btn-tmp-save">确认应用</button>' +
				'<button type="button" class="btn btn-primary btn-xs btn-save">写入字典</button>' +
				'</div>';

			const $box = $('.map-modal-box');
			const $form = $box.find('.map-modal');
			$form.find('[name="from_name"]').val(fromName);
			$form.find('[name="type"]').val(type);
			$form.find('.content').html(html);
			$box.dealboxShow('属性映射与衍生: ' + fromName);
		});

		// 属性维度入库勾选 / 取消
		$('.attr-info-content').on('change', '.attr-group-check', function() {
			const checked = $(this).prop('checked');
			const groupName = String($(this).closest('.attr-map-row').attr('data-attr-group') || '');
			_this.toggleAttrGroup(groupName, checked, true);
		});

		// 属性值勾选 / 取消
		$('.attr-info-content').on('change', '.attr-val-check', function(e) {
			e.stopPropagation();
			const checked = $(this).prop('checked');
			const $valItem = $(this).closest('.attr-value');
			const $row = $(this).closest('.attr-map-row');
			const groupName = String($row.attr('data-attr-group') || '');
			const valCount = parseInt($row.attr('data-val-count') || '0', 10);

			$valItem.toggleClass('val-unchecked', !checked);
			$valItem.find('.inline-attr-val, .inline-attr-ext').prop('disabled', !checked);
			if (valCount <= 1) {
				_this.toggleAttrGroup(groupName, checked, true);
				return;
			}
			if (checked && !$row.find('.attr-group-check').prop('checked')) {
				_this.toggleAttrGroup(groupName, true, false);
			}
			_this.syncSkuRowsByAttrValues();
		});

		// 属性组内全选值 / 反选值
		$('.attr-info-content').on('click', '.attr-val-select-all', function() {
			const $row = $(this).closest('.attr-map-row');
			const groupName = String($row.attr('data-attr-group') || '');
			if (!$row.find('.attr-group-check').prop('checked')) {
				_this.toggleAttrGroup(groupName, true, false);
			}
			$row.find('.attr-val-check').prop('checked', true).each(function() {
				const $v = $(this).closest('.attr-value');
				$v.removeClass('val-unchecked');
				$v.find('.inline-attr-val, .inline-attr-ext').prop('disabled', false);
			});
			_this.syncSkuRowsByAttrValues();
		});
		$('.attr-info-content').on('click', '.attr-val-select-invert', function() {
			const $row = $(this).closest('.attr-map-row');
			const groupName = String($row.attr('data-attr-group') || '');
			if (!$row.find('.attr-group-check').prop('checked')) {
				_this.toggleAttrGroup(groupName, true, false);
			}
			$row.find('.attr-val-check').each(function() {
				const next = !$(this).prop('checked');
				$(this).prop('checked', next);
				const $v = $(this).closest('.attr-value');
				$v.toggleClass('val-unchecked', !next);
				$v.find('.inline-attr-val, .inline-attr-ext').prop('disabled', !next);
			});
			_this.syncSkuRowsByAttrValues();
		});

		// 属性快捷工具栏
		$('#btn-attr-group-all').on('click', function() {
			$('.attr-info-content .attr-map-row').each(function() {
				_this.toggleAttrGroup(String($(this).attr('data-attr-group') || ''), true, true);
			});
		});
		$('#btn-attr-group-invert').on('click', function() {
			$('.attr-info-content .attr-map-row').each(function() {
				const next = !$(this).find('.attr-group-check').prop('checked');
				_this.toggleAttrGroup(String($(this).attr('data-attr-group') || ''), next, true);
			});
		});
		$('#btn-attr-ignore-single').on('click', function() {
			const $singleRows = $('.attr-info-content .attr-map-row').filter(function() {
				return parseInt($(this).attr('data-val-count') || '0', 10) <= 1;
			});
			if ($singleRows.length === 0) {
				errorTips('当前没有单值属性维度');
				return;
			}
			const hasChecked = $singleRows.find('.attr-group-check:checked').length > 0;
			$singleRows.each(function() {
				_this.toggleAttrGroup(String($(this).attr('data-attr-group') || ''), !hasChecked, true);
			});
		});
		$('#btn-attr-clean-noise').on('click', function() {
			let cleanedCount = 0;
			$('.attr-info-content .inline-attr-val:not(:disabled)').each(function() {
				const $input = $(this);
				const fromVal = String($input.attr('data-from-val') || '');
				const oldVal = $.trim($input.val());
				const newVal = _this.cleanAttrText(oldVal);
				if (newVal && newVal !== oldVal) {
					const extObj = _this.parseExt($input.closest('.attr-value').attr('data-ext')) || {};
					_this.mapAttrValue(fromVal, newVal, extObj, false);
					cleanedCount++;
				}
			});
			if (cleanedCount === 0) {
				errorTips('当前属性值已非常干净，无需去噪');
			}
		});
		$('#btn-attr-map-raw').on('click', function() {
			$('.attr-info-content .attr-map-row:not(.attr-group-disabled)').each(function() {
				const $row = $(this);
				$row.find('.attr-name').each(function() {
					const rawName = String($(this).attr('data-name') || '');
					const curName = $.trim($row.find('.inline-attr-name').val()) || rawName;
					if (rawName) {
						_this.mapAttrName(rawName, curName, false);
					}
				});
				$row.find('.attr-value').each(function() {
					const rawVal = String($(this).attr('data-name') || '');
					const curVal = $.trim($(this).find('.inline-attr-val').val()) || _this.cleanAttrText(rawVal) || rawVal;
					const extObj = _this.parseExt($(this).attr('data-ext')) || {};
					if (rawVal) {
						_this.mapAttrValue(rawVal, curVal, extObj, false);
					}
				});
			});
		});
		$('#btn-attr-sync-img').on('click', function() {
			let synced = 0;
			$('.attr-info-content .attr-value').each(function() {
				const img = String($(this).attr('data-img') || '');
				const fromVal = String($(this).attr('data-name') || '');
				if (img && fromVal) {
					$('#sku-list .sku-attr-group').filter(function() {
						return $(this).attr('data-from-val') === fromVal;
					}).each(function() {
						const $skuCard = $(this).closest('.sku-row');
						const $thumb = $skuCard.find('.sku-op-thumb');
						if ($thumb.find('img').length > 0) {
							$thumb.find('img').attr('src', img);
						} else {
							$thumb.find('i').remove();
							$thumb.prepend('<img src="' + _this.escapeHtml(img) + '" alt="SKU">');
						}
						$skuCard.find('input.img').val(img);
						synced++;
					});
				}
			});
			if (synced === 0) {
				errorTips('未检测到可同步的属性图片');
			}
		});

		// SKU列表内单个属性入库开关
		$('#sku-list').on('change', '.sku-single-attr-check', function() {
			const checked = $(this).prop('checked');
			const $group = $(this).closest('.sku-attr-group');
			const fromName = String($group.attr('data-from-name') || '');
			$group.toggleClass('sku-attr-ignored', !checked);
			$group.find('.sku-attr-input').prop('disabled', !checked);

			if (fromName) {
				const anyChecked = $('#sku-list .sku-attr-group').filter(function() {
					return $(this).attr('data-from-name') === fromName;
				}).find('.sku-single-attr-check:checked').length > 0;
				const $rightRow = $('.attr-info-content .attr-map-row').filter(function() {
					return $(this).attr('data-attr-group') === fromName;
				});
				$rightRow.find('.attr-group-check').prop('checked', anyChecked);
				$rightRow.toggleClass('attr-group-disabled', !anyChecked);
				$rightRow.find('.inline-attr-name, .inline-attr-val, .inline-attr-ext').prop('disabled', !anyChecked);
			}
			_this.updateSkuStats();
		});

		// SKU行勾选 / 全选 / 反选 / 有库存 / 隐藏未选
		$('#sku-list').on('change', '.sku-row-check', function() {
			const checked = $(this).prop('checked');
			$(this).closest('.sku-row').toggleClass('sku-row-disabled', !checked);
			_this.updateSkuStats();
		});
		$('#btn-sku-select-all').on('click', function() {
			$('#sku-list .sku-row-check').prop('checked', true).each(function() {
				$(this).closest('.sku-row').removeClass('sku-row-disabled');
			});
			_this.updateSkuStats();
		});
		$('#btn-sku-select-invert').on('click', function() {
			$('#sku-list .sku-row-check').each(function() {
				const next = !$(this).prop('checked');
				$(this).prop('checked', next);
				$(this).closest('.sku-row').toggleClass('sku-row-disabled', !next);
			});
			_this.updateSkuStats();
		});
		$('#btn-sku-select-instock').on('click', function() {
			$('#sku-list .sku-row').each(function() {
				const stock = parseInt($(this).find('input.stock').val() || '0', 10);
				const price = parseFloat($(this).find('input.price').val() || '0');
				const keep = stock > 0 && price > 0;
				$(this).find('.sku-row-check').prop('checked', keep);
				$(this).toggleClass('sku-row-disabled', !keep);
			});
			_this.updateSkuStats();
		});
		$('#btn-sku-toggle-unchecked').on('click', function() {
			const $list = $('#sku-list');
			$list.toggleClass('hide-unchecked-skus');
			const isHidden = $list.hasClass('hide-unchecked-skus');
			$(this).toggleClass('active btn-primary', isHidden).text(isHidden ? '显示全部' : '隐藏未选');
		});

		// 使用原值映射
		$('.map-modal').on('click', '.btn-raw-save', function() {
			const $form = $(this).closest('.map-modal');
			const fromName = $form.find('[name="from_name"]').val();
			$form.find('[name="name"]').val(fromName);
			$form.find('.btn-tmp-save').trigger('click');
		});

		// 保存属性映射到数据库
		$('.map-modal').on('click', '.btn-save', function() {
			const $btn = $(this);
			const $form = $btn.closest('.map-modal');
			const $box = $btn.closest('.map-modal-box');
			const fromName = $form.find('[name="from_name"]').val();
			const name = $.trim($form.find('[name="name"]').val());
			if (!name) {
				errorTips('请输入映射值');
				return false;
			}
			$btn.button('loading');
			post(_this.url, $form.serializeArray(), function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					const type = $form.find('[name="type"]').val();
					const finalName = (res.data && res.data.to_name) ? res.data.to_name : name.toUpperCase();
					if (type == '1') {
						_this.mapAttrName(fromName, finalName);
					} else {
						const nameExt = _this.collectModalExt($form, true);
						_this.mapAttrValue(fromName, finalName, nameExt);
					}
					$box.dealboxHide();
				}
			});
		});

		// 新增额外属性行
		$('.map-modal').on('click', '.btn-add', function() {
			$(this).closest('.map-modal').find('.add-map-content').append(_this.buildExtItemHtml('', ''));
		});

		// 临时映射
		$('.map-modal').on('click', '.btn-tmp-save', function() {
			const $form = $(this).closest('.map-modal');
			const $box = $(this).closest('.map-modal-box');
			const type = $form.find('[name="type"]').val();
			const fromName = $form.find('[name="from_name"]').val();
			const name = $.trim($form.find('[name="name"]').val());
			if (!name) {
				errorTips('请输入映射值');
				return false;
			}
			if (type == '1') {
				_this.mapAttrName(fromName, name);
			} else {
				const nameExt = _this.collectModalExt($form, false);
				_this.mapAttrValue(fromName, name, nameExt);
			}
			$box.dealboxHide();
		});

		// 打开分类弹窗
		$('.change-category-btn').on('click', function() {
			$('.change-category-modal').dealboxShow();
		});

		// 一级品类切换联动
		$('.change-category-modal [name="root_category_id"]').on('change', function() {
			_this.initCate($(this).val());
		});

		// 保存修改分类
		$('.change-category-modal .btn-save').on('click', function() {
			const $modal = $(this).closest('.change-category-modal');
			const cid = $modal.find('[name="category_id"]').val();
			if (!cid || cid == '0') {
				errorTips('请选择分类');
				return false;
			}
			const cateHtml = _this.pCate(cid, '');
			$('#add-product-page .category-name').html(cateHtml);
			$('#add-product-page [name="cate_id"]').val(cid);
			$modal.dealboxHide();
		});

		// 媒体标签页切换
		$('.hy-sl-list-inline li').on('click', function() {
			if ($(this).hasClass('hy-sl-selected')) {
				return false;
			}
			$(this).addClass('hy-sl-selected').siblings().removeClass('hy-sl-selected');
			$('.pic-wrap[data-id="' + $(this).data('id') + '"]').show().siblings().hide();
		});

		// 批量修改弹窗 (支持点击触发)
		$('.batch-btn').on('click', function() {
			const type = $(this).data('type');
			let fieldHtml = '';
			let name = '';
			if (type === 'unit') {
				name = '单位';
				fieldHtml = '<select name="unit" class="form-control">' +
					'<option value="0">--</option>' +
					'<option value="1">件</option>' +
					'<option value="2">个</option>' +
					'<option value="3">套</option>' +
					'<option value="4">打</option>' +
					'<option value="5">箱</option>' +
					'</select>';
			} else {
				const labelMap = {
					price: '采购价(元)',
					stock: '库存',
					weight: '重量(g)',
					length: '长度(cm)',
					width: '宽度(cm)',
					hight: '高度(cm)'
				};
				name = labelMap[type] || type;
				fieldHtml = '<input class="form-control" type="text" name="' + type + '" value="" placeholder="请输入' + name + '">';
			}
			const html = '<div class="input-group">' +
				'<span class="input-group-addon"><span>' + name + '</span></span>' +
				fieldHtml +
				'</div>' +
				'<div class="checkbox mt10" style="margin-bottom:0;">' +
				'<label><input type="checkbox" name="only_checked" checked> 仅对已勾选的SKU生效</label>' +
				'</div>';
			const $modal = $('.batch-edit-modal');
			$modal.find('[name="type"]').val(type);
			$modal.find('.content').html(html);
			$modal.dealboxShow('批量设置' + name);
		});

		// 确认批量赋值
		$('.batch-edit-modal').on('click', '.btn-save', function() {
			const $modal = $(this).closest('.batch-edit-modal');
			const type = $modal.find('[name="type"]').val();
			const value = $modal.find('[name="' + type + '"]').val();
			const onlyChecked = $modal.find('[name="only_checked"]').prop('checked');
			const $rows = onlyChecked ? $('#sku-list .sku-row').filter(function() {
				return $(this).find('.sku-row-check').prop('checked');
			}) : $('#sku-list .sku-row');
			$rows.find('.' + type).val(value);
			$modal.dealboxHide();
		});

		// 描述属性勾选 / 全选 / 反选 / 过滤冗余 / 删除 / 添加
		$('.desc-info-content').on('change', '.desc-item-check', function() {
			const checked = $(this).prop('checked');
			const $item = $(this).closest('.item');
			$item.toggleClass('desc-item-disabled', !checked);
			$item.find('input[type="text"]').prop('disabled', !checked);
			_this.updateDescCount();
		});
		$('.btn-clean-junk-desc').on('click', function() {
			const $content = $('.desc-info-content .content');
			$('.desc-info-content .content .item[data-junk="1"]').each(function() {
				$(this).addClass('desc-item-disabled');
				$(this).find('.desc-item-check').prop('checked', false);
				$(this).find('input[type="text"]').prop('disabled', true);
			});
			$content.toggleClass('hide-junk-desc');
			const isHidden = $content.hasClass('hide-junk-desc');
			$(this).text(isHidden ? '显示冗余' : '过滤冗余');
			_this.updateDescCount();
		});
		$('.btn-check-all-desc').on('click', function() {
			$('.desc-info-content .content').removeClass('hide-junk-desc');
			$('.btn-clean-junk-desc').text('过滤冗余');
			$('.desc-info-content .content .item').each(function() {
				$(this).removeClass('desc-item-disabled');
				$(this).find('.desc-item-check').prop('checked', true);
				$(this).find('input[type="text"]').prop('disabled', false);
			});
			_this.updateDescCount();
		});
		$('.btn-invert-desc').on('click', function() {
			$('.desc-info-content .content .item').each(function() {
				const next = !$(this).find('.desc-item-check').prop('checked');
				$(this).toggleClass('desc-item-disabled', !next);
				$(this).find('.desc-item-check').prop('checked', next);
				$(this).find('input[type="text"]').prop('disabled', !next);
			});
			_this.updateDescCount();
		});
		$('.desc-info-content').on('click', '.remove-desc-row', function() {
			$(this).closest('.item').remove();
			_this.updateDescCount();
		});
		$('.btn-add-desc').on('click', function() {
			const html = '<div class="item desc-pair-row">' +
				'<input type="checkbox" class="desc-item-check" checked>' +
				'<input type="text" class="form-control" name="desc[name][]" value="" placeholder="属性名">' +
				'<span class="pair-sep">:</span>' +
				'<input type="text" class="form-control" name="desc[value][]" value="" placeholder="属性值">' +
				'<button type="button" class="btn btn-danger btn-xs icon-btn remove-desc-row" title="移除"><i class="fa fa-trash-can"></i></button>' +
				'</div>';
			$('.desc-info-content .content').append(html);
			_this.updateDescCount();
		});

		// 设为SPU主图
		$('.set-spu-cover').on('click', function() {
			const $item = $(this).closest('.item');
			if ($item.find('.spu-sign').length > 0) {
				return false;
			}
			$('.spu-image .spu-sign').remove();
			$item.find('.image-left-tips').append('<div class="spu-sign">SPU</div>');
			const imgSrc = $item.find('img').attr('src') || $item.find('input[type="checkbox"]').val() || '';
			$('#add-product-page [name="spu_image"]').val(imgSrc);
		});

		// 打开SKU配图弹窗
		$('.set-sku-cover').on('click', function() {
			const $item = $(this).closest('.item');
			const img = $item.find('.pic-thumb img').attr('src') || $item.find('input[type="checkbox"]').val() || '';
			const $modal = $('.sku-modal');
			$modal.data('img', img);
			$modal.find('input[type="checkbox"]').prop('checked', false);
			$modal.dealboxShow();
		});

		// 保存SKU配图
		$('.sku-modal .btn-save').on('click', function() {
			const $modal = $(this).closest('.sku-modal');
			const img = $modal.data('img');
			$modal.find('input[type="checkbox"]:checked').each(function() {
				const skuKey = String($(this).data('sku') || '');
				const $skuCard = $('#sku-list .sku-row').filter(function() {
					return $(this).attr('data-sku') === skuKey;
				});
				const $thumb = $skuCard.find('.sku-op-thumb');
				if (img && img.indexOf('image/common/') === -1) {
					if ($thumb.find('img').length > 0) {
						$thumb.find('img').attr('src', img);
					} else {
						$thumb.find('i').remove();
						$thumb.prepend('<img src="' + img + '" alt="SKU">');
					}
				}
				$skuCard.find('input.img').val(img);
			});
			$modal.dealboxHide();
		});

		// 流水线提交（保存配置 / 保存并下一条 / 直通入库）
		$('.confirm-btn').on('click', function() {
			const $btn = $(this);
			const mode = String($btn.data('mode') || 'save');
			const $form = $('#operate-form');
			$('#submit-mode-input').val(mode);

			if ($form.find('[name="cate_id"]').val() == '0' || !$form.find('[name="cate_id"]').val()) {
				errorTips('请选择分类');
				return false;
			}
			if ($.trim($form.find('[name="spu_name"]').val()) === '') {
				errorTips('请设置产品标题');
				return false;
			}
			if ($('#sku-list .sku-row-check:checked').length === 0) {
				errorTips('请至少勾选一个要入库的SKU');
				return false;
			}
			$('.confirm-btn').prop('disabled', true);
			$btn.button('loading');
			post(_this.url, $form.serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					setTimeout(function() {
						$btn.button('reset');
						$('.confirm-btn').prop('disabled', false);
						if ((mode === 'save_next' || mode === 'direct') && res.data && res.data.next_url) {
							window.location.href = res.data.next_url;
						} else {
							window.history.back();
						}
					}, 250);
				} else {
					$btn.button('reset');
					$('.confirm-btn').prop('disabled', false);
				}
			});
		});

		// 按规则生成属性映射
		$('.attr-rule-btn').on('click', function() {
			const targetGroup = $('.attr-rule [name="attr_rule_target"]').val();
			const rawRule = $('.attr-rule [name="attr_rule_value"]').val();
			if (!rawRule) {
				errorTips('请输入分隔符或固定字数');
				$('.attr-rule [name="attr_rule_value"]').focus();
				return false;
			}
			const nameList = [];
			$('.attr-rule-name [name="attr_rule_name[]"]').each(function() {
				const v = $.trim($(this).val());
				if (v) {
					nameList.push(v);
				}
			});
			if (nameList.length === 0) {
				errorTips('请至少填写一个目标属性名');
				return false;
			}

			let $rows = $('.attr-info-content .attr-map-row');
			if (targetGroup) {
				$rows = $rows.filter(function() {
					return $(this).attr('data-attr-group') === targetGroup;
				});
			}

			const isNumericStep = /^\d+$/.test($.trim(rawRule)) && parseInt($.trim(rawRule), 10) > 0;
			const stepLen = isNumericStep ? parseInt($.trim(rawRule), 10) : 0;

			$rows.each(function() {
				const $row = $(this);
				const groupFromName = String($row.attr('data-attr-group') || '');
				_this.mapAttrName(groupFromName, nameList[0], false);

				$row.find('.attr-value').each(function() {
					const fromVal = String($(this).attr('data-name') || '');
					let parts = [];
					if (isNumericStep) {
						for (let i = 0; i < fromVal.length; i += stepLen) {
							parts.push($.trim(fromVal.substr(i, stepLen)));
						}
					} else if (rawRule === '空格' || rawRule === ' ') {
						parts = fromVal.split(/\s+/);
					} else {
						parts = fromVal.split(rawRule);
					}
					parts = parts.map(function(item) {
						return $.trim(item);
					}).filter(function(item) {
						return item !== '';
					});

					if (parts.length > 0) {
						const mainVal = parts[0];
						const nameExt = {};
						for (let idx = 1; idx < nameList.length; idx++) {
							if (parts[idx] !== undefined && parts[idx] !== '') {
								nameExt[nameList[idx]] = parts[idx];
							}
						}
						_this.mapAttrValue(fromVal, mainVal, nameExt, false);
					}
				});
			});
		});
	},

	cleanAttrText: function(text) {
		text = $.trim(String(text || ''));
		if (!text) {
			return '';
		}
		let cleaned = text.replace(/【[^】]*(现货|速发|包邮|发|折|起|特价|清仓|代发|收藏|加购|送|赠|拍|联系|客服|定制)[^】]*】/gu, '');
		cleaned = cleaned.replace(/\[[^\]]*(现货|速发|包邮|发|折|起|特价|清仓|代发|收藏|加购|送|赠|拍|联系|客服|定制)[^\]]*\]/gu, '');
		cleaned = cleaned.replace(/（[^）]*(现货|速发|包邮|发|折|起|特价|清仓|代发|收藏|加购|送|赠|拍|联系|客服|定制)[^）]*）/gu, '');
		cleaned = cleaned.replace(/\([^)]*(现货|速发|包邮|发|折|起|特价|清仓|代发|收藏|加购|送|赠|拍|联系|客服|定制)[^)]*\)/gu, '');
		cleaned = $.trim(cleaned);
		return cleaned !== '' ? cleaned : text;
	},

	escapeHtml: function(str) {
		return String(str == null ? '' : str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	},

	parseExt: function(extStr) {
		if (!extStr) {
			return null;
		}
		if (typeof extStr === 'object') {
			return extStr;
		}
		try {
			const parsed = JSON.parse(extStr);
			return (parsed && typeof parsed === 'object') ? parsed : null;
		} catch (e) {
			return null;
		}
	},

	buildExtItemHtml: function(k, v) {
		return '<div class="item">' +
			'<input type="text" class="form-control" name="attr[name][]" value="' + this.escapeHtml(k) + '" placeholder="衍生属性名">' +
			'<span>:</span>' +
			'<input type="text" class="form-control" name="attr[value][]" value="' + this.escapeHtml(v) + '" placeholder="衍生属性值">' +
			'</div>';
	},

	collectModalExt: function($form, toUpper) {
		const nameExt = {};
		$form.find('.add-map-content .item').each(function() {
			let tmpName = $.trim($(this).find('input').eq(0).val());
			let tmpValue = $.trim($(this).find('input').eq(1).val());
			if (tmpName && tmpValue) {
				if (toUpper) {
					tmpName = tmpName.toUpperCase();
					tmpValue = tmpValue.toUpperCase();
				}
				nameExt[tmpName] = tmpValue;
			}
		});
		return nameExt;
	},

	toggleAttrGroup: function(groupName, checked, syncValues) {
		const $row = $('.attr-info-content .attr-map-row').filter(function() {
			return $(this).attr('data-attr-group') === groupName;
		});
		$row.find('.attr-group-check').prop('checked', checked);
		$row.toggleClass('attr-group-disabled', !checked);
		$row.find('.inline-attr-name').prop('disabled', !checked);
		if (syncValues) {
			$row.find('.attr-val-check').prop('checked', checked).each(function() {
				const $v = $(this).closest('.attr-value');
				$v.toggleClass('val-unchecked', !checked);
				$v.find('.inline-attr-val, .inline-attr-ext').prop('disabled', !checked);
			});
		} else {
			$row.find('.attr-value').each(function() {
				const valChecked = checked && $(this).find('.attr-val-check').prop('checked');
				$(this).find('.inline-attr-val, .inline-attr-ext').prop('disabled', !valChecked);
			});
		}

		$('#sku-list .sku-attr-group').filter(function() {
			return $(this).attr('data-from-name') === groupName;
		}).each(function() {
			const $group = $(this);
			$group.toggleClass('sku-attr-ignored', !checked);
			$group.find('.sku-single-attr-check').prop('checked', checked);
			$group.find('.sku-attr-input').prop('disabled', !checked);
		});

		this.updateSkuStats();
	},

	syncSkuRowsByAttrValues: function() {
		const activeMultiGroups = {};
		$('.attr-info-content .attr-map-row').each(function() {
			const $row = $(this);
			const valCount = parseInt($row.attr('data-val-count') || '0', 10);
			const groupChecked = $row.find('.attr-group-check').prop('checked');
			if (valCount > 1 && groupChecked) {
				const groupName = String($row.attr('data-attr-group') || '');
				const valMap = {};
				$row.find('.attr-value').each(function() {
					const vName = String($(this).attr('data-name') || '');
					valMap[vName] = $(this).find('.attr-val-check').prop('checked');
				});
				activeMultiGroups[groupName] = valMap;
			}
		});

		$('#sku-list .sku-row').each(function() {
			const $row = $(this);
			let rowMatch = true;
			$row.find('.sku-attr-group').each(function() {
				const fromName = String($(this).attr('data-from-name') || '');
				const fromVal = String($(this).attr('data-from-val') || '');
				if (Object.prototype.hasOwnProperty.call(activeMultiGroups, fromName)) {
					if (!activeMultiGroups[fromName][fromVal]) {
						rowMatch = false;
						return false;
					}
				}
			});
			$row.find('.sku-row-check').prop('checked', rowMatch);
			$row.toggleClass('sku-row-disabled', !rowMatch);
		});

		this.updateSkuStats();
	},

	initCate: function(pid) {
		let html = '';
		if (!pid || pid == '0') {
			html = '<option value="0">请先选择品类</option>';
		} else {
			let status = false;
			for (let i = 0; i < category.length; i++) {
				if (status && category[i].parent_id == '0') {
					break;
				}
				if (pid == category[i].cate_id) {
					status = true;
				}
				if (status && category[i].parent_id != '0') {
					let disable = '';
					if (category[i + 1] && category[i + 1].parent_id == category[i].cate_id) {
						disable = 'disabled';
					}
					html += '<option value="' + category[i].cate_id + '" ' + disable + '>' + '　'.repeat(category[i].level) + category[i].name + '</option>';
				}
			}
		}
		$('.change-category-modal [name="category_id"]').html(html);
	},

	pCate: function(cid, html) {
		for (let i = 0; i < category.length; i++) {
			if (cid == category[i].cate_id) {
				html = category[i].name + (html ? ' - ' + html : html);
				if (category[i].parent_id == '0') {
					return html;
				} else {
					return this.pCate(category[i].parent_id, html);
				}
			}
		}
		return html;
	},

	mapAttrName: function(fromName, toName, skipInputUpdate) {
		const _this = this;
		const $rightAttr = $('.attr-info-content .attr-name').filter(function() {
			return $(this).attr('data-name') === fromName;
		});
		$rightAttr.removeClass('error').addClass('success').attr('title', toName);
		if (!skipInputUpdate) {
			$rightAttr.closest('.attr-map-row').find('.inline-attr-name').val(toName);
		}

		$('#sku-list .sku-attr-group').filter(function() {
			return $(this).attr('data-from-name') === fromName;
		}).each(function() {
			const $group = $(this);
			const skuId = $group.closest('.sku-row').attr('data-sku');
			const $main = $group.find('.sku-attr-main');
			$main.find('.attr-name').removeClass('error').addClass('success').text(toName);
			$main.find('.sku-attr-input').attr('name', 'sku[' + skuId + '][attr][' + toName + ']');
		});

		this.updateSkuStats();
	},

	mapAttrValue: function(fromVal, mainVal, nameExt, skipInputUpdate) {
		const _this = this;
		nameExt = nameExt || {};

		$('#sku-list .sku-attr-group').filter(function() {
			return $(this).attr('data-from-val') === fromVal;
		}).each(function() {
			const $group = $(this);
			const isIgnored = $group.hasClass('sku-attr-ignored');
			const disabledAttr = isIgnored ? ' disabled' : '';
			const skuId = $group.closest('.sku-row').attr('data-sku');
			const $main = $group.find('.sku-attr-main');
			const currentAttrName = $main.find('.attr-name').text().trim();

			$main.find('.attr-value').removeClass('error').addClass('success').text(mainVal);
			$main.find('.sku-attr-input')
				.attr('name', 'sku[' + skuId + '][attr][' + currentAttrName + ']')
				.val(mainVal);

			$group.find('.attr-map').remove();
			let extHtml = '';
			for (const k in nameExt) {
				if (Object.prototype.hasOwnProperty.call(nameExt, k) && k !== currentAttrName) {
					const v = nameExt[k];
					extHtml += '<p class="attr-map mapped-line">' +
						'<span class="attr-name success" data-name="' + _this.escapeHtml(k) + '">' + _this.escapeHtml(k) + '</span>' +
						'<span>: </span>' +
						'<span class="attr-value success" data-name="' + _this.escapeHtml(v) + '">' + _this.escapeHtml(v) + '</span>' +
						'<input type="hidden" class="sku-attr-input" name="sku[' + _this.escapeHtml(skuId) + '][attr][' + _this.escapeHtml(k) + ']" value="' + _this.escapeHtml(v) + '"' + disabledAttr + '>' +
						'</p>';
				}
			}
			if (extHtml) {
				$group.append(extHtml);
			}
		});

		let titleParts = [mainVal];
		let extTagsHtml = '';
		for (const k in nameExt) {
			if (Object.prototype.hasOwnProperty.call(nameExt, k)) {
				titleParts.push(k + ':' + nameExt[k]);
				extTagsHtml += '<em class="ext-tag">+' + _this.escapeHtml(k) + ':' + _this.escapeHtml(nameExt[k]) + '</em>';
			}
		}
		const extJsonStr = Object.keys(nameExt).length > 0 ? JSON.stringify(nameExt) : '';
		const $rightVal = $('.attr-info-content .attr-value').filter(function() {
			return $(this).attr('data-name') === fromVal;
		});
		$rightVal
			.removeClass('error')
			.addClass('success')
			.attr('data-mapped', mainVal)
			.attr('title', titleParts.join(' ; '))
			.attr('data-ext', extJsonStr);
		if (!skipInputUpdate) {
			$rightVal.find('.inline-attr-val').val(mainVal);
		}
		$rightVal.find('.inline-attr-ext').val(extJsonStr);
		$rightVal.find('.ext-tag-wrap').html(extTagsHtml);

		this.updateSkuStats();
	},

	updateSkuStats: function() {
		let totalCount = 0;
		let checkedCount = 0;
		$('#sku-list .sku-row').each(function() {
			totalCount++;
			const $row = $(this);
			if ($row.find('.sku-row-check').prop('checked')) {
				checkedCount++;
			}
			const hasError = $row.find('.sku-attr-group:not(.sku-attr-ignored) .error').length > 0;
			$row.find('.sku-op-thumb').toggleClass('check', !hasError);
		});
		$('#total-sku-count').text(totalCount);
		$('#checked-sku-count').text(checkedCount);
	},

	updateDescCount: function() {
		const total = $('.desc-info-content .content .item').length;
		const checked = $('.desc-info-content .content .item .desc-item-check:checked').length;
		$('#desc-count').text(total);
		$('#desc-checked-count').text(checked);
	}
};
