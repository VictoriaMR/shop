$(function(){
	CHECKOUTPAYORDER.init();
});
const CHECKOUTPAYORDER = {
	init: function() {
		//地址编辑
		$('.address-edit-btn').on('click', function(){
			ADDRESSBOOK.show();
			const orderId = $(this).data('order_id');
			const addressObj = $('#address-book .dialog');
			ADDRESSBOOK.setSaveCallback(function(data) {
				data.push({name: 'order_id', value: orderId});
				$.post(URI+'order/editOrderAddress', data, function(res){
					if (res.code === '200') {
						TIPS.success(res.message);
						setTimeout(function(){
							window.location.reload();
						}, 200);
					} else {
						TIPS.loadout(addressObj);
						TIPS.error(res.message);
					}
				});
			});
			TIPS.loading(addressObj);
			$.post(URI+'order/getOrderAddress', {order_id: orderId}, function(res){
				TIPS.loadout(addressObj, true);
				if (res.code === '200') {
					ADDRESSBOOK.pageInit(res.data);
				} else {
					ADDRESSBOOK.close();
					TIPS.error(res.message);
				}
			});
		});
	}
};