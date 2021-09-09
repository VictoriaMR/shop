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
				if (res.code === '200') {
					_this.showModal(res.data);
					_thisobj.button('reset');
				} else {
					errorTips(res.message);
				}
			});
		});
	},
	showModal: function(data) {
		if (!data) {
			return false;
		}
		for (const i in data) {
			$('#partView [name="'+i+'"]').val(data[i]);	
		}
		$('#partView').modal('show');
	}
};