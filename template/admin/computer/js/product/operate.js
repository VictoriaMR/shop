const OPERATE = {
	init: function() {
		const _this = this;
		this.updateSkuStats();
		this.updateDescCount();

		// 行内直接编辑属性名：实时同步到左侧SKU列表
		$('.attr-info-content').on('input change', '.inline-attr-name', function() {
			const $input = $(this);
			const fromName = String($input.attr('data-from-name') || '');
			const toName = $.trim($input.val()) || fromName;
			if (fromName) {
				_this.mapAttrName(fromName, toName, true);
			}
		});

		// 行内直接编辑属性值：实时同步到左侧SKU列表
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

		// 行内输入框回车自动跳到下一个输入框
		$('.attr-info-content').on('keydown', '.inline-attr-name, .inline-attr-val', function(e) {
			if (e.key === 'Enter' || e.keyCode === 13) {
				e.preventDefault();
				const $inputs = $('.attr-info-content').find('.inline-attr-name:visible:not(:disabled), .inline-attr-val:visible:not(:disabled)');
				const idx = $inputs.index(this);
				if (idx >= 0 && idx + 1 < $inputs.length) {
					$inputs.eq(idx + 1).focus().select();
				} else {
					$(this).blur();
				}
			}
		});

		// 点击属性名/属性值或"+"按钮进行高级衍生属性映射弹窗
		$('.attr-info-content').on('click', '.attr-item', function(e) {
			if ($(e.target).hasClass('bigImage') || $(e.target).hasClass('attr-val-check') || $(e.target).is('input')) {
				return;
			}
			const $item = $(this);
			const type = $item.hasClass('attr-name') ? '1' : '2';
			const fromName = String($item.attr('data-name') || '');
			const $tr = $item.closest('tr');
			const $rowAttrName = $tr.find('.attr-name');
			const rawAttrName = String($rowAttrName.attr('data-name') || '');

			// 获取当前属性名已映射的名称
			let mappedAttrName = $.trim($tr.find('.inline-attr-name').val()) || rawAttrName;

			let toName = '';
			if (type === '1') {
				toName = mappedAttrName || fromName;
			} else {
				toName = $.trim($item.find('.inline-attr-val').val()) || $item.attr('data-mapped') || fromName;
			}

			let extHtml = '';
			if (type === '2') {
				let extObj = _this.parseExt($item.attr('data-ext'));
				let setValue = true;
				if (!extObj) {
					setValue = false;
					const $siblingSuccess = $item.closest('td').find('.attr-value.success').first();
					if ($siblingSuccess.length) {
						extObj = _this.parseExt($siblingSuccess.attr('data-ext'));
					}
				}
				if (extObj) {
					for (let k in extObj) {
						if (Object.prototype.hasOwnProperty.call(extObj, k) && k !== mappedAttrName) {
							extHtml += _this.buildExtItemHtml(k, setValue ? extObj[k] : '');
						}
					}
				}
			}

			const addonTitle = type === '1' ? '属性名' : _this.escapeHtml(mappedAttrName);
			let html = `<div class="input-group">
							<div class="input-group-addon"><span>` + addonTitle + `</span></div>
							<input class="form-control" type="text" name="name" placeholder="请输入映射值" value="` + _this.escapeHtml(toName) + `"/>
						</div>
						<div class="add-map-content">` + extHtml + `</div>
						<div class="map-modal-footer mt20">`;
			if (type === '2') {
				html += `<button type="button" class="btn btn-info btn-sm btn-add">新增衍生属性</button>`;
			}
			html += `<button type="button" class="btn btn-default btn-sm btn-raw-save" title="直接使用原名称作为映射">使用原值</button>
					 <button type="button" class="btn btn-success btn-sm btn-tmp-save">确认应用</button>
					 <button type="button" class="btn btn-primary btn-sm btn-save">立即写入字典</button>
					</div>`;

			const obj = $('.map-modal');
			obj.find('[name="from_name"]').val(fromName);
			obj.find('[name="type"]').val(type);
			obj.find('.content').html(html);
			obj.dealboxShow('属性映射与衍生拆分: ' + fromName);
			setTimeout(function() {
				obj.find('[name="name"]').focus().select();
			}, 50);
		});

		// 属性维度入库勾选 / 取消
		$('.attr-info-content').on('change', '.attr-group-check', function() {
			const checked = $(this).prop('checked');
			const $tr = $(this).closest('tr[data-attr-group]');
			const groupName = String($tr.attr('data-attr-group') || '');
			_this.toggleAttrGroup(groupName, checked, true);
		});

		// 属性值勾选 / 取消
		$('.attr-info-content').on('change', '.attr-val-check', function(e) {
			e.stopPropagation();
			const checked = $(this).prop('checked');
			const $valItem = $(this).closest('.attr-value');
			const $tr = $(this).closest('tr[data-attr-group]');
			const groupName = String($tr.attr('data-attr-group') || '');
			const valCount = parseInt($tr.attr('data-val-count') || '0', 10);

			$valItem.toggleClass('val-unchecked', !checked);
			$valItem.find('.inline-attr-val, .inline-attr-ext').prop('disabled', !checked);

			// 若该属性组只有单个属性值（全部SKU共用单属性），直接切换该属性维度是否入库，不影响SKU勾选
			if (valCount <= 1) {
				_this.toggleAttrGroup(groupName, checked, true);
				return;
			}

			// 多属性值维度：若勾选了某个属性值且当前维度未启用，则自动启用该维度
			if (checked && !$tr.find('.attr-group-check').prop('checked')) {
				_this.toggleAttrGroup(groupName, true, false);
			}
			_this.syncSkuRowsByAttrValues();
		});

		// 单个属性组内：全选值 / 反选值
		$('.attr-info-content').on('click', '.attr-val-select-all', function() {
			const $tr = $(this).closest('tr[data-attr-group]');
			const groupName = String($tr.attr('data-attr-group') || '');
			if (!$tr.find('.attr-group-check').prop('checked')) {
				_this.toggleAttrGroup(groupName, true, false);
			}
			$tr.find('.attr-val-check').prop('checked', true).each(function() {
				const $v = $(this).closest('.attr-value');
				$v.removeClass('val-unchecked');
				$v.find('.inline-attr-val, .inline-attr-ext').prop('disabled', false);
			});
			_this.syncSkuRowsByAttrValues();
		});
		$('.attr-info-content').on('click', '.attr-val-select-invert', function() {
			const $tr = $(this).closest('tr[data-attr-group]');
			const groupName = String($tr.attr('data-attr-group') || '');
			if (!$tr.find('.attr-group-check').prop('checked')) {
				_this.toggleAttrGroup(groupName, true, false);
			}
			$tr.find('.attr-val-check').each(function() {
				const next = !$(this).prop('checked');
				$(this).prop('checked', next);
				const $v = $(this).closest('.attr-value');
				$v.toggleClass('val-unchecked', !next);
				$v.find('.inline-attr-val, .inline-attr-ext').prop('disabled', !next);
			});
			_this.syncSkuRowsByAttrValues();
		});

		// 属性快捷工具栏：全选 / 反选 / 忽略单值 / 一键去噪 / 一键原值 / 同步属性图
		$('#btn-attr-group-all').on('click', function() {
			$('.attr-info-content tr[data-attr-group]').each(function() {
				const groupName = String($(this).attr('data-attr-group') || '');
				_this.toggleAttrGroup(groupName, true, true);
			});
		});
		$('#btn-attr-group-invert').on('click', function() {
			$('.attr-info-content tr[data-attr-group]').each(function() {
				const groupName = String($(this).attr('data-attr-group') || '');
				const next = !$(this).find('.attr-group-check').prop('checked');
				_this.toggleAttrGroup(groupName, next, true);
			});
		});
		$('#btn-attr-ignore-single').on('click', function() {
			const $singleRows = $('.attr-info-content tr[data-attr-group]').filter(function() {
				return parseInt($(this).attr('data-val-count') || '0', 10) <= 1;
			});
			if ($singleRows.length === 0) {
				errorTips('当前没有单值属性维度');
				return;
			}
			const hasChecked = $singleRows.find('.attr-group-check:checked').length > 0;
			$singleRows.each(function() {
				const groupName = String($(this).attr('data-attr-group') || '');
				_this.toggleAttrGroup(groupName, !hasChecked, true);
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
			$('.attr-info-content tr[data-attr-group]:not(.attr-group-disabled)').each(function() {
				const $tr = $(this);
				$tr.find('.attr-name').each(function() {
					const rawName = String($(this).attr('data-name') || '');
					const curName = $.trim($tr.find('.inline-attr-name').val()) || rawName;
					if (rawName) {
						_this.mapAttrName(rawName, curName, false);
					}
				});
				$tr.find('.attr-value').each(function() {
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
						const $tr = $(this).closest('tr');
						$tr.find('.sku-img-cell img').attr('src', img).attr('data-src', img);
						$tr.find('input.img').val(img);
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

			// 同步右侧属性组复选框状态（若同名属性全部取消或存在勾选）
			if (fromName) {
				const anyChecked = $('#sku-list .sku-attr-group').filter(function() {
					return $(this).attr('data-from-name') === fromName;
				}).find('.sku-single-attr-check:checked').length > 0;
				const $rightTr = $('.attr-info-content tr[data-attr-group]').filter(function() {
					return $(this).attr('data-attr-group') === fromName;
				});
				$rightTr.find('.attr-group-check').prop('checked', anyChecked);
				$rightTr.toggleClass('attr-group-disabled', !anyChecked);
				$rightTr.find('.inline-attr-name, .inline-attr-val, .inline-attr-ext').prop('disabled', !anyChecked);
			}
			_this.updateSkuStats();
		});

		// 映射弹窗回车快捷保存
		$('.map-modal').on('keydown', 'input', function(e) {
			if (e.key === 'Enter' || e.keyCode === 13) {
				e.preventDefault();
				$(this).closest('.map-modal').find('.btn-tmp-save').trigger('click');
			}
		});

		// 使用原值映射
		$('.map-modal').on('click', '.btn-raw-save', function() {
			const obj = $(this).closest('.map-modal');
			const fromName = obj.find('[name="from_name"]').val();
			obj.find('[name="name"]').val(fromName);
			obj.find('.btn-tmp-save').trigger('click');
		});

		// 保存并入库映射
		$('.map-modal').on('click', '.btn-save', function() {
			const obj = $(this).closest('.map-modal');
			const formObj = obj.find('form');
			const type = obj.find('[name="type"]').val();
			const fromName = obj.find('[name="from_name"]').val();
			const name = $.trim(obj.find('[name="name"]').val());
			if (!name) {
				errorTips('请输入映射值');
				return false;
			}
			const _thisobj = $(this);
			_thisobj.button('loading');
			post('', formObj.serializeArray(), function(res) {
				showTips(res);
				_thisobj.button('reset');
				if (res.code) {
					const finalName = (res.data && res.data.to_name) ? res.data.to_name : name.toUpperCase();
					if (type === '1') {
						_this.mapAttrName(fromName, finalName);
					} else {
						const nameExt = _this.collectModalExt(obj, true);
						_this.mapAttrValue(fromName, finalName, nameExt);
					}
					obj.dealboxHide();
				}
			});
		});

		// 新增衍生属性行
		$('.map-modal').on('click', '.btn-add', function() {
			$(this).closest('.map-modal').find('.add-map-content').append(_this.buildExtItemHtml('', ''));
		});

		// 删除衍生属性行
		$('.map-modal').on('click', '.btn-remove-ext', function() {
			$(this).closest('.item').remove();
		});

		// 临时映射（仅当前页面生效）
		$('.map-modal').on('click', '.btn-tmp-save', function() {
			const obj = $(this).closest('.map-modal');
			const type = obj.find('[name="type"]').val();
			const fromName = obj.find('[name="from_name"]').val();
			const name = $.trim(obj.find('[name="name"]').val());
			if (!name) {
				errorTips('请输入映射值');
				return false;
			}
			if (type === '1') {
				_this.mapAttrName(fromName, name);
			} else {
				const nameExt = _this.collectModalExt(obj, false);
				_this.mapAttrValue(fromName, name, nameExt);
			}
			obj.dealboxHide();
		});

		// 修改分类
		$('.change-category-btn').on('click', function() {
			$('.change-category-modal').dealboxShow();
		});

		// 品类变化加载子分类
		$('.change-category-modal [name="root_category_id"]').on('change', function() {
			_this.initCate($(this).val());
		});

		// 保存修改分类
		$('.change-category-modal .btn-save').on('click', function() {
			const obj = $(this).closest('.change-category-modal');
			const cid = obj.find('[name="category_id"]').val();
			if (!cid || cid === '0') {
				errorTips('请选择分类');
				return false;
			}
			const cateHtml = _this.pCate(cid, '');
			$('#add-product-page .category-name').html(cateHtml);
			$('#add-product-page [name="cate_id"]').val(cid);
			obj.dealboxHide();
		});

		// 图片栏目切换
		$('.hy-sl-list-inline li').on('click', function() {
			if ($(this).hasClass('hy-sl-selected')) {
				return false;
			}
			$(this).addClass('hy-sl-selected').siblings().removeClass('hy-sl-selected');
			$('.pic-wrap[data-id="' + $(this).data('id') + '"]').show().siblings().hide();
			DROP.load();
		});

		// 当前图片组全选 / 反选
		$('.img-check-all').on('click', function() {
			$('.spu-image .pic-wrap:visible input[type="checkbox"]').prop('checked', true);
		});
		$('.img-check-invert').on('click', function() {
			$('.spu-image .pic-wrap:visible input[type="checkbox"]').each(function() {
				$(this).prop('checked', !$(this).prop('checked'));
			});
		});

		// SKU全选 / 反选 / 仅选有库存
		$('#sku-check-all').on('change', function() {
			const checked = $(this).prop('checked');
			$('#sku-list .sku-row-check').prop('checked', checked).each(function() {
				$(this).closest('tr').toggleClass('sku-row-disabled', !checked);
			});
			_this.updateSkuStats();
		});
		$('#sku-list').on('change', '.sku-row-check', function() {
			const checked = $(this).prop('checked');
			$(this).closest('tr').toggleClass('sku-row-disabled', !checked);
			_this.updateSkuStats();
		});
		$('#btn-sku-select-all').on('click', function() {
			$('#sku-check-all').prop('checked', true).trigger('change');
		});
		$('#btn-sku-select-invert').on('click', function() {
			$('#sku-list .sku-row-check').each(function() {
				const nextState = !$(this).prop('checked');
				$(this).prop('checked', nextState);
				$(this).closest('tr').toggleClass('sku-row-disabled', !nextState);
			});
			_this.updateSkuStats();
		});
		$('#btn-sku-select-instock').on('click', function() {
			$('#sku-list tbody tr').each(function() {
				const stock = parseInt($(this).find('input.stock').val() || '0', 10);
				const price = parseFloat($(this).find('input.price').val() || '0');
				const keep = stock > 0 && price > 0;
				$(this).find('.sku-row-check').prop('checked', keep);
				$(this).toggleClass('sku-row-disabled', !keep);
			});
			_this.updateSkuStats();
		});
		$('#btn-sku-toggle-unchecked').on('click', function() {
			const $table = $('#sku-list');
			$table.toggleClass('hide-unchecked-skus');
			const isHidden = $table.hasClass('hide-unchecked-skus');
			$(this).toggleClass('active btn-primary', isHidden).text(isHidden ? '显示全部' : '隐藏未选');
		});

		// 表头行内极速批量填充（输入即同步至所有已勾选SKU）
		$('#sku-list').on('input change', '.batch-inline-input', function() {
			const $el = $(this);
			const field = String($el.data('field') || $el.data('type') || '');
			const val = $el.val();
			if (!field || val === '') {
				return;
			}
			$('#sku-list tbody tr').each(function() {
				if ($(this).find('.sku-row-check').prop('checked')) {
					$(this).find('.' + field).val(val);
				}
			});
		});
		$('#sku-list').on('keydown', '.batch-inline-input', function(e) {
			if (e.key === 'Enter' || e.keyCode === 13) {
				e.preventDefault();
				const $inputs = $('#sku-list .batch-inline-input');
				const idx = $inputs.index(this);
				if (idx >= 0 && idx + 1 < $inputs.length) {
					$inputs.eq(idx + 1).focus().select();
				} else {
					$(this).blur();
				}
			}
		});

		// 批量修改弹窗（支持单击触发）
		$('.batch-btn').on('click', function() {
			const type = $(this).data('type');
			let fieldHtml = '';
			let name = '';
			switch (type) {
				case 'unit':
					name = '单位';
					fieldHtml += `<select name="` + type + `" class="form-control">
								<option value="0">--</option>
								<option value="1">件</option>
								<option value="2">个</option>
								<option value="3">套</option>
								<option value="4">打</option>
								<option value="5">箱</option>
							</select>`;
					break;
				default:
					if (type === 'price') {
						name = '采购价(元)';
					} else if (type === 'stock') {
						name = '库存';
					} else if (type === 'weight') {
						name = '重量(g)';
					} else if (type === 'length') {
						name = '长度(cm)';
					} else if (type === 'width') {
						name = '宽度(cm)';
					} else if (type === 'hight') {
						name = '高度(cm)';
					}
					fieldHtml += `<input class="form-control" type="text" name="` + type + `" value="" placeholder="请输入批量设置的` + name + `"/>`;
					break;
			}
			const html = `<div class="input-group">
							<div class="input-group-addon"><span>` + name + `</span></div>
							` + fieldHtml + `
						</div>
						<div class="checkbox mt10" style="margin-bottom:0;">
							<label><input type="checkbox" name="only_checked" checked> 仅对已勾选的SKU生效</label>
						</div>`;
			const obj = $('.batch-edit-modal');
			obj.find('[name="type"]').val(type);
			obj.find('.content').html(html);
			obj.dealboxShow('批量设置 - ' + name);
			setTimeout(function() {
				obj.find('input[type="text"], select').first().focus();
			}, 50);
		});

		// 批量弹窗回车确认
		$('.batch-edit-modal').on('keydown', 'input[type="text"]', function(e) {
			if (e.key === 'Enter' || e.keyCode === 13) {
				e.preventDefault();
				$(this).closest('.batch-edit-modal').find('.btn-save').trigger('click');
			}
		});

		// 批量确认
		$('.batch-edit-modal').on('click', '.btn-save', function() {
			const obj = $(this).closest('.batch-edit-modal');
			const type = obj.find('[name="type"]').val();
			const value = obj.find('[name="' + type + '"]').val();
			const onlyChecked = obj.find('[name="only_checked"]').prop('checked');
			const $rows = onlyChecked ? $('#sku-list tbody tr').filter(function() {
				return $(this).find('.sku-row-check').prop('checked');
			}) : $('#sku-list tbody tr');
			$rows.find('.' + type).val(value);
			obj.dealboxHide();
		});

		// 描述属性勾选 / 全选 / 反选 / 过滤冗余 / 删除 / 添加 / 清空
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
		$('.desc-info-content').on('click', '.glyphicon-remove', function() {
			$(this).closest('.item').remove();
			_this.updateDescCount();
		});
		$('.btn-add-desc').on('click', function() {
			const html = `<div class="item">
							<input type="checkbox" class="desc-item-check" checked title="勾选入库该描述属性">
							<input type="text" class="form-control input-sm" name="desc[name][]" value="" placeholder="属性名">
							<span>: </span>
							<input type="text" class="form-control input-sm" name="desc[value][]" value="" placeholder="属性值">
							<i class="glyphicon glyphicon-remove" title="删除"></i>
						</div>`;
			$('.desc-info-content .content').append(html);
			_this.updateDescCount();
		});
		$('.btn-clear-desc').on('click', function() {
			$('.desc-info-content .content').empty();
			_this.updateDescCount();
		});

		// 设置SPU主图
		$('.set-spu-cover').on('click', function() {
			const pObj = $(this).closest('.item');
			if (pObj.find('.spu-sign').length > 0) {
				return false;
			}
			$('.spu-image .spu-sign').remove();
			pObj.find('.image-left-tips').append('<div class="spu-sign">SPU</div>');
			const imgEl = pObj.find('.pic-thumb img');
			const realSrc = imgEl.attr('data-src') || imgEl.attr('src') || '';
			$('#add-product-page form [name="spu_image"]').val(realSrc);
		});

		// 设置SKU主图弹窗
		$('.set-sku-cover').on('click', function() {
			const imgEl = $(this).closest('.item').find('.pic-thumb img');
			const img = imgEl.attr('data-src') || imgEl.attr('src') || '';
			const pObj = $('.sku-modal');
			pObj.data('img', img);
			pObj.find('input[type="checkbox"]').prop('checked', false);
			pObj.dealboxShow();
		});
		$('#sku-modal-all').on('click', function() {
			$('.sku-modal input[type="checkbox"]').prop('checked', true);
		});
		$('#sku-modal-invert').on('click', function() {
			$('.sku-modal input[type="checkbox"]').each(function() {
				$(this).prop('checked', !$(this).prop('checked'));
			});
		});

		// 保存SKU主图
		$('.sku-modal .btn-save').on('click', function() {
			const pObj = $(this).closest('.sku-modal');
			const img = pObj.data('img');
			pObj.find('input[type="checkbox"]:checked').each(function() {
				const skuId = String($(this).attr('data-sku') || '');
				const $tr = $('#sku-list tr').filter(function() {
					return $(this).attr('data-sku') === skuId;
				});
				$tr.find('.sku-img-cell img').attr('src', img).attr('data-src', img);
				$tr.find('input.img').val(img);
			});
			pObj.dealboxHide();
		});

		// 流水线提交（保存配置 / 保存并处理下一条 / 立即生成产品入库）
		$('.confirm-btn').on('click', function() {
			const _thisobj = $(this);
			const mode = String(_thisobj.data('mode') || 'save');
			const obj = _thisobj.closest('form');
			obj.find('[name="submit_mode"]').val(mode);

			if (obj.find('[name="cate_id"]').val() === '0' || !obj.find('[name="cate_id"]').val()) {
				errorTips('请选择分类');
				return false;
			}
			if ($.trim(obj.find('[name="spu_name"]').val()) === '') {
				errorTips('请设置产品标题');
				return false;
			}
			if ($('#sku-list .sku-row-check:checked').length === 0) {
				errorTips('请至少勾选一个要入库的SKU');
				return false;
			}
			$('.confirm-btn').prop('disabled', true);
			_thisobj.button('loading');
			post('', obj.serializeArray(), function(res) {
				showTips(res);
				if (res.code) {
					setTimeout(function() {
						_thisobj.button('reset');
						$('.confirm-btn').prop('disabled', false);
						if ((mode === 'save_next' || mode === 'direct') && res.data && res.data.next_url) {
							window.location.href = res.data.next_url;
						} else {
							window.history.back();
						}
					}, 250);
				} else {
					_thisobj.button('reset');
					$('.confirm-btn').prop('disabled', false);
				}
			});
		});

		// 全局快捷键 Ctrl + Enter：一键触发“保存并处理下一条”
		$(document).on('keydown', function(e) {
			if ((e.ctrlKey || e.metaKey) && (e.key === 'Enter' || e.keyCode === 13)) {
				e.preventDefault();
				const $nextBtn = $('.confirm-btn[data-mode="save_next"]:not(:disabled)').first();
				if ($nextBtn.length) {
					$nextBtn.trigger('click');
				}
			}
		});

		// 规则拆分一键生成
		$('.attr-rule-btn').on('click', function() {
			const targetGroup = $('.attr-rule [name="attr_rule_target"]').val();
			const rawRule = $('.attr-rule [name="attr_rule_value"]').val();
			if (!rawRule) {
				errorTips('请输入分隔符(如 | 或 空格)或固定字数');
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

			let $rows = $('.attr-info-content tr[data-attr-group]');
			if (targetGroup) {
				$rows = $rows.filter(function() {
					return $(this).attr('data-attr-group') === targetGroup;
				});
			}

			const isNumericStep = /^\d+$/.test($.trim(rawRule)) && parseInt($.trim(rawRule), 10) > 0;
			const stepLen = isNumericStep ? parseInt($.trim(rawRule), 10) : 0;

			$rows.each(function() {
				const $tr = $(this);
				const groupFromName = String($tr.attr('data-attr-group') || '');
				// 将第一顺位目标属性名同步映射为当前属性组名
				_this.mapAttrName(groupFromName, nameList[0], false);

				$tr.find('.attr-value').each(function() {
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
		return `<div class="item">
					<input type="text" class="form-control" name="attr[name][]" placeholder="衍生属性名" value="` + this.escapeHtml(k) + `" />
					<span>: </span>
					<input type="text" class="form-control" name="attr[value][]" placeholder="衍生属性值" value="` + this.escapeHtml(v) + `" />
					<i class="glyphicon glyphicon-remove btn-remove-ext" title="移除"></i>
				</div>`;
	},

	collectModalExt: function(modalObj, toUpper) {
		const nameExt = {};
		modalObj.find('.add-map-content .item').each(function() {
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

	initCate: function(pid) {
		let html = '';
		if (pid == '0') {
			html += '<option value="0">请先选择品类</option>';
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
					html += '<option value="' + category[i].cate_id + '" ' + disable + '>' + '&nbsp;&nbsp;'.repeat(category[i].level) + category[i].name + '</option>';
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
	},

	toggleAttrGroup: function(groupName, checked, syncValues) {
		const $tr = $('.attr-info-content tr[data-attr-group]').filter(function() {
			return $(this).attr('data-attr-group') === groupName;
		});
		$tr.find('.attr-group-check').prop('checked', checked);
		$tr.toggleClass('attr-group-disabled', !checked);
		$tr.find('.inline-attr-name').prop('disabled', !checked);
		if (syncValues) {
			$tr.find('.attr-val-check').prop('checked', checked).each(function() {
				const $v = $(this).closest('.attr-value');
				$v.toggleClass('val-unchecked', !checked);
				$v.find('.inline-attr-val, .inline-attr-ext').prop('disabled', !checked);
			});
		} else {
			$tr.find('.attr-value').each(function() {
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
		// 收集所有已启用且有多个值的属性维度中，各属性值的勾选状态
		const activeMultiGroups = {};
		$('.attr-info-content tr[data-attr-group]').each(function() {
			const $tr = $(this);
			const valCount = parseInt($tr.attr('data-val-count') || '0', 10);
			const groupChecked = $tr.find('.attr-group-check').prop('checked');
			if (valCount > 1 && groupChecked) {
				const groupName = String($tr.attr('data-attr-group') || '');
				const valMap = {};
				$tr.find('.attr-value').each(function() {
					const vName = String($(this).attr('data-name') || '');
					valMap[vName] = $(this).find('.attr-val-check').prop('checked');
				});
				activeMultiGroups[groupName] = valMap;
			}
		});

		$('#sku-list tbody tr').each(function() {
			const $tr = $(this);
			let rowMatch = true;
			$tr.find('.sku-attr-group').each(function() {
				const fromName = String($(this).attr('data-from-name') || '');
				const fromVal = String($(this).attr('data-from-val') || '');
				if (Object.prototype.hasOwnProperty.call(activeMultiGroups, fromName)) {
					if (!activeMultiGroups[fromName][fromVal]) {
						rowMatch = false;
						return false;
					}
				}
			});
			$tr.find('.sku-row-check').prop('checked', rowMatch);
			$tr.toggleClass('sku-row-disabled', !rowMatch);
		});

		this.updateSkuStats();
	},

	mapAttrName: function(fromName, toName, skipInputUpdate) {
		const _this = this;
		// 更新右侧属性名标签与行内输入框
		const $rightAttr = $('.attr-info-content .attr-name').filter(function() {
			return $(this).attr('data-name') === fromName;
		});
		$rightAttr.removeClass('error').addClass('success').attr('title', toName);
		if (!skipInputUpdate) {
			$rightAttr.closest('tr').find('.inline-attr-name').val(toName);
		}

		// 更新左侧SKU表格中对应的属性组
		$('#sku-list .sku-attr-group').filter(function() {
			return $(this).attr('data-from-name') === fromName;
		}).each(function() {
			const $group = $(this);
			const skuId = $group.closest('tr').attr('data-sku');
			const $main = $group.find('.sku-attr-main');
			$main.find('.attr-name').removeClass('error').addClass('success').text(toName);
			$main.find('.sku-attr-input').attr('name', 'sku[' + skuId + '][attr][' + toName + ']');
		});

		this.updateSkuStats();
	},

	mapAttrValue: function(fromVal, mainVal, nameExt, skipInputUpdate) {
		const _this = this;
		nameExt = nameExt || {};

		// 更新左侧SKU表格中对应的属性组
		$('#sku-list .sku-attr-group').filter(function() {
			return $(this).attr('data-from-val') === fromVal;
		}).each(function() {
			const $group = $(this);
			const isIgnored = $group.hasClass('sku-attr-ignored');
			const disabledAttr = isIgnored ? ' disabled' : '';
			const skuId = $group.closest('tr').attr('data-sku');
			const $main = $group.find('.sku-attr-main');
			const currentAttrName = $main.find('.attr-name').text().trim();

			$main.find('.attr-value').removeClass('error').addClass('success').text(mainVal);
			$main.find('.sku-attr-input')
				.attr('name', 'sku[' + skuId + '][attr][' + currentAttrName + ']')
				.val(mainVal);

			$group.find('.attr-map').remove();
			let extHtml = '';
			for (let k in nameExt) {
				if (Object.prototype.hasOwnProperty.call(nameExt, k) && k !== currentAttrName) {
					const v = nameExt[k];
					extHtml += `<p class="attr-map">
									<span class="attr-name success" data-name="` + _this.escapeHtml(k) + `">` + _this.escapeHtml(k) + `</span>
									<span>: </span>
									<span class="attr-value success" data-name="` + _this.escapeHtml(v) + `">` + _this.escapeHtml(v) + `</span>
									<input type="hidden" class="sku-attr-input" name="sku[` + _this.escapeHtml(skuId) + `][attr][` + _this.escapeHtml(k) + `]" value="` + _this.escapeHtml(v) + `"` + disabledAttr + `>
								</p>`;
				}
			}
			if (extHtml) {
				$group.append(extHtml);
			}
		});

		// 更新右侧属性值标签与行内输入框
		let titleParts = [mainVal];
		let extTagsHtml = '';
		for (let k in nameExt) {
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
		let mappedCount = 0;

		$('#sku-list tbody tr').each(function() {
			totalCount++;
			const $tr = $(this);
			if ($tr.find('.sku-row-check').prop('checked')) {
				checkedCount++;
			}
			const hasError = $tr.find('.mapped-attr-cell .sku-attr-group:not(.sku-attr-ignored) .error').length > 0;
			$tr.find('.sku-img-cell').toggleClass('check', !hasError);
			if (!hasError) {
				mappedCount++;
			}
		});

		$('#total-sku-count').text(totalCount);
		$('#checked-sku-count').text(checkedCount);
		$('#mapped-sku-count').text(mappedCount);
		$('#sku-check-all').prop('checked', totalCount > 0 && checkedCount === totalCount);
	},

	updateDescCount: function() {
		const total = $('.desc-info-content .content .item').length;
		const checked = $('.desc-info-content .content .item .desc-item-check:checked').length;
		$('#desc-count').text(total);
		$('#desc-checked-count').text(checked);
	}
};

const DROP = {
	init: function() {
		var _this = this;
		$('.pic-wrap').on('mousedown', '.item', function() {
			_this.obj = $(this);
		});
		this.load();
	},
	load: function() {
		this.pObj = $('.right .pic-wrap:visible');
		if (this.pObj.length == 0) {
			return false;
		}
		this.p_w = this.pObj.width();
		this.p_h = this.pObj.height();
		this.p_x = this.pObj.offset().left;
		this.p_y = this.pObj.offset().top;
		this.pObj.off('mouseup', '.item .cover').on('mouseup', '.item .cover', this.end);
		this.pObj.off('mousemove', '.item .cover').on('mousemove', '.item .cover', this.move);
		this.pObj.off('mousedown', '.item .cover').on('mousedown', '.item .cover', this.down);
	},
	down: function(e) {
		DROP.obj = $(this).parents('.item');
		DROP.w = DROP.obj.width();
		DROP.h = DROP.obj.height();
		DROP.status = true;
		DROP.obj.before('<div class="index"></div>');
		DROP.obj.css({
			'position': 'fixed',
			'left': DROP.obj.offset().left - $(document).scrollLeft(),
			'top': DROP.obj.offset().top - $(document).scrollTop(),
			'z-index': 2
		});
		DROP.pObj.append(DROP.obj);
	},
	end: function() {
		if (DROP.pObj.find('.index').length > 0) {
			DROP.obj.css({'position': 'relative', left: 0, top: 0, 'z-index': 1});
			DROP.pObj.find('.index').before(DROP.obj);
			DROP.pObj.find('.index').remove();
			DROP.index();
		}
		DROP.status = false;
		DROP.obj = null;
	},
	move: function(e) {
		if (DROP.status) {
			DROP.x = e.pageX - DROP.w / 2;
			DROP.y = e.pageY - DROP.h / 2;
			DROP.obj.css({
				'left': DROP.x - $(document).scrollLeft(),
				'top': DROP.y - $(document).scrollTop()
			});
			DROP.offset();
		}
	},
	offset: function() {
		if (this.x > this.p_x && this.x < this.p_x + this.p_w && this.y > this.p_y && this.y < this.p_y + this.p_h) {
			var cos_num = parseInt((this.p_w - 10) / (this.w + 10));
			var x_index = parseInt((this.x - this.p_x) / (this.w + 10)) + cos_num * parseInt((this.y - this.p_y) / (this.h + 10));
			if (x_index != this.in_index) {
				this.in_index = x_index;
				this.pObj.find('.index').remove();
				this.pObj.find('.item').eq(x_index).before('<div class="index"></div>');
			}
		}
	},
	index: function() {
		DROP.pObj.find('.item').each(function(index) {
			$(this).find('.num').text(index + 1);
		});
	}
};

$(function() {
	OPERATE.init();
	DROP.init();
});