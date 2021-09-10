$(function(){
	PAYMENT.init();
});
const PAYMENT = {
	init: function(){
		const _this = this;
		//查看配置信息
		$('.glyphicon-eye-open').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			const id = _thisobj.parents('tr').data('id');
			$.post(URI+'payment', {opn: 'getInfo', id: id}, function(res){
				_thisobj.button('reset');
				if (res.code === '200') {
					_this.showViewModal(res.data);
				} else {
					errorTips(res.message);
				}
			});
		});
		//新增配置账号
		$('#add-data-btn').on('click', function(){
			_this.showEditModal();
		});
		//编辑账号
		$('.modify').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			const id = _thisobj.parents('tr').data('id');
			$.post(URI+'payment', {opn: 'getInfo', id: id}, function(res){
				_thisobj.button('reset');
				if (res.code === '200') {
					_this.showEditModal(res.data);
				} else {
					errorTips(res.message);
				}
			});
		});
		//保存账号配置
		$('#partEdit .btn.save').on('click', function(){
			const _thisobj = $(this);
			let check = true;
			$('#partEdit form [required="required"]').each(function(){
				if (!$(this).val()) {
					errorTips($(this).parents('.form-group').find('.control-label').text()+'不能未空!');
					$(this).focus();
					check = false;
					return false;
				}
			});
			if (!check) {
				return false;
			}
			_thisobj.button('loading');
			$.post(URI+'payment', $('#partEdit form').serializeArray(), function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					errorTips(res.message);
					_thisobj.button('reset');
				}
			});
		});
		//删除账号配置
		$('.delete').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			const id = _thisobj.parents('tr').data('id');
			$.post(URI+'payment', {opn: 'deleteInfo', id: id}, function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					_thisobj.button('reset');
					errorTips(res.message);
				}
			});
		});
		$('.switch_botton').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			const type = _thisobj.data('type');
			const id = _thisobj.parents('tr').data('id');
			$.post(URI+'payment', {opn: 'modifyInfo', id: id, [type]:status}, function(res){
				if (res.code === '200') {
					successTips(res.message);
					_thisobj.switchBtn(status);
				} else {
					errorTips(res.message);
				}
			});
		});
	},
	showViewModal: function(data) {
		if (!data) {
			return false;
		}
		for (const i in data) {
			$('#partView [name="'+i+'"]').val(data[i]);	
		}
		$('#partView').modal('show');
	},
	showEditModal: function(data) {
		if (!data) {
			data = {
				payment_id: 0,
				app_key: '',
				secret_key: '',
				webhook_key: '',
				is_sandbox: '1',
				status: '0',
				remark: '',
			};
		}
		for (const i in data) {
			if (i === 'is_sandbox' || i === 'status') {
				console.log(i, 'i')
				$('#partEdit [name="'+i+'"][value="'+data[i]+'"]').prop('checked', true);
			} else {
				$('#partEdit [name="'+i+'"]').val(data[i]);	
			}
		}
		$('#partEdit .modal-title').text(data.payment_id ? '编辑账号' : '添加账号');
		$('#partEdit').modal('show');
	}
};