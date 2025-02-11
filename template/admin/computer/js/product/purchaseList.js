$(function(){
	OPERATELIST.init();
});
const OPERATELIST = {
	init: function(){
		const _this = this;
		// 编辑按钮
		$('.btn-edit').on('click', function(){
			_this.initModal($(this).parents('tr').data());
		});
		// 保存
		$('.edit-status-modal .btn-save').on('click', function(){
			if ($('.edit-status-modal [name="status"]').val() < 0) {
				errorTips('请选择状态');
				return false;
			}
			var thisObj = $(this);
			thisObj.button('loading');
			post('', $('.edit-status-modal').serializeArray(), function(res){
				showTips(res);
				if (res.code) {
					setTimeout(function(){
						window.location.reload();
					}, 500);
				} else {
					thisObj.button('reset');
				}
			});
		});
	},
	initModal: function(data) {
		var obj = $('.edit-status-modal');
		for (var i in data) {
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.modalShow();
	}
};