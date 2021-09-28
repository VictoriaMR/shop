$(function(){
	FUNCTIONLIST.init();
});
const FUNCTIONLIST = {
	init: function() {
		const _this = this;
		//新增总控制器
		$('.add-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			_this.dealboxData();
		});
		//编辑控制器
		$('.btn.modify').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('tr').data('id');
			_thisobj.button('loading');
			$.post(URI+'controller', {opn: 'getInfo', id: id}, function(res){
				_thisobj.button('reset');
				if (res.code === '200') {
					_this.dealboxData(res.data);
				} else {
					errorTips(res.message);
				}
			});
		});
		//添加子控制器
		$('.btn.add').on('click', function(){
			const id = $(this).parents('tr').data('id');
			_this.dealboxData({parent_id: id});
		});
		//保存
		$('#dealbox .save-btn').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			$.post(URI+'controller', _thisobj.parent().serializeArray(), function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					_thisobj.button('reset');
					errorTips(res.message);
				}
			});
		});
	},
	dealboxData: function(data) {
		if (!data) {
			data = {
				con_id: 0,
				name: '',
				value: '',
				icon: '',
				sort: 0,
			};
		}
		const obj = $('#dealbox');
		for (const i in data) {
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.find('.selectpicker').selectpicker('refresh');
		obj.dealboxShow();
	}
};