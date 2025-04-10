/* member_index */
$(function(){
	MEMBERLIST.init();
});
const MEMBERLIST = {
	init: function() {
		var _this = this;
		$('#add-data-btn').on('click', function(){
			_this.dealboxData({});
		});
		//保存按钮
		$('#dealbox .btn.save').on('click', function(){
			const obj = $(this);
			if (!obj.parents('form').formFilter()) {
				return false;
			}
			obj.button('loading');
			post(URI+'member', $(this).parents('form').serializeArray(), function(res){
				showTips(res);
				if (res.code == 200) {
					window.location.reload();
				} else {
					obj.button('reset');
				}
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
			post(URI+'member', {opn:'modify', mem_id: $(this).parents('tr').data('id'), status: status}, function(res) {
				showTips(res);
				if (res.code == 200) {
					obj.switchBtn(status);
				}
			});
		});
		//修改
		$('#data-list .btn.modify').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			post(URI+'member', {opn:'getInfo', mem_id: obj.parents('tr').data('id')}, function(res) {
				if (res.code == 200) {
					_this.dealboxData(res.data);
				} else {
					showTips(res);
				}
				obj.button('reset');
			});
		});
	},
	dealboxData: function(data) {
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
	}
};