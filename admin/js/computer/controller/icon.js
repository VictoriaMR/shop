$(function(){
	CONTROLLERLIST.init();
});
const CONTROLLERLIST = {
	init: function() {
		const _this = this;
		//新增图标
		$('.add-btn').on('click', function(){
			const obj = $(this);
			obj.button('loading');
			_this.dealboxData({});
		});
		//编辑
		$('.btn.modify').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('tr').data('id');
			_thisobj.button('loading');
			$.post(URI+'controller/icon', {opn: 'getIconInfo', id: id}, function(res){
				_thisobj.button('reset');
				if (res.code === '200') {
					_this.dealboxData(res.data);
				} else {
					errorTips(res.message);
				}
			});
		});
		//删除
		$('.btn.delete').on('click', function(){
			const id = $(this).parents('tr').data('id');
			confirm('确认删除图标吗?', function(_thisobj){
				_thisobj.button('loading');
				$.post(URI+'controller/icon', {opn: 'deleteIconInfo', id: id}, function(res){
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
			$.post(URI+'controller/icon', _thisobj.parent().serializeArray(), function(res){
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
			post(URI+'controller/icon', {opn: 'sortIconInfo', id: id, sort: sort}, function(){
				window.location.reload();
			});
		});
	},
	dealboxData: function(data) {
		data = {
			icon_id: data.icon_id ? data.icon_id : 0,
			name: data.name ? data.name : '',
			remark: data.remark ? data.remark : '',
			sort: data.sort ? data.sort : '',
		};
		const obj = $('#dealbox');
		for (const i in data) {
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.dealboxShow();
	}
};