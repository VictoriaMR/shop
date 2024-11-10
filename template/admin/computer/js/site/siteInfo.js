$(function(){
	SITEINFO.init();
});
const SITEINFO = {
	init: function() {
		const _this = this;
		_this.id = $('#site-page').data('id');
		//状态开关
		$('#site-status .switch_botton').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post(URI+'site', {opn: 'modifySite', id: _this.id, status: status}, function(res){
				showTips(res);
				if (res.code == 200) {
					_thisobj.switchBtn(status);
				}
			});
		});
		//站点名称
		$('#site-name').on('click', function(){
			$('#name-info').dealboxShow();
		});
		//站点模板
		$('#site-path').on('click', function(){
			$('#path-info').dealboxShow();
		});
		//添加语言弹窗
		$('#add-language').on('click', function(){
			$('#language-info').dealboxShow();
		});
		$('#add-currency').on('click', function(){
			$('#currency-info').dealboxShow();
		});
		//添加域名弹窗
		$('#add-domain').on('click', function(){
			_this.initDomainInfo('新增域名');
		});
		//编辑域名
		$('#site-domain .btn.modify').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('tr').data('id');
			_thisobj.button('loading');
			post(URI+'site', {opn: 'domainInfo', id: id}, function(res){
				if (res.code === 200) {
					_this.initDomainInfo('编辑域名', res.data);
				} else {
					showTips(res);
				}
				_thisobj.button('reset');
			});
		});
		//删除域名
		$('#site-domain .btn.delete').on('click', function(){
			const id = $(this).parents('tr').data('id');
			confirm('确定要删除这个域名吗?', function(obj){
				obj.button('loading');
				post(URI+'site', {opn: 'deleteDomain', id: id}, function(res){
					showTips(res);
					if (res.code === 200) {
						window.location.reload();
					} else {
						obj.button('reset');
					}
				});
			});
		});
		//滑动按钮
		$('#site-domain .switch_botton').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('tr').data('id');
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post(URI+'site', {opn: 'modifyDomain', id: id, status: status}, function(res){
				showTips(res);
				if (res.code === 200) {
					_thisobj.switchBtn(status);
				}
			});
		});
		//域名保存按钮
		$('#domain-info .btn.save-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			post(URI+'site', obj.parents('form').serializeArray(), function(res){
				showTips(res);
				if (res.code == 200) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
			});
		});
		//名称 模板确认
		$('#name-info .save-btn,#path-info .save-btn').on('click', function(){
			const _thisobj = $(this);
			let check = true;
			_thisobj.parent('form').find('[required="required"]').each(function(){
				if ($(this).val() === '') {
					check = false;
					errorTips($(this).prev().text()+'不能为空');
					return false;
				}
			});
			if (!check) {
				return false;
			}
			_thisobj.button('loading');
			post(URI+'site', _thisobj.parent('form').serializeArray(), function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				} else {
					_thisobj.button('reset');
				}
			});
		});
		//删除语言
		$('#language-info .btn.delete, #language-table .btn.delete').on('click', function(){
			const code = $(this).parents('tr').data('code');
			confirm('确认要删除该语言吗?', function(_thisobj){
				_thisobj.button('loading');
				post(URI+'site', {opn: 'deleteLanguage', code: code, site_id: _this.id}, function(res){
					showTips(res);
					if (res.code === 200) {
						window.location.reload();
					} else {
						_thisobj.button('reset');
					}
				});
			});
		});
		//增加语言
		$('#language-info .btn.add').on('click', function(){
			const _thisobj = $(this);
			const code = _thisobj.parents('tr').data('code');
			_thisobj.button('loading');
			post(URI+'site', {opn: 'addLanguage', code: code, site_id: _this.id}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				} else {
					_thisobj.button('reset');
				}
			});
		});
		//语言排序
		$('#language-table [name="sort"]').on('blur', function(){
			const id = $(this).parents('tr').data('id');
			const sort = $(this).val();
			post(URI+'site', {opn: 'sortLanguage', id: id, sort: sort}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				}
			});
		});
		//删除货币
		$('#currency-info .btn.delete, #site-currency .btn.delete').on('click', function(){
			const code = $(this).parents('tr').data('code');
			confirm('确认要删除该货币吗?', function(_thisobj){
				_thisobj.button('loading');
				post(URI+'site', {opn: 'deleteCurrency', code: code, site_id: _this.id}, function(res){
					showTips(res);
					if (res.code === 200) {
						window.location.reload();
					} else {
						_thisobj.button('reset');
					}
				});
			});
		});
		//增加货币
		$('#currency-info .btn.add').on('click', function(){
			const _thisobj = $(this);
			const code = _thisobj.parents('tr').data('code');
			_thisobj.button('loading');
			post(URI+'site', {opn: 'addCurrency', code: code, site_id: _this.id}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				} else {
					_thisobj.button('reset');
				}
			});
		});
		//语言排序
		$('#site-currency [name="sort"]').on('blur', function(){
			const id = $(this).parents('tr').data('id');
			const sort = $(this).val();
			post(URI+'site', {opn: 'sortCurrency', id: id, sort: sort}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				}
			});
		});
		//更新关键字
		$('.text-content textarea').on('blur', function(){
			const value = $(this).val();
			const name = this.name;
			post(URI+'site', {opn: 'updateKeyword', value: value, site_id: _this.id, name: name}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				}
			});
		});
		$('.text-content .btn').on('click', function(){
			const _thisobj = $(this);
			const type = _thisobj.parents('.item').data('id');
			$('#dealbox-language input[name="source_text"]').val(_thisobj.parents('.item').next().find('textarea').val());
			post(URI+'site', {opn: 'getLanguage', site_id: _this.id, type: type}, function(res){
				if (res.code == 200) {
					var data = res.data;
					const obj = $('#dealbox-language');
					obj.find('input[name="type"]').val(type);
					obj.find('table textarea').val('');
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
										<span>'+data[i].name2+'</span>\
									</th>\
									<td class="p0">\
										<textarea name="language['+data[i].code+']" data-tr_code="'+data[i].tr_code+'" class="form-control" autocomplete="off" rows="3">'+data[i].tr_text+'</textarea>\
									</td>\
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
			const name = $('#dealbox-language input[name="source_text"]').val();
			const thisobj = $(this);
			const obj = thisobj.parents('table').find('textarea');
			let len = obj.length;
			thisobj.button('loading');
			obj.each(function(){
				console.log($(this))
				const value = $(this).val();
				if (value === '') {
					const _thisobj = $(this);
					const tr_code = _thisobj.data('tr_code');
					post(URI+'site', {opn:'transfer', tr_code:tr_code, name:name}, function(res){
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
		//保存语言
		$('#dealbox-language .save-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			post(URI+'site', $('#dealbox-language form').serializeArray(), function(res){
				showTips(res);
				if (res.code == 200) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
			});
			return false;
		});
	},
	initDomainInfo: function(title, data)
	{
		if (!data) {
			data = {
				domain_id: 0,
				domain: '',
				status: 0,
				remark: '',
			};
		}
		const obj = $('#domain-info');
		for (const i in data) {
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.dealboxShow(title);
		return true;
	}
};