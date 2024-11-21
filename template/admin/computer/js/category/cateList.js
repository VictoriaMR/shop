$(function(){
	CATEGORYLIST.init();
});
const CATEGORYLIST = {
	init: function() {
		var _this = this;
		$('#dealbox .switch_botton').on('click', function(){
			let status = $(this).data('status');
			status = status == 0 ? 1 : 0;
			$(this).switchBtn(status);
			$(this).next().val(status);
		});
		//新增修改
		$('.btn.modify').on('click', function(event){
			event.stopPropagation();
			const btnobj = $(this);
			const id = btnobj.parents('.item').data('id');
			btnobj.button('loading');
			post(URI+'category/cateList', {opn: 'getCateInfo', cate_id: id}, function(res){
				if (res.code == 200) {
					_this.initData(res.data);
				} else {
					showTips(res);
				}
				btnobj.button('reset');
			});
		});
		//新增子分类
		$('.btn.add').on('click', function(event){
			event.stopPropagation();
			const btnobj = $(this);
			const id = btnobj.parents('.item').data('id');
			const data = {parent_id: id};
			_this.initData(data);
		});
		//更新数据
		$('.btn.update-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			post(URI+'category/cateList', {opn:'updateStat'}, function(res){
				showTips(res);
				obj.button('reset');
			});
		});
		//多语言配置
		$('.glyphicon-globe').on('click', function(event){
			event.stopPropagation();
			const _thisobj = $(this);
			const id = _thisobj.parents('.item').data('id');
			const type = _thisobj.data('type');
			post(URI+'category/cateList', {opn: 'getCateLanguage', cate_id: id, type: type}, function(res){
				if (res.code == 200) {
					var data = res.data;
					const obj = $('#dealbox-language');
					obj.find('input[name="cate_id"]').val(id);
					obj.find('input[name="type"]').val(type);
					var name = _thisobj.next().text();
					obj.find('input[name="cate_name"]').val(name);
					obj.find('.dealbox-title').text(name);
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
									<td class="p0">';
						if (type == 0) {
							html += '<input type="text" name="language['+data[i].lan_id+']" data-tr_code="'+data[i].tr_code+'" class="input transfer" value="'+data[i].name+'" autocomplete="off">';
						} else {
							html += '<textarea rows="3" type="text" name="language['+data[i].lan_id+']" data-tr_code="'+data[i].tr_code+'" class="form-control transfer" autocomplete="off">'+data[i].name+'</textarea>';
						}
						html += '</td>\
								</tr>';
					}
					obj.find('table tbody').html(html);
					obj.dealboxShow();

				} else {
					showTips(res);
				}
			});
		});
		//智能翻译
		$('#dealbox-language').on('click', '.glyphicon-transfer', function(){
			let name = $('#dealbox-language input[name="cate_name"]').val();
			if (!name) {
				var obj = $('#dealbox-language td').eq(0);
				if (obj.find('input').length > 0) {
					name = obj.find('input').val();
				} else {
					name = obj.find('textarea').val();
				}
			}
			const thisobj = $(this);
			var obj = thisobj.parents('table').find('.transfer');
			let len = obj.length;
			thisobj.button('loading');
			obj.each(function(){
				const value = $(this).val();
				if (value === '') {
					const _thisobj = $(this);
					const tr_code = _thisobj.data('tr_code');
					post(URI+'category/cateList', {opn:'transfer', tr_code:tr_code, name:name}, function(res){
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
		//保存数据
		$('#dealbox .save-btn').on('click', function(){
			const name = $('#dealbox form input[name="name"]').val();
			if (name == '') {
				errorTips('名称不能为空');
				return false;
			}
			const obj = $(this);
			obj.button('loading');
			post(URI+'category/cateList', $('#dealbox form').serializeArray(), function(res){
				showTips(res);
				if (res.code == 200) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
			});
		});
		//保存语言
		$('#dealbox-language .save-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			post(URI+'category/cateList', $('#dealbox-language form').serializeArray(), function(res){
				showTips(res);
				if (res.code == 200) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
			});
		});
		//保存排序
		$('.btn.sort-btn').on('click', function(){
			let data = {};
			let pid;
			const obj = $(this);
			obj.button('loading');
			$('#category-list table tr.item').each(function(){
				const lev = $(this).data('lev');
				const id = $(this).data('id');
				if (lev == 0) {
					pid = id+'-pid';
					if (!data[pid]) {
						data[pid] = new Array();
					}
				} else {
					data[pid].push(id);
				}
			});
			post(URI+'category/cateList', {opn: 'sortCategory', data: data}, function(res){
				showTips(res);
				if (res.code == 200) {
					obj.button('reset').addClass('disabled');
				} else {
					obj.button('reset');
				}
			});
		});
		//删除子分类
		$('.btn.delete').on('click', function(event){
			event.stopPropagation();
			const btnobj = $(this);
			const id = btnobj.parents('.item').data('id');
			confirm('确定要删除吗?', function(obj){
				obj.button('loading');
				post(URI+'category/cateList', {opn: 'deleteCategory', cate_id: id}, function(res){
					showTips(res);
					if (res.code == 200) {
						btnobj.parents('tr').remove();
						_this.sortInit();
						$('#dealbox').hide();
					}
					obj.button('reset');
				});
			});
		});
		//点击收起
		$('#category-list table .item').on('click', function(){
			const lev = $(this).data('lev');
			//获取下一个是否是自己下属
			if ($(this).next().data('lev') > lev) {
				if ($(this).next().is(':visible')) {
					$(this).nextUntil('[data-lev="'+lev+'"]').hide();
				} else {
					$(this).nextUntil('[data-lev="'+lev+'"]').show();
				}
			}
			$('.item[data-lev="0"]').show();
		});
		$('#data-list .avatar-hover img').imageUpload('category', function(data, obj){
			const id = obj.parents('tr').data('id');
			post(URI+'category/cateList', {opn: 'modifyCategory', id: id, attach_id: data.attach_id}, function(res){
				showTips(res);
			});
		});
		//状态
		$('#data-list .switch_botton').on('click', function(event){
			event.stopPropagation();
			var _thisobj = $(this);
			var param = {};
			var type = _thisobj.data('type');
			param.id = _thisobj.parents('tr').data('id');
			param[type] = _thisobj.data('status') == '1' ? '0' : '1';
			param.opn = 'modifyCategory';
			post(URI+'category/cateList', param, function(res){
				showTips(res);
				if (res.code === 200) {
					_thisobj.switchBtn(param[type]);
				}
			});
		});
	},
	initData: function(data) {
		const obj = $('#dealbox');
		if (data) {
			obj.find('input[name="cate_id"]').val(data.cate_id);
			obj.find('input[name="parent_id"]').val(data.parent_id);
			obj.find('input[name="name"]').val(data.name);
			obj.find('input[name="name_en"]').val(data.name_en);
			obj.find('input[name="image"]').val(data.avatar);
			obj.find('.form-category-img img').attr('src', data.avatar_format);
		} else {
			obj.find('input[name="cate_id"]').val(0);
			obj.find('input[name="parent_id"]').val(0);
			obj.find('input[name="name"]').val('');
			obj.find('input[name="name_en"]').val('');
			obj.find('input[name="image"]').val('');
		}
		obj.dealboxShow();
		return true;
	},
};