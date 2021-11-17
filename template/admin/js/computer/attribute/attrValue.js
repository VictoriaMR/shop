$(function(){
	ATTRIBUTE.init();
});
const ATTRIBUTE = {
	init: function() {
		const _this = this;
		//新增修改
		$('.btn.modify').on('click', function(){
			var btnobj = $(this);
			var id = btnobj.parents('.item').data('id');
			btnobj.button('loading');
			_this.loadData(id, function(data){
				_this.initData(data);
				$('#dealbox').dealboxShow();
				btnobj.button('reset');
			});
		});
		//多语言配置
		$('.glyphicon-globe').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('.item').data('id');
			post(URI+'attribute/attrValue', {opn: 'getAttvLanguage', id: id}, function(data){
				const obj = $('#dealbox-language');
				obj.find('input[name="id"]').val(id);
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
									<input type="text" name="language['+i+']" data-tr_code="'+data[i].tr_code+'" class="input" value="'+data[i].name+'" autocomplete="off">\
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
			const obj = thisobj.parents('table').find('.input');
			let len = obj.length;
			thisobj.button('loading');
			obj.each(function(){
				const value = $(this).val();
				if (value === '') {
					const _thisobj = $(this);
					const tr_code = _thisobj.data('tr_code');
					$.post(URI+'attribute/attrValue', {opn:'transfer', tr_code:tr_code, name:name}, function(res){
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
		//保存数据
		$('#dealbox .save-btn').on('click', function(){
			var name = $('#dealbox form input[name="name"]').val();
			if (name == '') {
				errorTips('名称不能为空');
				return false;
			}
			var obj = $(this);
			obj.button('loading');
			post(URI+'attribute/attrValue', $('#dealbox form').serializeArray(), function(){
				window.location.reload();
			});
		});
		//保存语言
		$('#dealbox-language .save-btn').on('click', function(){
			var obj = $(this);
			obj.button('loading');
			post(URI+'attribute/attrValue', $('#dealbox-language form').serializeArray(), function(){
				obj.button('reset');
				window.location.reload();
			});
			return false;
		});
		//删除
		$('.btn.delete').on('click', function(){
			var btnobj = $(this);
			var id = btnobj.parents('.item').data('id');
			confirm('确定要删除吗?', function(obj){
				obj.button('loading');
				post(URI+'attribute/attrValue', {opn: 'deleteAttvInfo', id: id}, function(){
					window.location.reload();
				}, function(){
					obj.button('reset');
				});
			});
		});
	},
	loadData: function(id, callback) {
		if (id) {
			post(URI+'attribute/attrValue', {opn: 'getAttvInfo', id: id}, function(data){
				callback(data);
			});
		} else {
			callback({});
		}
	},
	initData: function(data) {
		var obj = $('#dealbox');
		if (data) {
			obj.find('input[name="id"]').val(data.attv_id);
			obj.find('input[name="name"]').val(data.name);
		} else {
			obj.find('input[name="id"]').val(0);
			obj.find('input[name="name"]').val('');
		}
		return true;
	},
};