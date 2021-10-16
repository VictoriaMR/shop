$(function(){
	SITE.init();
});
const SITE = {
	init: function() {
		const _this = this;
		$('#site-page .btn.add-btn').on('click', function(){
			_this.initInfo('新增站点');
		});
		//编辑按钮
		$('#site-page .btn.modify').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			const id = obj.parents('tr').data('id');
			$.post(URI+'site', {opn: 'getInfo', id: id}, function(res){
				obj.button('reset');
				if (res.code === '200') {
					_this.initInfo('编辑站点', res.data);
				} else {
					errorTips(res.message);
				}
			});
		});
		//保存按钮
		$('#dealbox-info .btn.save-btn').on('click', function(){
			const obj = $(this);
			post(URI+'site', obj.parents('form').serializeArray(), function(){
				window.location.reload();
			});
		});
		//状态开关
		$('.switch_botton').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('tr').data('id');
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post(URI+'site', {opn: 'modifySite', id: id, status: status}, function(){
				_thisobj.switchBtn(status);
			});
		});
	},
	initInfo: function(title, data) {
		if (!data) {
			data = {
				site_id: 0,
				name: '',
				domain: '',
				path: '',
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