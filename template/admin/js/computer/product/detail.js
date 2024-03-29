$(function(){
	PRODUCT.init();
});
const PRODUCT = {
	init: function(){
		const _this = this;
		$('.status-btn').on('click', function(){
			$('#status-dealbox').dealboxShow();
		});
		$('.category-btn').on('click', function(){
			$('#category-dealbox').dealboxShow();
		});
		$('.gender-btn').on('click', function(){
			$('#gender-dealbox').dealboxShow();
		});
		$('.data-btn').on('click', function(){
			const obj = $('#dealbox-data');
			const title = $(this).parent().prev().prev().text();
			obj.find('.dealbox-title,.input-group-addon').text(title);
			obj.find('[name="name"]').val($(this).data('name'));
			obj.dealboxShow();
		});
		//保存SPUData数据 保存语言
		$('.centerShow .save-btn').on('click', function(){
			const obj = $(this);
			if (obj.hasClass('batch-save-btn')) {
				obj.parent().append('<input type="hidden" name="spu_id" value="'+$('.detail-page').data('id')+'" />');
			}
			obj.button('loading');
			post(URI+'product/detail', obj.parents('form').serializeArray(), function(res){
				showTips(res);
				if (res.code == 200) {
					if (obj.parent().find('[name="opn"]').val() != 'editSpuLanguage') {
						window.location.reload();
					} else {
						obj.parents('.centerShow').parent().dealboxHide();
						obj.button('reset');
					}
				} else {
					obj.button('reset');
				}
			});
			return false;
		});
		//免邮按钮点击
		$('.switch_botton.free-ship').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), free_ship: status, opn: 'editInfo'}, function(res) {
				showTips(res);
				if (res.code == 200) {
					_thisobj.switchBtn(status);
				}
			});
		});
		//名称翻译
		$('.name-trans-btn').on('click', function(){
			const _thisobj = $(this);
			post(URI+'product/detail', {opn: 'getSpuNameLanguage', id: $('.detail-page').data('id')}, function(res){
				if (res.code == 200) {
					var data = res.data;
					const obj = $('#dealbox-language');
					obj.find('input[name="name"]').val(_thisobj.next().text());
					obj.find('table input').val('');
					let html = '<tr>\
									<th style="width:88px">语言名称</th>\
									<th>\
										<span>文本</span>\
										<span title="智能翻译" class="glyphicon glyphicon-transfer"></span>\
									</th>\
								</tr>';
					for (const i in data) {
						html += '<tr>\
									<th>\
										<span>'+data[i].language_name+'</span>\
									</th>\
									<td class="p0">\
										<textarea class="form-control" name="language['+i+']" data-tr_code="'+data[i].tr_code+'" autocomplete="off">'+data[i].name+'</textarea>\
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
		//智能翻译
		$('#dealbox-language').on('click', '.glyphicon-transfer', function(){
			const name = $('#dealbox-language input[name="name"]').val();
			const thisobj = $(this);
			const obj = thisobj.parents('table').find('textarea');
			let len = obj.length;
			thisobj.button('loading');
			obj.each(function(){
				const value = $(this).val();
				if (value === '') {
					const _thisobj = $(this);
					const tr_code = _thisobj.data('tr_code');
					post(URI+'product/detail', {opn:'transfer', tr_code:tr_code, name:name}, function(res){
						len = len - 1;
						if (res.code === 200) {
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
		//更改排序
		$('.spu-image input[name="sort"]').on('blur', function(){
			post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), attach_id: $(this).parents('.spu-image').data('id'), sort: $(this).val(), opn: 'modifySpuImage'}, function(res) {
				showTips(res);
				if (res.code == 200) {
					window.location.reload();
				}
			});
		});
		//删除图片
		$('.spu-image .glyphicon-trash').on('click', function(){
			const item_id = $(this).parents('.spu-image').data('item_id');
			confirm('确定要删除该图片吗?', function(_thisobj){
				_thisobj.button('loading');
				post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), item_id: item_id, opn: 'deleteSpuImage'}, function(res) {
					showTips(res);
					if (res.code === 200) {
						window.location.reload();
					} else {
						_thisobj.button('reset');
					}
				});
			});
		});
		//设置SPU主图
		$('.spu-image .spu-btn').on('click', function(){
			const attach_id = $(this).parents('.spu-image').data('id');
			confirm('确定设置该图片为主图吗?', function(_thisobj){
				post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), attach_id: attach_id, opn: 'editInfo'}, function(res) {
					showTips(res);
					if (res.code === 200) {
						window.location.reload();
					} else {
						_thisobj.button('reset');
					}
				});
			});
		});
		//设置SKU主图
		$('.spu-image .sku-btn').on('click', function(){
			const attach_id = $(this).parents('.spu-image').data('id');
			const obj = $('#dealbox-sku-image');
			obj.find('input[name="attach_id"]').val(attach_id);
			obj.find('tr.item').each(function(){
				if ($(this).data('id') == attach_id) {
					$(this).find('input').prop('checked', true).prop('disabled', true);
				} else {
					$(this).find('input').prop('checked', false).prop('disabled', false);
				}
			});
			obj.dealboxShow();
		});
		//上传图片
		$('.upload-image').imageUpload('product', function(data, obj){
			obj.button('loading');
			post(URI+'product/detail', {opn: 'addSpuImage', spu_id: $('.detail-page').data('id'), attach_id: data.attach_id}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
			});
		});
		//sku状态点击
		$('.switch_botton.sku-status').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post(URI+'product/detail', {sku_id: _thisobj.parents('tr').data('id'), status: status, opn: 'editSkuInfo'}, function(res) {
				showTips(res);
				if (res.code == 200) {
					_thisobj.switchBtn(status);
				}
			});
		});
		//双击修改价格库存
		$('.can-edit').on('dblclick', function(){
			const obj = $('#dealbox-sku-info');
			const id = $(this).parent('tr').data('id');
			const value = $(this).text();
			const name = $(this).data('name');
			obj.find('.name').val(value).attr('name', name);
			obj.find('[name="sku_id"]').val(id);
			obj.find('[name="spu_id"]').remove();
			const text = {
				price: '售价',
				original_price: '原价',
				cost_price: '成本价',
				stock: '库存',
				volume: '体积',
				weight: '重量',
			};
			obj.find('.dealbox-title').text('SKU'+text[name]+'管理');
			obj.dealboxShow();
		});
		//SKU属性上传图片
		$('.sku-attr-image').imageUpload('product', function(data, obj){
			let param = obj.parents('tr').data();
			param.opn = 'modifySkuAttrImage';
			param.attach_id = data.attach_id;
			post(URI+'product/detail', param, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				}
			});
		});
		//删除sku图片
		$('.delete-attr-image-btn').on('click', function(){
			let param = $(this).parents('tr').data();
			param.opn = 'modifySkuAttrImage';
			param.attach_id = 0;
			confirm('确定要删除该属性图片吗?', function(obj){
				obj.button('loading');
				post(URI+'product/detail', param, function(res){
					showTips(res);
					if (res.code === 200) {
						window.location.reload();
					} else {
						obj.button('reset');
					}
				});
			});
		});
		//描述排序
		$('#sku-desc-content [name="sort"]').on('blur', function(){
			const id = $(this).parents('tr').data('id');
			const sort = $(this).val();
			post(URI+'product/detail', {opn: 'modifySpuDesc', item_id: id, sort: sort}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				}
			});
		});
		//删除描述文本
		$('.delete-desc-btn').on('click', function(){
			const id = $(this).parents('tr').data('id');
			confirm('确定要删除该描述文本吗?', function(obj){
				obj.button('loading');
				post(URI+'product/detail', {opn: 'deleteSpuDesc', item_id: id}, function(res){
					showTips(res);
					if (res.code === 200) {
						window.location.reload();
					} else {
						obj.button('reset');
					}
				});
			});
		});
		//新增描述性文本
		$('.add-desc-btn').on('click', function(){
			_this.initDescShow();
		});
		//编辑描述性文本
		$('.edit-desc-btn').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			const id = _thisobj.parents('tr').data('id');
			post(URI+'product/detail', {opn: 'getSpuDescInfo', item_id: id}, function(res){
				if (res.code === 200) {
					_this.initDescShow(res.data);
				} else {
					showTips(res);
				}
				_thisobj.button('reset');
			});
		});
		//描述图片排序
		$('.spu-introduce-image [name="sort"]').on('blur', function(){
			post(URI+'product/detail', {item_id: $(this).parent().data('id'), attach_id: $(this).parents('.spu-introduce-image').data('id'), sort: $(this).val(), opn: 'modifySpuIntroduceImage'}, function(res) {
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				}
			});
		});
		//删除描述图片
		$('.spu-introduce-image .delete-introduce-btn').on('click', function(){
			const id = $(this).parent().data('id');
			confirm('确定删除该描述图片吗?', function(obj){
				obj.button('loading');
				post(URI+'product/detail', {item_id: id, opn: 'deleteSpuIntroduceImage'}, function(res) {
					showTips(res);
					if (res.code === 200) {
						window.location.reload();
					} else {
						obj.button('reset');
					}
				});
			});
		});
		//上传描述图片
		$('.upload-introduce-image').imageUpload('introduce', function(data, obj){
			const spu_id = $('.detail-page').data('id');
			obj.button('loading');
			post(URI+'product/detail', {opn: 'addSpuIntroduceImage', spu_id: spu_id, attach_id:data.attach_id}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
			});
		});
		//描述选中
		$('#sku-desc-content .glyphicon').on('click', function(){
			if ($(this).hasClass('glyphicon-ok-circle')) {
				$(this).removeClass('glyphicon-ok-circle').addClass('glyphicon-ok-sign red');
			} else {
				$(this).removeClass('glyphicon-ok-sign red').addClass('glyphicon-ok-circle');
			}
			_this.initDescSelect();
		});
		//分组点击
		$('#sku-desc-content .desc-group-btn').on('click', function(){
			if ($('#sku-desc-content .glyphicon-ok-sign').length === 0) {
				errorTips('请先选择需要分组的描述');
				return false;
			}
			$('#desc-group-dealbox').dealboxShow();
		});
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
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.dealboxShow();
	},
	initDescSelect: function() {
		var obj = $('#desc-group-dealbox');
		obj.find('[name="id[]"]').remove();
		$('#sku-desc-content .glyphicon-ok-sign').each(function(){
			var id = $(this).parents('tr').data('id');
			obj.find('form').append('<input type="hidden" name="id[]" value="'+id+'">');
		});
	}
};