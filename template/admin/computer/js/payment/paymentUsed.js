$(function(){
	PAYMENTUSED.init();
});
const PAYMENTUSED = {
	init: function() {
		const _this = this;
		//新增使用
		$('#add-data-btn').on('click', function(){
			_this.showEditModal();
		});
		//编辑按钮
		$('.modify').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			const id = _thisobj.parents('tr').data('id');
			post(URI+'payment/paymentUsed', {opn: 'getUsedInfo', id: id}, function(res){
				if (res.code === 200) {
					_this.showEditModal(res.data);
				} else {
					showTips(res);
				}
				_thisobj.button('reset');
			});
		});
		//保存按钮
		$('#dealbox .save').on('click', function(){
			let check = true;
			$('#dealbox form [required="required"]').each(function(){
				if (!$(this).val()) {
					check = false;
					errorTips($(this).parent('.input-group').find('.input-group-addon').text()+'必选');
					return false;
				}
			});
			if (!check) {
				return false;
			}
			const _thisobj = $(this);
			_thisobj.button('loading');
			post(URI+'payment/paymentUsed', $('#dealbox form').serializeArray(), function(res) {
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				} else {
					_thisobj.button('reset');
				}
			});
		});
		//删除按钮
		$('.delete').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			const id = _thisobj.parents('tr').data('id');
			post(URI+'payment/paymentUsed', {opn: 'deleteUsedInfo', id: id}, function(res){
				showTips(res);
				if (res.code === 200) {
					window.location.reload();
				} else {
					_thisobj.button('reset');
				}
			});
		});
	},
	showEditModal: function(data) {
		if (!data) {
			data = {
				item_id: 0,
				site_id: '',
				type: '',
				payment_id: '',
			};
		}
		var obj = $('#dealbox');
		for (const i in data) {
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.find('.dealbox-title').text(data.item_id ? '编辑使用' : '新增使用');
		obj.dealboxShow();
	}
};