$(function(){
	MEMBERLIST.init();
});
const MEMBERLIST = {
	init: function() {
		$('#add-data-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			MEMBERLIST.initDealbox(0, function(){
				obj.button('reset');
			});
		});
		//保存按钮
		$('#dealbox .btn.save').on('click', function(){
			if (!$(this).parents('form').formFilter()) {
				return false;
			}
			post(URI+'member', $(this).parents('form').serializeArray(), function(data){
				window.location.reload();
			});
		});
		$('#dealbox .switch_botton').on('click', function(){
			let status = $(this).data('status');
			status = status == 0 ? 1 : 0;
			$(this).switchBtn(status);
			$(this).next().val(status);
		});
		//改变状态按钮
		$('#data-list .switch_botton').on('click', function(){
			const obj = $(this);
			const status = obj.data('status') == 0 ? 1 : 0;
			post(URI+'member', {opn:'modify', mem_id: $(this).parents('tr').data('id'), status: status}, function(data) {
				obj.switchBtn(status);
			});
		});
		//修改
		$('#data-list .btn.modify').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			MEMBERLIST.initDealbox(obj.parents('tr').data('id'), function(){
				obj.button('reset');
			});
		});
	},
	initDealbox: function(mem_id, callback) {
		if (mem_id) {
			post(URI+'member', {opn:'getInfo', mem_id: mem_id}, function(data) {
				MEMBERLIST.dealboxData(data, callback);
			});
		} else {
			MEMBERLIST.dealboxData({}, callback);
		}
	},
	dealboxData: function(data, callback) {
		const obj = $('#dealbox');
		obj.find('input:not(.no_replace)').val('');
		if (data) {
			obj.find('.dealbox-title').text('编辑管理员');
			for (const i in data) {
				obj.find('[name="'+i+'"]').val(data[i]);
				obj.find('[name="'+i+'"]:not(.no_show)').show();
			}
		} else {
			obj.find('.dealbox-title').text('新增管理员');
			obj.find('input[name="salt"]').hide();
		}
		let status = 0;
		if (typeof data.status !== 'undefinded') {
			status = data.status;
		}
		obj.find('.switch_botton').switchBtn(status);
		obj.dealboxShow();
		if (callback) {
			callback();
		}
	}
};