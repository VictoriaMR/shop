const CATEGORYLIST = {
	init: function() {
		$('#dealbox .switch_botton').on('click', function(){
			var status = $(this).data('status');
			status = status == 0 ? 1 : 0;
			$(this).switchBtn(status);
			$(this).next().val(status);
		});
		//新增修改
		$('.btn.modify').on('click', function(){
			var btnobj = $(this);
			var id = btnobj.parents('.item').data('id');
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
			var btnobj = $(this);
			var id = btnobj.parents('.item').data('id');
			var data = {parent_id: id};
			CATEGORYLIST.initData(data);
			$('#dealbox').dealboxShow();
		});
		//更新数据
		$('.btn.update-btn').on('click', function(){
			var obj = $(this);
			obj.button('loading');
			post(URI+'category', {opn:'updateStat'}, function(){
				obj.button('reset');
			});
		});
		//多语言配置
		$('.glyphicon-globe').on('click', function(){
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
			var name = $('#dealbox form input[name="name"]').val();
			if (name == '') {
				errorTips('名称不能为空');
				return false;
			}
			var obj = $(this);
			obj.button('loading');
			post(URI+'category', $('#dealbox form').serializeArray(), function(){
				window.location.reload();
			});
		});
		//保存语言
		$('#dealbox-language .save-btn').on('click', function(){
			var obj = $(this);
			obj.button('loading');
			post(URI+'category', $('#dealbox-language form').serializeArray(), function(){
				obj.button('reset');
				window.location.reload();
			});
			return false;
		});
		//保存排序
		$('.btn.sort-btn').on('click', function(){
			var data = {};
			var pid;
			var obj = $(this);
			obj.button('loading');
			$('#category-list table tr.item').each(function(){
				var lev = $(this).data('lev');
				var id = $(this).data('id');
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
		$('.btn.delete').on('click', function(){
			var btnobj = $(this);
			var id = btnobj.parents('.item').data('id');
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

		// this.sortInit();
		// this.sortClick();
		
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
		var obj = $('#dealbox');
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
	sortInit: function() {
		var trobj = $('#category-list table tr.item');
		trobj.find('.glyphicon').removeClass('disabled');
		$('#category-list table tr.item').each(function(index, item) {
			var obj = $(this);
			var lev = obj.data('lev');
			if (lev == 0) {
				if (obj.prevAll('[data-lev="0"]').length == 0) {
					obj.find('.glyphicon-arrow-up,.glyphicon-chevron-up').addClass('disabled');
				}
				if (obj.nextAll('[data-lev="0"]').length == 0) {
					obj.find('.glyphicon-arrow-down,.glyphicon-chevron-down').addClass('disabled');
				}
			} else {
				var prevLev = obj.prev().data('lev');
				var nextLev = obj.next().data('lev');
				if (lev !== prevLev) {
					obj.find('.glyphicon-arrow-up,.glyphicon-chevron-up').addClass('disabled');
				}
				if (lev !== nextLev) {
					obj.find('.glyphicon-arrow-down,.glyphicon-chevron-down').addClass('disabled');
				}
			}
		});
	},
	sortClick: function() {
		$('.sort-btn-content .glyphicon').on('click', function(){
			if ($(this).hasClass('disabled')) return false;
			$('.btn.sort-btn').removeClass('disabled');
			var obj = $(this).parents('tr');
			var sort = $(this).data('sort');
			var lev = obj.data('lev');
			if (lev == 0) {
				var removeObj = obj.nextUntil('[data-lev="'+lev+'"]');
				if (sort == 'top') {
					$('[data-lev="0"]:first').before(obj);
				} else if (sort == 'up') {
					obj.prevAll('[data-lev="0"]').eq(0).before(obj);
				} else if (sort == 'down') {
					obj.nextAll('[data-lev="0"]').eq(0).nextUntil('[data-lev="0"]').eq(-1).after(obj);
				} else if (sort == 'bottom') {
					$('[data-lev="0"]:last').nextUntil('[data-lev="0"]').eq('-1').after(obj);
				}
				obj.after(removeObj);
			} else {
				if (sort == 'top') {
					obj.prevAll('[data-lev="'+lev+'"]').eq(-1).before(obj);
				} else if (sort == 'up') {
					obj.prev().before(obj);
				} else if (sort == 'down') {
					obj.next().after(obj);
				} else if (sort == 'bottom') {
					obj.nextUntil('[data-lev="0"]').eq(-1).after(obj);
				}
			}
			CATEGORYLIST.sortInit();
		});
	},
};