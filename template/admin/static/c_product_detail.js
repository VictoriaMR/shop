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
		$('.centerShow .btn.save').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			post(URI+'product/detail', _thisobj.parent().serializeArray(), function(res) {
				window.location.reload();
			}, function(res) {
				_thisobj.button('reset');
				_thisobj.parents('.centerShow').parent().dealboxHide();
			});
		});
		//免邮按钮点击
		$('.switch_botton.free-ship').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), free_ship: status, opn: 'editInfo'}, function(res) {
				_thisobj.switchBtn(status);
			});
		});
		//名称翻译
		$('.name-trans-btn').on('click', function(){
			const _thisobj = $(this);
			post(URI+'product/detail', {opn: 'getSpuNameLanguage', id: $('.detail-page').data('id')}, function(data){
				console.log(data, 'data')
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
									<textarea class="form-control" rows="2" name="language['+i+']" data-tr_code="'+data[i].tr_code+'" autocomplete="off">'+data[i].name+'</textarea>\
								</td>\
							</tr>';
				}
				obj.find('table tbody').html(html);
				obj.dealboxShow();
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
					$.post(URI+'product/detail', {opn:'transfer', tr_code:tr_code, name:name}, function(res){
						len = len - 1;
						if (res.code === '200') {
							_thisobj.val(res.data);
						} else {
							errorTips(res.message);
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
		//保存语言
		$('#dealbox-language .save-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			post(URI+'product/detail', $('#dealbox-language form').serializeArray(), function(){
				window.location.reload();
			}, function(res){
				obj.button('reset');
				obj.parents('.centerShow').parent().dealboxHide();
			});
			return false;
		});
		//更改排序
		$('.spu-image input[name="sort"]').on('blur', function(){
			post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), attach_id: $(this).parents('.spu-image').data('id'), sort: $(this).val(), opn: 'modifySpuImage'}, function(res) {
				window.location.reload();
			});
		});
		//删除图片
		$('.spu-image .glyphicon-trash').on('click', function(){
			const attach_id = $(this).parents('.spu-image').data('id');
			confirm('确定要删除该图片吗?', function(_thisobj){
				_thisobj.button('loading');
				$.post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), attach_id: attach_id, opn: 'deleteSpuImage'}, function(res) {
					if (res.code === '200') {
						successTips(res.message);
						window.location.reload();
					} else {
						errorTips(res.message);
						_thisobj.button('reset');
					}
				});
			});
		});
		//设置SPU主图
		$('.spu-image .spu-btn').on('click', function(){
			const attach_id = $(this).parents('.spu-image').data('id');
			confirm('确定设置该图片为主图吗?', function(_thisobj){
				$.post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), attach_id: attach_id, opn: 'editInfo'}, function(res) {
					if (res.code === '200') {
						successTips(res.message);
						window.location.reload();
					} else {
						errorTips(res.message);
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
		//保存sku主图
		$('#dealbox-sku-image .save-btn').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			$.post(URI+'product/detail', _thisobj.parents().serializeArray(), function(res) {
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					errorTips(res.message);
					_thisobj.button('reset');
				}
			});
		});
		//上传图片
		$('.upload-image').imageUpload('product', function(data, obj){
			obj.button('loading');
			$.post(URI+'product/detail', {opn: 'addSpuImage', spu_id: $('.detail-page').data('id'), attach_id: data.attach_id}, function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					errorTips(res.message);
					obj.button('reset');
				}
			});
		});
		//sku状态点击
		$('.switch_botton.sku-status').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post(URI+'product/detail', {sku_id: _thisobj.parents('tr').data('id'), status: status, opn: 'editSkuInfo'}, function(res) {
				_thisobj.switchBtn(status);
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
		//保存
		$('#dealbox-sku-info').on('click', '.save-btn,.batch-save-btn', function(){
			const _thisobj = $(this);
			if (_thisobj.hasClass('batch-save-btn')) {
				_thisobj.parent().append('<input type="hidden" name="spu_id" value="'+$('.detail-page').data('id')+'" />');
			}
			_thisobj.button('loading');
			$.post(URI+'product/detail', _thisobj.parents('form').serializeArray(), function(res) {
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					_thisobj.button('reset');
					errorTips(res.message);
				}
			});
		});
		//SKU属性上传图片
		$('.sku-attr-image').imageUpload('product', function(data, obj){
			let param = obj.parents('tr').data();
			param.opn = 'modifySkuAttrImage';
			param.attach_id = data.attach_id;
			$.post(URI+'product/detail', param, function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					errorTips(res.message);
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
				$.post(URI+'product/detail', param, function(res){
					if (res.code === '200') {
						successTips(res.message);
						window.location.reload();
					} else {
						obj.button('reset');
						errorTips(res.message);
					}
				});
			});
		});
		//描述排序
		$('#sku-desc-content [name="sort"]').on('blur', function(){
			const id = $(this).parents('tr').data('id');
			const sort = $(this).val();
			$.post(URI+'product/detail', {opn: 'modifySpuDesc', item_id: id, sort: sort}, function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					errorTips(res.message);
				}
			});
		});
		//删除描述文本
		$('.delete-desc-btn').on('click', function(){
			const id = $(this).parents('tr').data('id');
			confirm('确定要删除该描述文本吗?', function(obj){
				obj.button('loading');
				$.post(URI+'product/detail', {opn: 'deleteSpuDesc', item_id: id}, function(res){
					if (res.code === '200') {
						successTips(res.message);
						window.location.reload();
					} else {
						errorTips(res.message);
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
			$.post(URI+'product/detail', {opn: 'getSpuDescInfo', item_id: id}, function(res){
				_thisobj.button('reset');
				if (res.code === '200') {
					_this.initDescShow(res.data);
				} else {
					errorTips(res.message);
				}
			});
		});
		//保存描述性文本
		$('#dealbox-desc .save-btn').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			$.post(URI+'product/detail', _thisobj.parent().serializeArray(), function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					_thisobj.button('reset');
					errorTips(res.message);
				}
			});
		});
		//描述图片排序
		$('.spu-introduce-image [name="sort"]').on('blur', function(){
			post(URI+'product/detail', {item_id: $(this).parent().data('id'), attach_id: $(this).parents('.spu-introduce-image').data('id'), sort: $(this).val(), opn: 'modifySpuIntroduceImage'}, function(res) {
				window.location.reload();
			});
		});
		//删除描述图片
		$('.spu-introduce-image .delete-introduce-btn').on('click', function(){
			const id = $(this).parent().data('id');
			confirm('确定删除该描述图片吗?', function(obj){
				obj.button('loading');
				$.post(URI+'product/detail', {item_id: id, opn: 'deleteSpuIntroduceImage'}, function(res) {
					if (res.code === '200') {
						successTips(res.message);
						window.location.reload();
					} else {
						obj.button('reset');
						errorTips(res.message);
					}
				});
			});
		});
		//上传描述图片
		$('.upload-introduce-image').imageUpload('introduce', function(data, obj){
			const spu_id = $('.detail-page').data('id');
			obj.button('loading');
			$.post(URI+'product/detail', {opn: 'addSpuIntroduceImage', spu_id: spu_id, attach_id:data.attach_id}, function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					obj.button('reset');
					errorTips(res.message);
				}
			});
		});
	},
	initDescShow: function(data) {
		const obj = $('#dealbox-desc');
		if (data) {
			obj.find('.dealbox-title').text('编辑描述文本');
		} else {
			data= {
				item_id: 0,
				name: '',
				value: '',
				sort: 0,
			};
			obj.find('.dealbox-title').text('增加描述文本');
		}
		for (const i in data) {
			obj.find('[name="'+i+'"').val(data[i]);
		}
		obj.dealboxShow();
	}
};