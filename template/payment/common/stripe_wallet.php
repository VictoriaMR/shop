<div id="payment-stripe-wallet-pay-content" style="display: none;">
	<?php if (empty($success_callback)){?>
	<form id="payment-stripe-wallet-pay-return" method="post" action="<?php echo url(empty($order_id) ? 'checkout' : 'checkout/payReturn', ['order_id' => $order_id, 'method' => $method]);?>">
		<input type="hidden" name="payment_pay_id" value="">
	</form>
	<?php } ?>
	<div id="payment-stripe-wallet-pay-button"></div>
	<?php if (empty($error_callback)){?>
	<div class="stripe-wallet-error" style="color:#ff0000"></div>
	<?php } ?>
</div>
<script type="text/javascript">
	let stripe_walletpay_sdk_js = document.getElementById('stripe_sdk_js');
	if (stripe_walletpay_sdk_js) {
		stripe_walletpay_sdk_js.addEventListener('load', function(){
			STRIPEWALLETPAY.init();
		}, false);
	} else {
		stripe_walletpay_sdk_js = document.createElement('script');
		stripe_walletpay_sdk_js.src = 'https://js.stripe.com/v3/';
		stripe_walletpay_sdk_js.id = 'stripe_sdk_js';
		stripe_walletpay_sdk_js.async = 'true';
		document.body.appendChild(stripe_walletpay_sdk_js);
		stripe_walletpay_sdk_js.addEventListener('load', function(){
			STRIPEWALLETPAY.init();
		}, false);
	}
	const STRIPEWALLETPAY = {
		init: function() {
			const _this = this;
			_this.payment_stripe_wallet_pay_btn_loading = false;
			const stripeWalletPay = Stripe('<?php echo $config['app_key'];?>');
			const paymentRequest = stripeWalletPay.paymentRequest({
				country: '<?php echo $country_code2;?>',
				currency: '<?php echo $currency;?>',
				total: {
					label: '<?php echo appT('order_total');?>',
					amount: <?php echo $order_total;?>,
				},
			});
			const elements = stripeWalletPay.elements();
			const prButton = elements.create('paymentRequestButton', {
				paymentRequest: paymentRequest,
				<?php if (!empty($data['style'])){?>style: {
					paymentRequestButton: <?php echo json_encode($data['style'], JSON_UNESCAPED_UNICODE);?>,
				},
			<?php } ?>});
			paymentRequest.canMakePayment().then(function(result) {
				if (result) {
					prButton.mount('#payment-stripe-wallet-pay-button');
					document.getElementById('payment-stripe-wallet-pay-content').style.display = 'block';
					<?php if (!empty($payment_init_success)) {?><?php echo $payment_init_success;?>(result);
				<?php }?>} else {
					<?php if (!empty($payment_init_failed)) {?><?php echo $payment_init_failed;?>(result);
				<?php }?>document.getElementById('payment-stripe-wallet-pay-content').style.display = 'none';
				}
			});
			paymentRequest.on('paymentmethod', function(ev) {
				<?php if (empty($payment_confirm_btn_click)) {?>_this.error();
				<?php } else {?><?php echo $payment_confirm_btn_click;?>(ev);
			<?php } ?>if (_this.payment_stripe_wallet_pay_btn_loading) {
					return false;
				}
				_this.payment_stripe_wallet_pay_btn_loading = true;
				//生成意向支付订单 todo 如果要集成快速支付[先授权后订单购买的,更换接口]
				$.post('<?php echo url('Checkout/payOrderAjax');?>', {order_id:'<?php echo $order_id ?? 0;?>', method: '<?php echo $method;?>'}, function(res){
					if (res.code === '0') {
						const clientSecret = res.data;
						mobilePayStripe.confirmCardPayment(
							clientSecret,
							{payment_method: ev.paymentMethod.id},
							{handleActions: false}
						).then(function(confirmResult) {
							if (confirmResult.error) {
								ev.complete('fail');
								<?php if (empty($error_callback)){?>_this.error(confirmResult.error.message);<?php } else {?><?php echo $error_callback;?>(result.error.message);<?php } ?>_this.payment_stripe_wallet_pay_btn_loading = false;
							} else {
								ev.complete('success');
								if (confirmResult.paymentIntent.status === 'requires_action') {
									mobilePayStripe.confirmCardPayment(clientSecret).then(function(result) {
										if (result.error) {
											<?php if (empty($error_callback)){?>_this.error(result.error.message);<?php } else {?><?php echo $error_callback;?>(result.error.message);<?php } ?>_this.payment_stripe_wallet_pay_btn_loading = false;
										} else {
											<?php if (empty($success_callback)){?>_this.success(confirmResult.paymentIntent.id);<?php } else {?><?php echo $success_callback;?>(confirmResult);<?php } ?>
										}
									});
								} else {
									<?php if (empty($success_callback)){?>_this.success(confirmResult.paymentIntent.id);<?php } else {?><?php echo $success_callback;?>(confirmResult);<?php } ?>
								}
							}
						});
					} else {
						<?php if (empty($error_callback)){?>_this.error(res.msg);<?php } else {?><?php echo $error_callback;?>(res.msg);<?php } ?>_this.payment_stripe_wallet_pay_btn_loading = false;
					}
				});
			});
			paymentRequest.on('cancel', function(ev) {
				_this.payment_stripe_wallet_pay_btn_loading = false;
			});
		},
		error: function(msg) {
			msg = msg ? msg : '';
			document.querySelector('#payment-mobile-pay-content .stripe-wallet-error').innerText = msg;
		},
		success: function(paymentIntentId) {
			document.querySelector('#payment-mobile-pay-return [name="payment_pay_id"]').value = paymentIntentId;
			document.getElementById('payment-mobile-pay-return').submit();
		}
	};
</script>