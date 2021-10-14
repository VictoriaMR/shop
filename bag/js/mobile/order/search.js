$(function(){
	ORDERSEARCH.init();
});
const ORDERSEARCH = {
	init: function() {
		// 滚动条居中
		const _this = this;
		//滚动翻页
		if ($('.order-list').length > 0 && $('.order-list').height() > $(window).height()) {
			_this.initLoad();
		}
		//重新采购
		$('.order-list').on('click', '.repurchase-btn', function(){
			const id = $(this).parents('.item').data('id');
			TIPS.confirm(appT('order_repurchase_confirm'), function(obj){
				TIPS.loadingBtn(obj);
				$.post(URI+'order/repurchase', {id: id}, function(res){
					if (res.code === '200') {
						window.location.href = res.data.url;
					} else {
						TIPS.error(res.message);
						TIPS.loadoutBtn(obj);
					}
				});
			});
		});
		//删除
		$('.order-list').on('click', '.delete-btn', function(){
			const id = $(this).parents('.item').data('id');
			TIPS.confirm(appT('order_delete_confirm'), function(obj){
				TIPS.loadingBtn(obj);
				$.post(URI+'order/delete', {id: id}, function(res){
					if (res.code === '200') {
						window.location.reload();
					} else {
						TIPS.error(res.message);
						TIPS.loadoutBtn(obj);
					}
				});
			});
		});
		//退款
		$('.order-list').on('click', '.refund-btn', function(){
			const id = $(this).parents('.item').data('id');
			TIPS.confirm(appT('order_refund_confirm'), function(obj){
				TIPS.loadingBtn(obj);
				$.post(URI+'order/refund', {id: id}, function(res){
					if (res.code === '200') {
						window.location.reload();
					} else {
						TIPS.error(res.message);
						TIPS.loadoutBtn(obj);
					}
				});
			});
		});
		//完成
		$('.order-list').on('click', '.complete-btn', function(){
			const id = $(this).parents('.item').data('id');
			TIPS.confirm(appT('order_complete_confirm'), function(obj){
				TIPS.loadingBtn(obj);
				$.post(URI+'order/complete', {id: id}, function(res){
					if (res.code === '200') {
						window.location.reload();
					} else {
						TIPS.error(res.message);
						TIPS.loadoutBtn(obj);
					}
				});
			});
		});
		//取消订单
		$('.order-list').on('click', '.cancel-btn', function(){
			const id = $(this).parents('.item').data('id');
			TIPS.confirm(appT('order_cancel_confirm'), function(obj){
				TIPS.loadingBtn(obj);
				$.post(URI+'order/cancel', {id: id}, function(res){
					if (res.code === '200') {
						window.location.reload();
					} else {
						TIPS.error(res.message);
						TIPS.loadoutBtn(obj);
					}
				});
			});
		});
	},
	initLoad: function() {
		const _this = this;
		_this.stop = true;
		$(window).scroll(function() {
			if (_this.stop) {
				const scrollTop = $(this).scrollTop();
				const scrollHeight = $(document).height();
				const windowHeight = $(this).height();
				if (scrollTop + windowHeight >= scrollHeight - 20) {
					_this.stop = false;
					$('.order-list').append('<div class="page-loading-block"><div></div><div></div><div></div></div>');
					_this.getPgae();
				}
			}
		});
	},
	getPgae: function() {
		const _this = this;
		const obj = $('.order-list');
		const page = parseInt(obj.data('page')) + 1;
		const size = parseInt(obj.data('size'));
		const keyword = $('.order-search-content [name="keyword"]').val();
		$.post(URI+'order/getSearchOrderListAjax', {page:page, size:size, keyword: keyword}, function(res){
			if (res.code === '200') {
				obj.data('page', page);
				$('.order-list').find('.page-loading-block').remove();
				if (res.data.length > 0) {
					if (res.data.length >= size) {
						_this.stop = true;
					}
					let html = '';
					const data = res.data;
					for (let i=0; i<data.length; i++) {
						const order = data[i];
						html += `<div class="item" data-id="`+order.order_id+`">
							<a class="block" href="`+order.url+`">
								<p class="status-title">`+order.status_text+`</p>
								<div class="mt8 c6">
									<p class="left e1 w50">`+order.add_time_format+`</p>
									<p class="e1 w50 right tr">`+appT('no')+`: `+order.order_no+`</p>
									<div class="clear"></div>
								</div>
								<div class="order-product-content">`;
						for (let j=0; j<order.product.length; j++) {
							const product = order.product[j];
							html += `<div class="product-item mt12">
										<div class="table">
											<div class="image tcell">
												<div class="image-tcell tcell">
													<img src="`+URI+`/image/common/noimg.svg" data-src="`+product.image+`" class="lazyload">
												</div>
											</div>
											<div class="info tcell">
												<p class="e2 product-name">`+product.name+`</p>
												<div class="field-row">
													<div class="attr-content">`;
											for (let k=0; k<product.attr.length; k++){
												const attr = product.attr[k];
												if (attr.image) {
													html += `<div class="attr-item attr-image">
															<img src="`+URI+`/image/common/noimg.svg" data-src="`+attr.image+`" class="lazyload">
															<span class="e1">`+attr.attv_name+`</span>
															<div class="clear"></div>
														</div>`;
												} else {
													html += `<div class="attr-item">
															<span class="e1">`+attr.attv_name+`</span>
														</div>`;
												}
											}
											html += `</div>
													<div class="edit-content">
														<div class="product-price">
															<p class="price e1">`+data[i].currency_symbol+product.price+`</p>
														</div>
														<div class="quantity tcell w25 tr">
															<button class="quantity-num">x `+product.quantity+`</button>
														</div>
													</div>
												</div>
											</div>
										</div>
								</div>`;
						}
						html += `</div>
							</a>`;
						if (order.status < 5){
							html += `<div class="order-list-footer mt10">`;
							if (order.status == 0) {
								html += `<button class="btn24 delete-btn">`+appT('delete')+`</button>
										<button class="btn24 btn-black right repurchase-btn">`+appT('repurchase')+`</button>`;
							}
							if (order.status == 1) {
								html += `<button class="btn24 cancel-btn">`+appT('cancel')+`</button>
										<a class="btn24 btn-black right ml6" href="`+URI+`/checkout/payOrder.html?id=`+order.order_id+`">`+appT('checkout')+`</a>`;
							}
							if (order.status == 2) {
								html += `button class="btn24 right ml6 refund-btn">`+appT('refund')+`</button>`;
							}
							if (order.status == 3) {
								html += `<button class="btn24 btn-black right ml6 complete-btn">`+appT('complete')+`</button>`;
							}
							if (order.status == 4 && !order.is_review) {
								html += `<a class="btn24 btn-black right ml6" href="`+URI+`/order/review.html?id=`+order.order_id+`">`+appT('review')+`</a>`;
							}
							if (order.status == 3 || order.status == 4) {
								html += `<a class="btn24 right ml6 bg-ef" href="`+URI+`/order/logistics.html?id=`+order.order_id+`">`+appT('logistics')+`</a>`;
							}
							html += `<div class="clear"></div>
									</div>`;
						}
					html += `</div>`;
					}
					obj.append(html);
				}
			}
		});
	}
};