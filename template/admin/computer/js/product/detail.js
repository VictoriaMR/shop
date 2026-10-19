$(function(){
	PRODUCT.init();
});

const PRODUCT = {
	activeStatusFilter: 'all',
	activeAttrFilter: '',

	init: function(){
		const _this = this;
		const spuId = $('.detail-page').data('id');

		// 弹窗触发
		$('.status-btn').on('click', function(){
			$('#status-dealbox').dealboxShow();
		});
		$('.category-btn').on('click', function(){
			$('#category-dealbox').dealboxShow();
		});
		$('.data-btn').on('click', function(){
			const obj = $('#dealbox-data');
			const name = $(this).data('name');
			const title = $(this).data('title') || '修改参数';
			const val = $('.j-data-val[data-name="' + name + '"]').text().trim();
			obj.find('.dealbox-title, .input-group-addon span').text(title);
			obj.find('[name="name"]').val(name);
			obj.find('[name="value"]').val(val === '--' ? '' : val);
			obj.dealboxShow();
		});

		// 顶部一键上下架切换
		$('.j-quick-toggle-spu-status').on('click', function(){
			const btn = $(this);
			const curStatus = parseInt(btn.attr('data-status'), 10);
			const nextStatus = curStatus === 1 ? 2 : 1;
			btn.prop('disabled', true);
			post('/product/detail', {spu_id: spuId, status: nextStatus, opn: 'editInfo'}, function(res){
				showTips(res);
				if (res.code == 1) {
					window.location.reload();
				} else {
					btn.prop('disabled', false);
				}
			});
		});

		// 弹窗通用保存
		$('.centerShow .save-btn').on('click', function(){
			const obj = $(this);
			const form = obj.parents('form');
			const opn = form.find('[name="opn"]').val();
			if (obj.hasClass('batch-save-btn')) {
				if (form.find('input.batch-spu-id').length === 0) {
					form.append('<input type="hidden" class="batch-spu-id" name="spu_id" value="' + spuId + '" />');
				}
				form.find('[name="sku_id"]').val('');
			} else {
				form.find('input.batch-spu-id').remove();
			}
			obj.button('loading');
			post('/product/detail', form.serializeArray(), function(res){
				showTips(res);
				if (res.code == 1) {
					if (opn === 'editSpuLanguage') {
						const zhName = form.find('[name="language[0]"]').val();
						if (zhName) {
							$('#spu-main-title').text(zhName);
						}
						obj.closest('.dealbox').dealboxHide();
						obj.button('reset');
					} else {
						window.location.reload();
					}
				} else {
					obj.button('reset');
				}
			});
			return false;
		});

		// 免邮开关
		$('.switch_botton.free-ship').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post('/product/detail', {spu_id: spuId, free_ship: status, opn: 'editInfo'}, function(res) {
				showTips(res);
				if (res.code == 1) {
					_thisobj.switchBtn(status);
				}
			});
		});

		// 热门开关
		$('.switch_botton.is-hot').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post('/product/detail', {spu_id: spuId, is_hot: status, opn: 'editInfo'}, function(res) {
				showTips(res);
				if (res.code == 1) {
					_thisobj.switchBtn(status);
				}
			});
		});

		// 名称多语言翻译弹窗
		$('.name-trans-btn').on('click', function(){
			post('/product/detail', {opn: 'getSpuNameLanguage', id: spuId}, function(res){
				if (res.code == 1) {
					const data = res.data;
					const obj = $('#dealbox-language');
					obj.find('input[name="name"]').val($('#spu-main-title').text().trim());
					let html = '<tr>\
									<th style="width:110px">语言名称</th>\
									<th>\
										<span>对应文本</span>\
										<span title="一键智能翻译空缺语言" class="j-smart-translate pointer ml6 c-primary"><i class="fa fa-language"></i> 智能翻译</span>\
									</th>\
								</tr>';
					for (const i in data) {
						html += '<tr>\
									<th><span>' + data[i].language_name + '</span></th>\
									<td class="p0">\
										<textarea class="form-control" name="language[' + i + ']" data-tr_code="' + data[i].tr_code + '" autocomplete="off">' + (data[i].name || '') + '</textarea>\
									</td>\
								</tr>';
					}
					obj.find('table tbody').html(html);
					obj.dealboxShow();
					obj.find('textarea').autoHeight();
				} else {
					showTips(res);
				}
			});
		});

		// 智能翻译
		$('#dealbox-language').on('click', '.j-smart-translate', function(){
			const name = $('#dealbox-language textarea[name="language[0]"]').val() || $('#dealbox-language input[name="name"]').val();
			const thisobj = $(this);
			const obj = thisobj.parents('table').find('textarea');
			let len = obj.length;
			thisobj.button('loading');
			obj.each(function(){
				const value = $(this).val().trim();
				const tr_code = $(this).data('tr_code');
				if (value === '' && tr_code && tr_code !== 'zh') {
					const _thisobj = $(this);
					post('/product/detail', {opn: 'transfer', tr_code: tr_code, name: name}, function(res){
						len = len - 1;
						if (res.code == 1) {
							_thisobj.val(res.data);
						} else {
							showTips(res);
						}
						if (len === 0) {
							thisobj.button('reset');
						}
					});
				} else {
					len = len - 1;
					if (len === 0) {
						thisobj.button('reset');
					}
				}
			});
		});

		// 主图拖动排序（无刷新保存）
		_this.initSpuImageDragSort(spuId);

		// 删除主图
		$('.spu-image .delete-spu-img-btn').on('click', function(){
			const card = $(this).closest('.spu-image');
			const item_id = card.data('item_id');
			confirm('确定要删除该主图吗?', function(_thisobj, pop){
				_thisobj.button('loading');
				post('/product/detail', {spu_id: spuId, item_id: item_id, opn: 'deleteSpuImage'}, function(res) {
					showTips(res);
					_thisobj.button('reset');
					if (pop) pop.hide();
					if (res.code == 1) {
						card.fadeOut(200, function(){
							$(this).remove();
							_this.refreshSpuImageIndex();
						});
					}
				});
			});
		});

		// 设置SPU主封面
		$('.spu-image .spu-btn').on('click', function(){
			const attach_id = $(this).closest('.spu-image').data('id');
			post('/product/detail', {spu_id: spuId, attach_id: attach_id, opn: 'editInfo'}, function(res) {
				showTips(res);
				if (res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 设置SKU主图弹窗
		$('.spu-image .sku-btn').on('click', function(){
			const attach_id = $(this).closest('.spu-image').data('id');
			const obj = $('#dealbox-sku-image');
			obj.find('input[name="attach_id"]').val(attach_id);
			$('#modal-check-all-sku-img').prop('checked', false);
			obj.find('tr.item').each(function(){
				if ($(this).data('id') == attach_id) {
					$(this).find('input').prop('checked', true).prop('disabled', true);
				} else {
					$(this).find('input').prop('checked', false).prop('disabled', false);
				}
			});
			obj.dealboxShow();
		});

		$('#modal-check-all-sku-img').on('change', function(){
			const checked = $(this).prop('checked');
			$('#dealbox-sku-image tr.item input:not(:disabled)').prop('checked', checked);
		});

		// 上传主图
		$('.upload-image').imageUpload('product', function(data, obj){
			obj.button('loading');
			post('/product/detail', {opn: 'addSpuImage', spu_id: spuId, attach_id: data.attach_id}, function(res){
				showTips(res);
				if (res.code == 1) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
			});
		});

		// 规格属性看板：点击规格过滤下方SKU矩阵
		$('.j-filter-sku-by-attr').on('click', function(){
			const chip = $(this).closest('.attr-val-chip');
			const attvName = String(chip.data('attv-name') || '').toLowerCase();
			if (chip.hasClass('active-filter')) {
				chip.removeClass('active-filter');
				_this.activeAttrFilter = '';
				$('#clear-attr-filter-btn').addClass('hidden');
				$('#sku-active-filter-tag').addClass('hidden').text('');
			} else {
				$('.attr-val-chip').removeClass('active-filter');
				chip.addClass('active-filter');
				_this.activeAttrFilter = attvName;
				$('#clear-attr-filter-btn').removeClass('hidden');
				$('#sku-active-filter-tag').removeClass('hidden').text('规格过滤: ' + chip.data('attv-name'));
			}
			_this.filterSkuRows();
		});

		$('#clear-attr-filter-btn, #sku-active-filter-tag').on('click', function(){
			$('.attr-val-chip').removeClass('active-filter');
			_this.activeAttrFilter = '';
			$('#clear-attr-filter-btn').addClass('hidden');
			$('#sku-active-filter-tag').addClass('hidden').text('');
			_this.filterSkuRows();
		});

		// SKU状态筛选与搜索框
		$('#sku-status-filter button').on('click', function(){
			$('#sku-status-filter button').removeClass('active');
			$(this).addClass('active');
			_this.activeStatusFilter = $(this).data('status');
			_this.filterSkuRows();
		});

		$('#sku-keyword-input').on('input', function(){
			_this.filterSkuRows();
		});

		// SKU全选与勾选计数
		$('#check-all-skus').on('change', function(){
			const checked = $(this).prop('checked');
			$('#sku-matrix-table tbody tr.sku-row:visible .sku-row-check').prop('checked', checked);
			_this.updateCheckedSkuCount();
		});

		$('#sku-matrix-table').on('change', '.sku-row-check', function(){
			_this.updateCheckedSkuCount();
		});

		// 批量修改勾选SKU的字段
		$('#apply-sku-batch-btn').on('click', function(){
			const skuIds = _this.getCheckedSkuIds();
			if (skuIds.length === 0) {
				errorTips('请先在表格左侧勾选需要批量修改的SKU');
				return;
			}
			const field = $('#sku-batch-field').val();
			const val = $('#sku-batch-val').val().trim();
			if (val === '') {
				errorTips('请输入批量修改的目标数值');
				return;
			}
			const payload = {
				opn: 'editSkuInfo',
				spu_id: spuId,
				sku_id: skuIds
			};
			payload[field] = val;
			post('/product/detail', payload, function(res){
				showTips(res);
				if (res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 批量上下架勾选的SKU
		$('.j-batch-sku-status').on('click', function(){
			const skuIds = _this.getCheckedSkuIds();
			if (skuIds.length === 0) {
				errorTips('请先在表格左侧勾选需要操作的SKU');
				return;
			}
			const targetStatus = $(this).data('status');
			post('/product/detail', {
				opn: 'editSkuInfo',
				spu_id: spuId,
				sku_id: skuIds,
				status: targetStatus
			}, function(res){
				showTips(res);
				if (res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 按成本+邮费一键重算售价
		$('.recalc-sku-price-btn').on('click', function(){
			const skuIds = _this.getCheckedSkuIds();
			const scopeText = skuIds.length > 0 ? ('已勾选的 ' + skuIds.length + ' 个SKU') : '当前SPU下全部SKU';
			confirm('确定按【成本价 + 基础邮费】阶梯公式重算' + scopeText + '的售价吗？', function(btn, pop){
				btn.button('loading');
				post('/product/detail', {
					opn: 'recalcSkuPrice',
					spu_id: spuId,
					sku_id: skuIds
				}, function(res){
					showTips(res);
					btn.button('reset');
					if (pop) pop.hide();
					if (res.code == 1) {
						window.location.reload();
					}
				});
			});
		});

		// 单个SKU状态开关
		$('.switch_botton.sku-status').on('click', function(){
			const _thisobj = $(this);
			const tr = _thisobj.closest('tr');
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post('/product/detail', {sku_id: tr.data('id'), spu_id: spuId, status: status, opn: 'editSkuInfo'}, function(res) {
				showTips(res);
				if (res.code == 1) {
					_thisobj.switchBtn(status);
					tr.attr('data-status', status);
					_this.updateSpuPriceBanner(res.data);
				}
			});
		});

		// SKU单元格直接行内编辑（单击或双击触发，回车/失焦自动保存，无刷新更新DOM）
		$('#sku-matrix-table').on('click dblclick', 'td.can-edit', function(){
			const td = $(this);
			if (td.find('input.inline-sku-input').length > 0) {
				return;
			}
			const tr = td.closest('tr');
			const skuId = tr.data('id');
			const field = td.data('name');
			const origVal = td.text().trim();

			const input = $('<input type="text" class="inline-sku-input" />').val(origVal);
			td.empty().append(input);
			input.focus().select();

			const commitChange = function() {
				const newVal = input.val().trim();
				if (newVal === origVal) {
					td.text(origVal);
					return;
				}
				const payload = {
					opn: 'editSkuInfo',
					spu_id: spuId,
					sku_id: skuId
				};
				payload[field] = newVal;
				td.text(newVal);
				post('/product/detail', payload, function(res){
					showTips(res);
					if (res.code == 1) {
						_this.recalcRowProfit(tr);
						_this.updateSpuPriceBanner(res.data);
					} else {
						td.text(origVal);
					}
				});
			};

			input.on('blur', commitChange);
			input.on('keydown', function(e){
				if (e.key === 'Enter') {
					e.preventDefault();
					input.off('blur');
					commitChange();
				} else if (e.key === 'Escape') {
					td.text(origVal);
				}
			});
		});

		// SKU属性上传图片
		$('.sku-attr-image').imageUpload('product', function(data, obj){
			let param = obj.closest('.attr-val-chip').data();
			param.opn = 'modifySkuAttrImage';
			param.attach_id = data.attach_id;
			post('/product/detail', param, function(res){
				showTips(res);
				if (res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 删除SKU属性图片
		$('.delete-attr-image-btn').on('click', function(){
			let param = $(this).closest('.attr-val-chip').data();
			param.opn = 'modifySkuAttrImage';
			param.attach_id = 0;
			confirm('确定要移除该属性规格图片吗?', function(obj, pop){
				obj.button('loading');
				post('/product/detail', param, function(res){
					showTips(res);
					if (pop) pop.hide();
					if (res.code == 1) {
						window.location.reload();
					} else {
						obj.button('reset');
					}
				});
			});
		});

		// 描述排序图标点击（无刷新移动行并保存）
		$('#sku-desc-content').on('click', '.sort-btn-action', function(){
			const btn = $(this);
			if (btn.is(':disabled') || btn.hasClass('disabled')) {
				return false;
			}
			const tr = btn.closest('tr.desc-item-row');
			const id = tr.data('id');
			const groupId = tr.data('group_id');
			const type = btn.data('type');
			const groupRows = $('#sku-desc-content tr.desc-item-row[data-group_id="' + groupId + '"]');
			if (groupRows.length <= 1) {
				return false;
			}
			groupRows.find('.sort-btn-action').prop('disabled', true);
			post('/product/detail', {opn: 'modifySpuDesc', item_id: id, type: type}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					if (type === 'top') {
						groupRows.first().before(tr);
					} else if (type === 'up') {
						const prev = tr.prev('tr.desc-item-row[data-group_id="' + groupId + '"]');
						if (prev.length) {
							prev.before(tr);
						}
					} else if (type === 'down') {
						const next = tr.next('tr.desc-item-row[data-group_id="' + groupId + '"]');
						if (next.length) {
							next.after(tr);
						}
					} else if (type === 'bottom') {
						groupRows.last().after(tr);
					}
				}
				_this.refreshDescSortButtons(groupId);
			});
		});

		// 删除单条描述文本（无刷新移除行）
		$('.delete-desc-btn').on('click', function(){
			const tr = $(this).closest('tr');
			const id = tr.data('id');
			const groupId = tr.data('group_id');
			confirm('确定要删除该描述属性吗?', function(obj, pop){
				obj.button('loading');
				post('/product/detail', {opn: 'deleteSpuDesc', item_id: id}, function(res){
					showTips(res);
					obj.button('reset');
					if (pop) pop.hide();
					if (res.code == 1) {
						tr.fadeOut(200, function(){
							$(this).remove();
							_this.refreshDescSortButtons(groupId);
						});
					}
				});
			});
		});

		// 批量删除选中描述属性
		$('.batch-delete-desc-btn').on('click', function(){
			const ids = [];
			$('#sku-desc-content .desc-check-icon.fas').each(function(){
				ids.push($(this).closest('tr').data('id'));
			});
			if (ids.length === 0) {
				errorTips('请先点击左侧图标选择需要删除的描述项');
				return;
			}
			confirm('确定批量删除已选中的 ' + ids.length + ' 条描述属性吗？', function(obj, pop){
				obj.button('loading');
				post('/product/detail', {opn: 'batchDeleteSpuDesc', id: ids}, function(res){
					showTips(res);
					obj.button('reset');
					if (pop) pop.hide();
					if (res.code == 1) {
						$('#sku-desc-content .desc-check-icon.fas').closest('tr').fadeOut(200, function(){
							$(this).remove();
							_this.refreshDescSortButtons();
						});
					}
				});
			});
		});

		// 全选/反选描述属性
		$('#toggle-all-desc-btn').on('click', function(){
			const allIcons = $('#sku-desc-content td.desc-check-cell .desc-check-icon');
			const selectedCount = allIcons.filter('.fas').length;
			if (selectedCount === allIcons.length) {
				allIcons.removeClass('fas red').addClass('far');
			} else {
				allIcons.removeClass('far').addClass('fas red');
			}
			_this.initDescSelect();
		});

		// 新增描述性文本
		$('.add-desc-btn').on('click', function(){
			_this.initDescShow();
		});

		// 编辑描述性文本
		$('.edit-desc-btn').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			const id = _thisobj.closest('tr').data('id');
			post('/product/detail', {opn: 'getSpuDescInfo', item_id: id}, function(res){
				if (res.code == 1) {
					_this.initDescShow(res.data);
				} else {
					showTips(res);
				}
				_thisobj.button('reset');
			});
		});

		// 描述图片拖动排序（无刷新保存）
		_this.initIntroImageDragSort(spuId);

		// 删除描述图片（无刷新移除卡片）
		$('.spu-introduce-image .delete-introduce-btn').on('click', function(){
			const card = $(this).closest('.spu-introduce-image');
			const id = card.data('item_id') || $(this).parent().data('id');
			confirm('确定删除该详情介绍图片吗?', function(obj, pop){
				obj.button('loading');
				post('/product/detail', {item_id: id, opn: 'deleteSpuIntroduceImage'}, function(res) {
					showTips(res);
					obj.button('reset');
					if (pop) pop.hide();
					if (res.code == 1) {
						card.fadeOut(200, function(){
							$(this).remove();
							_this.refreshIntroImageIndex();
						});
					}
				});
			});
		});

		// 上传描述图片
		$('.upload-introduce-image').imageUpload('introduce', function(data, obj){
			obj.button('loading');
			post('/product/detail', {opn: 'addSpuIntroduceImage', spu_id: spuId, attach_id: data.attach_id}, function(res){
				showTips(res);
				if (res.code == 1) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
			});
		});

		// 描述选中切换
		$('#sku-desc-content').on('click', 'td.desc-check-cell', function(){
			const icon = $(this).find('.desc-check-icon');
			if (icon.hasClass('far')) {
				icon.removeClass('far').addClass('fas red');
			} else {
				icon.removeClass('fas red').addClass('far');
			}
			_this.initDescSelect();
		});

		// 分组点击
		$('#sku-desc-content .desc-group-btn').on('click', function(){
			if ($('#sku-desc-content .desc-check-icon.fas').length === 0) {
				errorTips('请先选择需要分组的描述项');
				return false;
			}
			$('#desc-group-dealbox').dealboxShow();
		});
	},

	filterSkuRows: function() {
		const statusFilter = String(this.activeStatusFilter);
		const attrFilter = this.activeAttrFilter;
		const kw = ($('#sku-keyword-input').val() || '').trim().toLowerCase();

		$('#sku-matrix-table tbody tr.sku-row').each(function(){
			const tr = $(this);
			const rowStatus = String(tr.attr('data-status'));
			const searchStr = String(tr.attr('data-search') || '');
			let show = true;
			if (statusFilter !== 'all' && rowStatus !== statusFilter) {
				show = false;
			}
			if (show && attrFilter && searchStr.indexOf(attrFilter) === -1) {
				show = false;
			}
			if (show && kw && searchStr.indexOf(kw) === -1) {
				show = false;
			}
			tr.toggle(show);
		});
		this.updateCheckedSkuCount();
	},

	getCheckedSkuIds: function() {
		const ids = [];
		$('#sku-matrix-table tbody tr.sku-row .sku-row-check:checked').each(function(){
			ids.push($(this).val());
		});
		return ids;
	},

	updateCheckedSkuCount: function() {
		const cnt = $('#sku-matrix-table tbody tr.sku-row .sku-row-check:checked').length;
		$('#sku-checked-count').text(cnt);
	},

	recalcRowProfit: function(tr) {
		const postFee = parseFloat($('.detail-page').data('post-fee')) || 0;
		const salePrice = parseFloat(tr.find('td[data-name="price"]').text()) || 0;
		const costPrice = parseFloat(tr.find('td[data-name="cost_price"]').text()) || 0;
		const profit = salePrice - costPrice - postFee;
		const cell = tr.find('.sku-profit-cell');
		cell.text('￥' + profit.toFixed(2));
		cell.toggleClass('c-success', profit >= 0).toggleClass('c-danger', profit < 0);
	},

	updateSpuPriceBanner: function(rangeData) {
		if (!rangeData || !rangeData.min_price) {
			return;
		}
		const minP = parseFloat(rangeData.min_price);
		const maxP = parseFloat(rangeData.max_price);
		const text = minP < maxP ? ('￥' + minP + ' ~ ￥' + maxP) : ('￥' + minP);
		$('#spu-price-range-text').text(text);
	},

	initDescShow: function(data) {
		const obj = $('#dealbox-desc');
		if (data) {
			obj.find('.dealbox-title').text('编辑描述文本');
		} else {
			data = {
				item_id: 0,
				name: '',
				value: '',
				sort: 0,
			};
			obj.find('.dealbox-title').text('增加描述文本');
		}
		for (var i in data) {
			obj.find('[name="' + i + '"]').val(data[i]);
		}
		obj.dealboxShow();
	},

	initDescSelect: function() {
		var obj = $('#desc-group-dealbox');
		obj.find('[name="id[]"]').remove();
		$('#sku-desc-content .desc-check-icon.fas').each(function(){
			var id = $(this).closest('tr').data('id');
			obj.find('form').append('<input type="hidden" name="id[]" value="' + id + '">');
		});
	},

	initSpuImageDragSort: function(spuId) {
		const _this = this;
		this.bindGalleryDragSort({
			gridSelector: '#sec-images .media-gallery-grid',
			itemSelector: '.spu-image',
			idDataKey: 'id',
			onRefreshIndex: function() {
				_this.refreshSpuImageIndex();
			},
			onSave: function(attachIds) {
				post('/product/detail', {
					spu_id: spuId,
					attach_ids: attachIds,
					opn: 'modifySpuImage'
				}, function(res) {
					showTips(res);
				});
			}
		});
	},

	initIntroImageDragSort: function(spuId) {
		const _this = this;
		this.bindGalleryDragSort({
			gridSelector: '#sec-intro .media-gallery-grid',
			itemSelector: '.spu-introduce-image',
			idDataKey: 'item_id',
			onRefreshIndex: function() {
				_this.refreshIntroImageIndex();
			},
			onSave: function(itemIds) {
				post('/product/detail', {
					spu_id: spuId,
					item_ids: itemIds,
					opn: 'modifySpuIntroduceImage'
				}, function(res) {
					showTips(res);
				});
			}
		});
	},

	bindGalleryDragSort: function(options) {
		const $grid = $(options.gridSelector);
		if ($grid.length === 0) {
			return;
		}
		let dragEl = null;
		let initialOrder = '';

		const getOrderIds = function() {
			const ids = [];
			$grid.find(options.itemSelector).each(function() {
				ids.push($(this).data(options.idDataKey));
			});
			return ids;
		};

		$grid.on('dragstart', options.itemSelector, function(e) {
			if ($(e.target).closest('.image-btn').length > 0) {
				e.preventDefault();
				return false;
			}
			dragEl = this;
			initialOrder = getOrderIds().join(',');
			$(this).addClass('is-dragging');
			const dt = e.originalEvent && e.originalEvent.dataTransfer;
			if (dt) {
				dt.effectAllowed = 'move';
				try {
					dt.setData('text/plain', String($(this).data(options.idDataKey) || ''));
				} catch (err) {}
			}
		});

		$grid.on('dragover', options.itemSelector, function(e) {
			if (!dragEl) {
				return;
			}
			e.preventDefault();
			const dt = e.originalEvent && e.originalEvent.dataTransfer;
			if (dt) {
				dt.dropEffect = 'move';
			}
			if (this === dragEl) {
				return;
			}
			const rect = this.getBoundingClientRect();
			const clientX = e.originalEvent ? e.originalEvent.clientX : e.clientX;
			const isAfter = (clientX - rect.left) > (rect.width / 2);
			if (isAfter) {
				if (this.nextElementSibling !== dragEl) {
					$(this).after(dragEl);
					options.onRefreshIndex();
				}
			} else {
				if (this.previousElementSibling !== dragEl) {
					$(this).before(dragEl);
					options.onRefreshIndex();
				}
			}
		});

		$grid.on('dragover drop', function(e) {
			if (dragEl) {
				e.preventDefault();
			}
		});

		$grid.on('dragend', options.itemSelector, function() {
			if (!dragEl) {
				return;
			}
			$(dragEl).removeClass('is-dragging');
			dragEl = null;
			options.onRefreshIndex();

			const ids = getOrderIds();
			if (ids.join(',') === initialOrder) {
				return;
			}
			options.onSave(ids);
		});
	},

	refreshSpuImageIndex: function() {
		$('#sec-images .media-gallery-grid .spu-image').each(function(idx) {
			$(this).find('.spu-index-badge').text('#' + (idx + 1));
		});
	},

	refreshIntroImageIndex: function() {
		$('#sec-intro .media-gallery-grid .spu-introduce-image').each(function(idx) {
			$(this).find('.intro-index-badge').text('#' + (idx + 1));
		});
	},

	refreshDescSortButtons: function(groupId) {
		const selector = groupId !== undefined
			? '#sku-desc-content tr.desc-group-header[data-group_id="' + groupId + '"]'
			: '#sku-desc-content tr.desc-group-header';
		$(selector).each(function() {
			const gid = $(this).data('group_id');
			const rows = $('#sku-desc-content tr.desc-item-row[data-group_id="' + gid + '"]');
			const total = rows.length;
			rows.each(function(idx) {
				const row = $(this);
				row.find('.sort-btn-action[data-type="top"], .sort-btn-action[data-type="up"]').prop('disabled', idx === 0);
				row.find('.sort-btn-action[data-type="down"], .sort-btn-action[data-type="bottom"]').prop('disabled', idx === total - 1);
			});
		});
	}
};