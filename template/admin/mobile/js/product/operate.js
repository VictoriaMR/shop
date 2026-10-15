$(function() {
	OPERATE.init();
});

const OPERATE = {
	url: '',
	init: function() {
		const _this = this;
		this.url = $('#add-product-page').data('url') || '';

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 点击属性标签打开映射弹窗
		$('.attr-item').on('click', function() {
			const $chip = $(this);
			const type = $chip.hasClass('attr-name') ? '1' : '2';
			const fromName = $chip.data('name');
			let attrName = $chip.closest('.attr-map-row').find('.attr-name').data('name');
			attrName = $('#sku-list .attr-name[data-name="' + attrName + '"]').eq(0).text() || attrName;

			let toName = $chip.find('span').eq(0).text();
			toName = $('#sku-list .attr-value[data-name="' + toName + '"]').eq(0).text() || toName;
			if (toName == fromName) {
				toName = '';
			}

			const ext = $chip.closest('.attr-row-vals').find('.success').eq(0).data('ext');
			let extHtml = '';
			if (ext && typeof ext === 'object') {
				let setValue = false;
				if ($chip.closest('.attr-row-vals').find('.success').eq(0).data('name') == fromName) {
					setValue = true;
				}
				for (const i in ext) {
					if (i != attrName) {
						extHtml += '<div class="item">' +
							'<input type="text" class="form-control" name="attr[name][]" value="' + i + '" placeholder="属性名">' +
							'<span>:</span>' +
							'<input type="text" class="form-control" name="attr[value][]" value="' + (setValue ? ext[i] : '') + '" placeholder="属性值">' +
							'</div>';
					}
				}
			}

			let html = '<div class="input-group">' +
				'<div class="input-group-addon"><span>' + (attrName || '映射') + '</span></div>' +
				'<input class="form-control" type="text" name="name" placeholder="请输入映射值" value="' + toName + '">' +
				'</div>' +
				'<div class="add-map-content">' + extHtml + '</div>' +
				'<div class="map-modal-actions">';
			if (type === '2') {
				html += '<button type="button" class="btn btn-info btn-xs btn-add">新增属性</button>';
			}
			html += '<button type="button" class="btn btn-success btn-xs btn-tmp-save">临时映射</button>' +
				'<button type="button" class="btn btn-primary btn-xs btn-save">保存映射</button>' +
				'</div>';

			const $mask = $('.map-modal-mask');
			const $form = $mask.find('.map-modal');
			$mask.find('.title').text(fromName);
			$form.find('[name="from_name"]').val(fromName);
			$form.find('[name="type"]').val(type);
			$form.find('.content').html(html);
			$mask.fadeIn(150);
		});

		// 保存属性映射到数据库
		$('.map-modal').on('click', '.btn-save', function() {
			const $btn = $(this);
			const $form = $btn.closest('.map-modal');
			const $mask = $btn.closest('.map-modal-mask');
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
					if (type == '1') {
						_this.mapAttrName(fromName, name);
					} else {
						const nameExt = {};
						$form.find('.add-map-content .item').each(function() {
							const tmpName = $.trim($(this).find('input').eq(0).val());
							const tmpValue = $.trim($(this).find('input').eq(1).val());
							if (tmpName && tmpValue) {
								nameExt[tmpName] = tmpValue;
							}
						});
						nameExt[$form.find('.input-group-addon').text()] = name;
						_this.mapAttrValue(fromName, nameExt);
					}
					$mask.fadeOut(150);
				}
			});
		});

		// 新增额外属性行
		$('.map-modal').on('click', '.btn-add', function() {
			const html = '<div class="item">' +
				'<input type="text" class="form-control" name="attr[name][]" value="" placeholder="属性名">' +
				'<span>:</span>' +
				'<input type="text" class="form-control" name="attr[value][]" value="" placeholder="属性值">' +
				'</div>';
			$(this).closest('.map-modal').find('.add-map-content').append(html);
		});

		// 临时映射
		$('.map-modal').on('click', '.btn-tmp-save', function() {
			const $form = $(this).closest('.map-modal');
			const $mask = $(this).closest('.map-modal-mask');
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
				const nameExt = {};
				$form.find('.add-map-content .item').each(function() {
					const tmpName = $.trim($(this).find('input').eq(0).val());
					const tmpValue = $.trim($(this).find('input').eq(1).val());
					if (tmpName && tmpValue) {
						nameExt[tmpName] = tmpValue;
					}
				});
				nameExt[$form.find('.input-group-addon').text()] = name;
				_this.mapAttrValue(fromName, nameExt);
			}
			$mask.fadeOut(150);
		});

		// 打开分类弹窗
		$('.change-category-btn').on('click', function() {
			$('.change-category-modal').fadeIn(150);
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
			$modal.fadeOut(150);
		});

		// 打开站点弹窗
		$('.change-site-btn').on('click', function() {
			$('.change-site-modal').fadeIn(150);
		});

		// 保存修改站点
		$('.change-site-modal .btn-save').on('click', function() {
			const $modal = $(this).closest('.change-site-modal');
			const sid = $modal.find('[name="site_id"]').val();
			if (!sid || sid == '0') {
				errorTips('请选择站点');
				return false;
			}
			const siteText = $modal.find('[name="site_id"] option:selected').text();
			$('#add-product-page .site-name').html(sid + ' - ' + siteText);
			$('#add-product-page [name="site_id"]').val(sid);
			$modal.fadeOut(150);
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
			let html = '';
			let name = '';
			if (type === 'unit') {
				name = '单位';
				html = '<select name="unit" class="form-control">' +
					'<option value="0">--</option>' +
					'<option value="1">件</option>' +
					'<option value="2">个</option>' +
					'<option value="3">套</option>' +
					'<option value="4">打</option>' +
					'<option value="5">箱</option>' +
					'</select>';
			} else {
				const labelMap = {
					weight: '重量(g)',
					length: '长度(cm)',
					width: '宽度(cm)',
					hight: '高度(cm)'
				};
				name = labelMap[type] || type;
				html = '<input class="form-control" type="text" name="' + type + '" value="" placeholder="请输入' + name + '">';
			}
			const $modal = $('.batch-edit-modal');
			$modal.find('.title').text('批量设置' + name);
			$modal.find('[name="type"]').val(type);
			$modal.find('.content').html(html);
			$modal.fadeIn(150);
		});

		// 确认批量赋值
		$('.batch-edit-modal').on('click', '.btn-save', function() {
			const $modal = $(this).closest('.batch-edit-modal');
			const type = $modal.find('[name="type"]').val();
			const value = $modal.find('[name="' + type + '"]').val();
			$('#sku-list .' + type).val(value);
			$modal.fadeOut(150);
		});

		// 删除描述行
		$('.desc-info-content').on('click', '.remove-desc-row', function() {
			$(this).closest('.item').remove();
		});

		// 添加描述行
		$('.btn-add-desc').on('click', function() {
			const html = '<div class="item desc-pair-row">' +
				'<input type="text" class="form-control" name="desc[name][]" value="" placeholder="属性名">' +
				'<span class="pair-sep">:</span>' +
				'<input type="text" class="form-control" name="desc[value][]" value="" placeholder="属性值">' +
				'<button type="button" class="btn btn-danger btn-xs icon-btn remove-desc-row" title="移除"><i class="fa fa-trash-can"></i></button>' +
				'</div>';
			$('.desc-info-content .content').append(html);
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
			$modal.fadeIn(150);
		});

		// 保存SKU配图
		$('.sku-modal .btn-save').on('click', function() {
			const $modal = $(this).closest('.sku-modal');
			const img = $modal.data('img');
			$modal.find('input[type="checkbox"]:checked').each(function() {
				const skuKey = $(this).data('sku');
				const $skuCard = $('#sku-list .sku-row[data-sku="' + skuKey + '"]');
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
			$modal.fadeOut(150);
		});

		// 提交保存整个产品配置
		$('.confirm-btn').on('click', function() {
			const $btn = $(this);
			const $form = $('#operate-form');
			if ($form.find('[name="site_id"]').val() == '0') {
				errorTips('请选择站点');
				return false;
			}
			if ($form.find('[name="cate_id"]').val() == '0') {
				errorTips('请选择分类');
				return false;
			}
			if ($.trim($form.find('[name="spu_name"]').val()) === '') {
				errorTips('请设置产品标题');
				return false;
			}
			$btn.button('loading');
			post(_this.url, $form.serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					setTimeout(function() {
						$btn.button('reset');
						window.history.back();
					}, 300);
				} else {
					$btn.button('reset');
				}
			});
		});

		// 按规则生成属性映射
		$('.attr-rule-btn').on('click', function() {
			const stepLen = parseInt($('.attr-rule [name="attr_rule_value"]').val(), 10);
			if (!stepLen || stepLen <= 0) {
				errorTips('请输入有效的分割长度');
				$('.attr-rule [name="attr_rule_value"]').focus();
				return false;
			}
			const nameArr = {};
			$('.attr-rule-name [name="attr_rule_name[]"]').each(function(index) {
				if ($.trim($(this).val())) {
					nameArr[index] = $.trim($(this).val());
				}
			});
			if (JSON.stringify(nameArr) === '{}') {
				errorTips('没有分配属性名');
				return false;
			}
			$('.attr-info-content .attr-value').each(function() {
				const name = String($(this).data('name') || '');
				const tmpArr = [];
				for (let i = 0; i < name.length; i++) {
					if (i % stepLen === 0) {
						tmpArr.push(name.substr(i, stepLen));
					}
				}
				const nameExt = {};
				for (const i in nameArr) {
					if (tmpArr[i] !== undefined) {
						nameExt[nameArr[i]] = tmpArr[i];
					}
				}
				_this.mapAttrValue(name, nameExt);
			});
		});
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

	mapAttrName: function(fromName, toName) {
		$('#sku-list .attr-name[data-name="' + fromName + '"]').removeClass('error').addClass('success').text(toName);
		$('.attr-info-content .attr-name[data-name="' + fromName + '"]').removeClass('error').addClass('success').attr('title', toName);
	},

	mapAttrValue: function(fromName, nameExt) {
		const $targets = $('#sku-list .attr-value[data-name="' + fromName + '"]');
		$targets.each(function() {
			const $cell = $(this).closest('.sku-mapped-cell');
			const skuKey = $(this).closest('.sku-row').data('sku');
			$cell.empty();
			let html = '';
			for (const i in nameExt) {
				html += '<p class="attr-map mapped-line">' +
					'<span class="attr-name success" data-name="' + i + '">' + i + '</span>' +
					'<span>: </span>' +
					'<span class="attr-value success" data-name="' + nameExt[i] + '">' + nameExt[i] + '</span>' +
					'<input type="hidden" name="sku[' + skuKey + '][attr][' + i + ']" value="' + nameExt[i] + '">' +
					'</p>';
			}
			$cell.append(html);
		});
		let title = '';
		for (const i in nameExt) {
			title += i + ':' + nameExt[i] + ';';
		}
		$('.attr-info-content .attr-value[data-name="' + fromName + '"]').removeClass('error').addClass('success').attr('title', title).data('ext', nameExt);
	}
};
