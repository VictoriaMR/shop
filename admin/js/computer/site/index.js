$(function(){
	SITE.init();
});
const SITE = {
	init: function() {
		$('#site-page .btn.add-btn').on('click', function(){
			SITE.initInfo('新增');
		});
		//编辑按钮
		$('#site-page .btn.modify').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			const id = obj.parents('tr').data('id');
			post(URI+'site', {opn: 'getInfo', id: id}, function(data){
				obj.button('reset');
				SITE.initInfo('编辑', data);
			});
		});
		//保存按钮
		$('#dealbox-info .btn.save-btn').on('click', function(){
			const obj = $(this);
			post(URI+'site', obj.parents('form').serializeArray(), function(){
				window.location.reload();
			});
		});
		$('#site-page .glyphicon-globe').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			const name = obj.data('id');
			const id = obj.parents('tr').data('id');
			const value = obj.next().text();
	    	post(URI+'site', {opn: 'getSiteLanguage', name: name, id: id}, function(data){
	    		obj.button('reset');
	    		const showObj = $('#dealbox-language');
	    		showObj.find('input[name="name"]').val(name);
	    		showObj.find('input[name="value"]').val(value);
	    		showObj.find('input[name="site_id"]').val(id);
	    		showObj.find('table textarea').val('');
	    		for (const i in data) {
	    			showObj.find('table textarea[name="language['+data[i].lan_id+']"]').val(data[i].value);
	    		}
	    		showObj.dealboxShow();
			});
		});
		//保存语言
	    $('#dealbox-language .save-btn').on('click', function(){
	    	const obj = $(this);
	    	obj.button('loading');
	    	post(URI+'site', $('#dealbox-language form').serializeArray(), function(){
	    		obj.button('reset');
	    		$('#dealbox-language').dealboxHide();
	    	});
	    	return false;
	    });
	    //自动翻译
	    $('#dealbox-language .glyphicon-transfer').on('click', function() {
	    	const value = $(this).parents('form').find('[name="value"]').val();
	    	$(this).parents('form').find('table tr').each(function(){
	    		const obj = $(this);
	    		const code = obj.data('id');
	    		const val = obj.find('textarea').val();
	    		if (code && !val) {
	    			post(URI+'site', {opn: 'getTransfer', value: value, code: code}, function(data) {
			    		if (data) {
			    			obj.find('textarea').val(data);
			    		}
			    	});
	    		}
	    	});
	    });
	},
	initInfo: function(title, data) {
		if (!data) {
			data = {
				site_id: 0,
				name: '',
				domain: '',
				title: '',
				keyword: '',
				description: '',
			};
		}
		const obj = $('#dealbox-info');
		for (const i in data) {
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.dealboxShow(title);
		return true;
	}
};