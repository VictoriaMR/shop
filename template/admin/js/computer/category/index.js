$(function(){
	CATEGORYLIST.init();
});
const CATEGORYLIST = {
	init: function() {
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
			CATEGORYLIST.loadData(id, function(data){
				CATEGORYLIST.initData(data);
				$('#dealbox').dealboxShow();
				btnobj.button('reset');
			});
		});
		//新增子分类
		$('.btn.add').on('click', function(event){
			event.stopPropagation();
			const btnobj = $(this);
			const id = btnobj.parents('.item').data('id');
			const data = {parent_id: id};
			CATEGORYLIST.initData(data);
			$('#dealbox').dealboxShow();
		});
		//更新数据
		$('.btn.update-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			post(URI+'category', {opn:'updateStat'}, function(){
				obj.button('reset');
			});
		});
		//多语言配置
		$('.glyphicon-globe').on('click', function(event){
			event.stopPropagation();
			const _thisobj = $(this);
			const id = _thisobj.parents('.item').data('id');
			post(URI+'category', {opn: 'getCateLanguage', cate_id: id}, function(data){
				const obj = $('#dealbox-language');
				obj.find('input[name="cate_id"]').val(id);
				obj.find('input[name="cate_name"]').val(_thisobj.next().text());
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
									<input type="text" name="language['+data[i].lan_id+']" data-tr_code="'+data[i].tr_code+'" class="input" value="'+data[i].name+'" autocomplete="off">\
								</td>\
							</tr>';
				}
				obj.find('table tbody').html(html);
				obj.dealboxShow();
			});
		});
		//智能翻译
		$('#dealbox-language').on('click', '.glyphicon-transfer', function(){
			const name = $('#dealbox-language input[name="cate_name"]').val();
			const thisobj = $(this);
			const obj = thisobj.parents('table').find('.input');
			let len = obj.length;
			thisobj.button('loading');
			obj.each(function(){
				const value = $(this).val();
				if (value === '') {
					const _thisobj = $(this);
					const tr_code = _thisobj.data('tr_code');
					$.post(URI+'category', {opn:'transfer', tr_code:tr_code, name:name}, function(res){
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
			const name = $('#dealbox form input[name="name"]').val();
			if (name == '') {
				errorTips('名称不能为空');
				return false;
			}
			const obj = $(this);
			obj.button('loading');
			post(URI+'category', $('#dealbox form').serializeArray(), function(){
				window.location.reload();
			});
		});
		//保存语言
		$('#dealbox-language .save-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			post(URI+'category', $('#dealbox-language form').serializeArray(), function(){
				obj.button('reset');
				window.location.reload();
			});
			return false;
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
			post(URI+'category', {opn: 'sortCategory', data: data}, function(){
				obj.button('reset').addClass('disabled');
			});
		});
		//删除子分类
		$('.btn.delete').on('click', function(event){
			event.stopPropagation();
			const btnobj = $(this);
			const id = btnobj.parents('.item').data('id');
			confirm('确定要删除吗?', function(obj){
				obj.button('loading');
				post(URI+'category', {opn: 'deleteCategory', cate_id: id}, function(){
					obj.button('reset');
					btnobj.parents('tr').remove();
					CATEGORYLIST.sortInit();
				}, function(){
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
			$.post(URI+'category', {opn: 'modifyCategory', id: id, attach_id: data.attach_id}, function(res){
				if (res.code === '200') {
					successTips(res.message);
				} else {
					errorTips(res.message);
				}
			});
		});
		//状态
		$('#data-list .switch_botton').on('click', function(event){
			event.stopPropagation();
			var _thisobj = $(this);
			let param = {};
			param.id = _thisobj.parents('tr').data('id');
			param[_thisobj.data('type')] = _thisobj.data('status') == '1' ? '0' : '1';
			param.opn = 'modifyCategory';
			$.post(URI+'category', param, function(res){
				if (res.code === '200') {
					successTips(res.message);
					_thisobj.switchBtn(param.status);
				} else {
					errorTips(res.message);
				}
			});
		});
	},
	loadData: function(id, callback) {
		if (id) {
			post(URI+'category', {opn: 'getCateInfo', cate_id: id}, function(data){
				callback(data);
			});
		} else {
			callback({});
		}
	},
	initData: function(data) {
		const obj = $('#dealbox');
		if (data) {
			obj.find('input[name="cate_id"]').val(data.cate_id);
			obj.find('input[name="parent_id"]').val(data.parent_id);
			obj.find('input[name="name"]').val(data.name);
			obj.find('input[name="image"]').val(data.avatar);
			obj.find('.form-category-img img').attr('src', data.avatar_format);
		} else {
			obj.find('input[name="cate_id"]').val(0);
			obj.find('input[name="parent_id"]').val(0);
			obj.find('input[name="name"]').val('');
			obj.find('input[name="image"]').val('');
		}
		return true;
	},
};