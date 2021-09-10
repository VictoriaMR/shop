<div id="payment-mobile-pay-content" style="display: none;">
	<?php if (empty($data['success_callback'])){?>
	<?php if (empty($order_id)) {?>
	<!-- 快速购买[预授权] -->
	<form id="payment-mobile-pay-return" method="post" action="<?php echo url('checkout', ['method' => $method]);?>">
		<input type="hidden" name="payment_pay_id" value="">
	</form>
	<?php } else { ?>
	<form id="payment-mobile-pay-return" method="post" action="<?php echo url('checkout/payReturn', ['order_id' => $order_id, 'method' => $method]);?>">
		<input type="hidden" name="payment_pay_id" value="">
	</form>
	<?php }?>
	<?php } ?>
	<div id="payment-mobile-pay-button"></div>
	<?php if (empty($data['error_callback'])){?>
	<div class="error-tips" style="color: #ff0000;"></div>
	<?php } ?>
</div>
<script type="text/javascript">
	var stripe_mobilepay_sdk_js = document.getElementById('stripe_sdk_js');
	if (stripe_mobilepay_sdk_js) {
		stripe_mobilepay_sdk_js.addEventListener('load', function(){
			readyStripeMobilePay();
		}, false);
	} else {
		stripe_mobilepay_sdk_js = document.createElement('script');
		stripe_mobilepay_sdk_js.src = 'https://js.stripe.com/v3/';
		stripe_mobilepay_sdk_js.id = 'stripe_sdk_js';
		stripe_mobilepay_sdk_js.async = 'true';
		document.body.appendChild(stripe_mobilepay_sdk_js);
		stripe_mobilepay_sdk_js.addEventListener('load', function(){
			readyStripeMobilePay();
		}, false);
	}
	function readyStripeMobilePay() {
		var payment_mobile_pay_btn_loading = false;
		var mobilePayStripe = Stripe('<?php echo $config["app_key"];?>');
		//支付参数
		var paymentRequest = mobilePayStripe.paymentRequest({
			country: '<?php echo $country_code2;?>',
			currency: '<?php echo $currency;?>',//小写
			total: {
				label: 'Order Total',
				amount: <?php echo $order_total;?>,
			},
		});
		// 按钮实例化
		var elements = mobilePayStripe.elements();
		var prButton = elements.create('paymentRequestButton', {
			paymentRequest: paymentRequest,
			<?php if (!empty($data['style'])){?>
			style: {
				paymentRequestButton: <?php echo json_encode($data['style'], JSON_UNESCAPED_UNICODE);?>,
			},
			<?php } ?>
		});
		paymentRequest.canMakePayment().then(function(result) {
			if (result) {
				prButton.mount('#payment-mobile-pay-button');
				document.getElementById('payment-mobile-pay-content').style.display = 'block';
				//实例化成功回调
				<?php if (!empty($data['payment_init_success'])) {?>
					<?php echo $data['payment_init_success'];?>(result);
				<?php }?>
			} else {
				//实例化失败回调
				<?php if (!empty($data['payment_init_failed'])) {?>
					<?php echo $data['payment_init_failed'];?>(result);
				<?php }?>
				document.getElementById('payment-mobile-pay-content').style.display = 'none';
			}
		});
		//监听完成付款按钮
		paymentRequest.on('paymentmethod', function(ev) {
			<?php if (empty($data['payment_confirm_btn_click'])) {?>
				stripe_mobilepay_error_tips_default();
			<?php } else {?>
				<?php echo $data['payment_confirm_btn_click'];?>(ev);
			<?php } ?>
			if (payment_mobile_pay_btn_loading) {
				return false;
			}
			//生成意向支付订单 todo 如果要集成快速支付[先授权后订单购买的,更换接口]
			$.post('<?php echo url('Checkout/payOrderAjax');?>', {order_id:"<?php echo $order_id;?>", method: "<?php echo $method;?>"}, function(res){
				if (res.code === 0) {
					var clientSecret = res.data;
					mobilePayStripe.confirmCardPayment(
						clientSecret,
						{payment_method: ev.paymentMethod.id},
						{handleActions: false}
					).then(function(confirmResult) {
						if (confirmResult.error) {
							ev.complete('fail');
							<?php if (empty($data['error_callback'])){?>
								stripe_mobilepay_error_tips_default(confirmResult.error.message);
							<?php } else {?>
								<?php echo $data['error_callback'];?>(result.error.message);
							<?php } ?>
							payment_mobile_pay_btn_loading = false;
						} else {
							ev.complete('success');
							//捕获支付款
							if (confirmResult.paymentIntent.status === "requires_action") {
								mobilePayStripe.confirmCardPayment(clientSecret).then(function(result) {
									if (result.error) {
										<?php if (empty($data['error_callback'])){?>
											stripe_mobilepay_error_tips_default(result.error.message);
										<?php } else {?>
											<?php echo $data['error_callback'];?>(result.error.message);
										<?php } ?>
										payment_mobile_pay_btn_loading = false;
									} else {
										//成功跳转
										<?php if (empty($data['success_callback'])){?>
											stripe_mobilepay_pay_success_default(confirmResult.paymentIntent.id);
										<?php } else {?>
											<?php echo $data['success_callback'];?>(confirmResult);
										<?php } ?>
									}
								});
							} else {
								//成功跳转
								<?php if (empty($data['success_callback'])){?>
									stripe_mobilepay_pay_success_default(confirmResult.paymentIntent.id);
								<?php } else {?>
									<?php echo $data['success_callback'];?>(confirmResult);
								<?php } ?>
							}
						}
					});
				} else {
					<?php if (empty($data['error_callback'])){?>
						stripe_mobilepay_error_tips_default(res.msg);
					<?php } else {?>
						<?php echo $data['error_callback'];?>(res.msg);
					<?php } ?>
					payment_mobile_pay_btn_loading = false;
				}
			});
		});
		//取消支付
		paymentRequest.on('cancel', function(ev) {
			payment_mobile_pay_btn_loading = false;
		});
		function stripe_mobilepay_pay_success_default(paymentIntentId) {
			//默认成功跳转
			document.querySelector('#payment-mobile-pay-return [name="payment_pay_id"]').value = paymentIntentId;
			document.getElementById('payment-mobile-pay-return').submit();
		}
		function stripe_mobilepay_error_tips_default(msg) {
			if (typeof msg === 'undefined') {
                msg = '';
            }
            document.querySelector('#payment-mobile-pay-content .error-tips').innerText = msg;
		}
	}
</script>