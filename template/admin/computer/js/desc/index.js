$(function(){
	DESC_NAME.init();
});
const DESC_NAME = {
	url: '',
	init: function() {
		const _this = this;
		// 状态筛选
		$('.status-filter .btn').on('click', function(){
			const form = $(this).parents('form');
			form.find('input[name="status"]').val($(this).data('status'));
			form.submit();
		});
		// 新增
		$('.btn.add').on('click', function(){
			_this.initData();
		});
		// 修改
		$('.btn.modify').on('click', function(){
			const btnobj = $(this);
			const id = btnobj.parents('.item').data('id');
			btnobj.button('loading');
			post(_this.url, {opn: 'getDescInfo', id: id}, function(res){
				btnobj.button('reset');
				if (res.code == 1) {
					_this.initData(res.data);
				} else {
					showTips(res);
				}
			});
		});
		// 多语言配置
		$('.btn.language').on('click', function(){
			const btnobj = $(this);
			const item = btnobj.parents('.item');
			btnobj.button('loading');
			post(_this.url, {opn: 'getDescLanguage', id: item.data('id')}, function(res){
				btnobj.button('reset');
				if (res.code == 1) {
					_this.initLanguage(item.data('id'), item.data('name'), res.data);
				} else {
					showTips(res);
				}
			});
		});
		// 智能翻译 (仅翻译空白项)
		$('#dealbox-language').on('click', '.glyphicon-transfer', function(){
			const thisobj = $(this);
			const name = $('#dealbox-language input[name="name"]').val();
			const empty = $('#dealbox-language .language-list .input').filter(function(){
				return $(this).val() === '';
			});
			let len = empty.length;
			if (len === 0) return;
			thisobj.button('loading');
			empty.each(function(){
				const input = $(this);
				post(_this.url, {opn: 'transfer', tr_code: input.data('tr_code'), name: name}, function(res){
					if (res.code == 1) {
						input.val(res.data);
					} else {
						showTips(res);
					}
					len--;
					if (len === 0) {
						thisobj.button('reset');
					}
				});
			});
		});
		// 保存数据
		$('#dealbox .save-btn').on('click', function(){
			const form = $('#dealbox form');
			if ($.trim(form.find('[name="name"]').val()) === '') {
				errorTips('名称不能为空');
				return false;
			}
			_this.submit($(this), form);
		});
		// 保存多语言
		$('#dealbox-language .save-btn').on('click', function(){
			_this.submit($(this), $('#dealbox-language form'));
		});
		// 删除
		$('.btn.delete').on('click', function(){
			const id = $(this).parents('.item').data('id');
			confirm('确定要删除吗?', function(obj){
				obj.button('loading');
				post(_this.url, {opn: 'deleteDescInfo', id: id}, function(res){
					showTips(res);
					if (res.code == 1) {
						window.location.reload();
					} else {
						obj.button('reset');
					}
				});
			});
		});
	},
	submit: function(btn, form) {
		btn.button('loading');
		post(this.url, form.serializeArray(), function(res){
			showTips(res);
			if (res.code == 1) {
				window.location.reload();
			} else {
				btn.button('reset');
			}
		});
	},
	initData: function(data) {
		data = data || {descn_id: 0, name: ''};
		const obj = $('#dealbox');
		obj.find('input[name="id"]').val(data.descn_id);
		obj.find('[name="name"]').val(data.name);
		obj.dealboxShow();
		obj.find('[name="name"]').focus();
	},
	initLanguage: function(id, name, data) {
		const obj = $('#dealbox-language');
		obj.find('input[name="id"]').val(id);
		obj.find('input[name="name"]').val(name);
		const list = obj.find('.language-list').empty();
		$.each(data, function(lanId, item){
			const input = $('<input type="text" class="input form-control" maxlength="255" autocomplete="off">')
				.attr('name', 'language[' + lanId + ']')
				.attr('data-tr_code', item.tr_code)
				.val(item.name);
			$('<div class="input-group">')
				.append($('<div class="input-group-addon">').text(item.language_name + '：'))
				.append(input)
				.appendTo(list);
		});
		obj.dealboxShow();
	},
};