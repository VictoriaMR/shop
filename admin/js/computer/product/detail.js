$(function(){
	PRODUCT.init();
});
const PRODUCT = {
	init: function(){
		$('.status-btn').on('click', function(){
			$('#status-dealbox').dealboxShow();
		});
		$('.category-btn').on('click', function(){
			$('#category-dealbox').dealboxShow();
		});
		$('.centerShow .btn.save').on('click', function(){
			const _thisobj = $(this);
			post(URI+'product/detail', _thisobj.parent().serializeArray(), function(res) {
				window.location.reload();
			}, function(res) {
				_thisobj.parents('.centerShow').parent().dealboxHide();
			});
		});
		//免邮按钮点击
		$('.switch_botton.free-ship').on('click', function(){
			const _thisobj = $(this);
			const status = _thisobj.data('status') == '0' ? 1 : 0;
			post(URI+'product/detail', {spu_id: $('.detail-page').data('id'), free_ship: status, is_ajax: 1, opn: 'editInfo'}, function(res) {
				_thisobj.switchBtn(status);
			});
		});
		//名称翻译
		
	}
};