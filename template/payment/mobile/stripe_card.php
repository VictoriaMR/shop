<style type="text/css">
#payment-stripe-card-content .field-box{display:table; width:100%}
#payment-stripe-card-content .field-box .payment-field{height:0.4rem;padding:0 0.08rem;border:0.01rem solid #e5e5e5;border-radius:0.06rem;overflow:hidden;display:table-cell;vertical-align:middle}
#payment-stripe-card-content #card-errors{color:#e64545;min-height:0.2rem}
</style>
<div id="payment-stripe-card-content" class="relative">
	<div class="row mt20">
		<div class="field-box">
			<div class="payment-field">
				<input type="text" class="input" id="card-holder" name="card-holder" value="<?php echo trim($shipping_address['first_name'].' '.$shipping_address['last_name']);?>" placeholder="<?php echo appT('name');?>">
			</div>
		</div>
	</div>
	<div class="row mt6">
		<div class="field-box">
			<div id="card-field" class="payment-field"></div>
		</div>
	</div>
	<div class="row mt6">
		<div class="w50 pr4 left">
			<div class="field-box">
				<div id="expiration-field" class="payment-field"></div>
			</div>
		</div>
		<div class="w50 pl4 left">
			<div class="field-box">
				<div id="cvv-field" class="payment-field"></div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div id="card-errors" role="alert"></div>
	<button type="button" id="payment-stripe-card-submit" class="btn btn-black w100 mt10" disabled="disabled"><?php echo appT('loading');?></button>
	<form id="payment-stripe-card-pay-return" method="post" action="<?php echo url('checkout/payReturn', ['order_id' => $order_id ?? 0, 'method' => $method]);?>">
	    <input type="hidden" name="payment_pay_id" value="">
	</form>
	<div class="m-modal loading" style="position:absolute">
		<div class="mask" style="position:absolute"></div>
		<div class="loading-block">
			<div></div>
			<div></div>
			<div></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	let stripe_creditcard_sdk_js = document.getElementById('stripe_sdk_js');
	if (stripe_creditcard_sdk_js) {
		stripe_creditcard_sdk_js.addEventListener('load', function(){
			STRIPECREDITCARD.init();
		}, false);
	} else {
		stripe_creditcard_sdk_js = document.createElement('script');
		stripe_creditcard_sdk_js.id = 'stripe_sdk_js';
		stripe_creditcard_sdk_js.async = 'true';
		stripe_creditcard_sdk_js.src = 'https://js.stripe.com/v3/';
		document.body.appendChild(stripe_creditcard_sdk_js);
		stripe_creditcard_sdk_js.addEventListener('load', function(){
			STRIPECREDITCARD.init();
		}, false);
	}
	const STRIPECREDITCARD = {
		init: function() {
			const _this = this;
			_this.card_empty = true;
			_this.expiration_empty = true;
			_this.cvv_empty = true;
			_this.card_load = false;
			_this.expiration_load = false;
			_this.cvv_load = false;
			const stripeCardStripe = Stripe('<?php echo $config['app_key'];?>');
			const elements = stripeCardStripe.elements();
			const cardNumber = elements.create('cardNumber', {showIcon:true});
			cardNumber.mount('#card-field');
			const cardExpiry = elements.create('cardExpiry');
			cardExpiry.mount('#expiration-field');
			const cardCvc = elements.create('cardCvc');
			cardCvc.mount('#cvv-field');
			cardNumber.on('ready', function(event) {
				_this.card_load = true;
				_this.initBtn();
			});
			cardExpiry.on('ready', function(event) {
				_this.expiration_load = true;
				_this.initBtn();
			});
			cardCvc.on('ready', function(event) {
				_this.cvv_load = true;
				_this.initBtn();
			});
			//监听错误
			cardNumber.on('change', function(event) {
				if (event.error) {
					_this.card_empty = true;
					_this.error(event.error.message);
				} else {
					_this.card_empty = event.empty;
					_this.error();
				}
			});
			cardExpiry.on('change', function(event) {
				if (event.error) {
					_this.expiration_empty = true;
					_this.error(event.error.message);
				} else {
					_this.expiration_empty = event.empty;
					_this.error();
				}
			});
			cardCvc.on('change', function(event) {
				if (event.error) {
					_this.cvv_empty = true;
					_this.error(event.error.message);
				} else {
					_this.cvv_empty = event.empty;
					_this.error();
				}
			});
			//付款
			$('#payment-stripe-card-submit').on('click', function(event){
				event.preventDefault();
				const cardHolder = $('#card-holder');
				if (cardHolder.val() === '') {
					cardHolder.focus();
					_this.error('<?php echo appT('enter_name');?>');
					return false;
				}
				if (_this.card_empty) {
					cardNumber.focus();
					_this.error('<?php echo appT('enter_card_number');?>');
					return false;
				}
				if (_this.expiration_empty) {
					cardExpiry.focus();
					_this.error('<?php echo appT('enter_card_exp');?>');
					return false;
				}
				if (_this.cvv_empty) {
					cardCvc.focus();
					_this.error('<?php echo appT('enter_card_cvv');?>');
					return false;
				}
				_this.error();
				_this.loadBtn(false);
				TIPS.loadout($(this).parent());
				//获取CLIENT_SECRET
				$.post('<?php echo url('checkout/payOrderAjax');?>', {order_id: "<?php echo $order_id ?? 0;?>", method: "<?php echo $method;?>"}, function(res) {
					if (res.code === '0') {
						//请求付款
						stripe.confirmCardPayment(res.data, {
							payment_method: {
								card: cardNumber,
								<?php if (!empty($billing_address)){?>billing_details : {
									name: '<?php echo trim($billing_address['first_name'].' '.$billing_address['last_name']);?>',
									email: '<?php echo $billing_address['email'] ?? '';?>',
									phone: '<?php echo $billing_address['phone'];?>',
									address: {
										city: '<?php echo $billing_address['city'];?>',
										country: '<?php echo $billing_address['country_code2'];?>',
										state: '<?php echo $billing_address['state'];?>',
										postal_code: '<?php echo $billing_address['postcode'];?>',
										line1: '<?php echo $billing_address['address1'];?>',
										line2: '<?php echo $billing_address['address2'];?>',
									}
								},
							<?php } ?>},
						}).then(function(result) {
							if (result.error) {
								_this.loadBtn(true);
								_this.error(result.error.message);
							} else {
								if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
									$('#payment-stripe-card-pay-return [name="payment_pay_id"]').val(result.paymentIntent.id);
									$('#payment-stripe-card-pay-return').submit();
								} else {
									_this.loadBtn(true);
									_this.error('<?php echo appT('order_status');?> '+result.paymentIntent.status);
								}
							}
						});
					} else {
						_this.loadBtn(true);
						_this.error(res.msg);
					}
				});
			});
		},
		initBtn: function() {
			if (this.card_load && this.expiration_load && this.cvv_load) {
				this.loadBtn(true);
			} else {
				this.loadBtn();
			}
		},
		loadBtn: function (complete) {
			const obj = $('#payment-stripe-card-submit');
			if (complete) {
				obj.attr('disabled', false).text("<?php echo appT('pay', ['{amount}' => $order_total_format]);?>");
				TIPS.loadout(obj.parent());
			} else {
				obj.attr('disabled', true).text("<?php echo appT('loading');?>");
			}
		},
		error: function(msg) {
			const obj = $('#card-errors');
			msg = msg ? msg : '';
			obj.text(msg);
		}
	};
</script>