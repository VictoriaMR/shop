$(function(){
	CONTROLLERLIST.init();
});
const CONTROLLERLIST = {
	init: function() {
		const _this = this;
		//新增总控制器
		$('.add-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			_this.dealboxData({});
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
		//删除
		$('.btn.delete').on('click', function(){
			const id = $(this).parents('tr').data('id');
			confirm('确认删除功能吗?', function(_thisobj){
				_thisobj.button('loading');
				$.post(URI+'controller', {opn: 'deleteInfo', id: id}, function(res){
					if (res.code === '200') {
						successTips(res.message);
						window.location.reload();
					} else {
						_thisobj.button('reset');
						errorTips(res.message);
					}
				});
			});
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
		//排序
		$('#data-list [name="sort"]').on('blur', function(){
			const id = $(this).parents('tr').data('id');
			const sort = $(this).val();
			post(URI+'controller', {opn: 'sortInfo', id: id, sort: sort}, function(){
				window.location.reload();
			});
		});
	},
	dealboxData: function(data) {
		data = {
			con_id: data.con_id ? data.con_id : 0,
			name: data.name ? data.name : '',
			value: data.value ? data.value : '',
			icon: data.icon ? data.icon : '',
			sort: data.sort ? data.sort : '',
		};
		const obj = $('#dealbox');
		for (const i in data) {
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.find('.selectpicker').selectpicker('refresh');
		obj.dealboxShow();
	}
};