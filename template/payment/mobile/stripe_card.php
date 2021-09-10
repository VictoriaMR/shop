<div id="payment-stripe-card-content">
	<div class="row">
		<div class="field-box">
			<label for="card-holder" class="payment-field-lable"><?php echo appT('name');?>: </label>
			<div class="payment-field">
				<input type="text" class="input" id="card-holder" name="card-holder" value="<?php echo trim(trim($orderData['shipping_address']['first_name']).' '.trim($orderData['shipping_address']['last_name']));?>" placeholder="Cardholder name">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="field-box">
			<label for="card-field" class="payment-field-lable"><?php echo appT('card_number');?>: </label>
			<div id="card-field" class="payment-field"></div>
		</div>
	</div>
	<div class="row">
		<div class="field-box w50" style="padding-right: 10px;">
			<label for="expiration-field" class="payment-field-lable"><?php echo appT('expiry');?>: </label>
			<div id="expiration-field" class="payment-field"></div>
		</div>
		<div class="field-box w50" style="padding-left: 10px;">
			<label for="cvv-field" class="payment-field-lable"><?php echo appT('cvv');?>: </label>
			<div id="cvv-field" class="payment-field"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div id="card-errors" role="alert"></div>
	<button type="button" id="on-submit" class="btn btn-black" disabled="disabled"><?php echo appT('loading');?>..</button>
</div>
<script type="text/javascript">
	let stripe_creditcard_sdk_js = document.getElementById('stripe_sdk_js');
	if (stripe_creditcard_sdk_js) {
		stripe_creditcard_sdk_js.addEventListener('load', function(){
		    readyStripeCreditCardPay();
		}, false);
	} else {
		stripe_creditcard_sdk_js = document.createElement('script');
		stripe_creditcard_sdk_js.id = 'stripe_sdk_js';
		stripe_creditcard_sdk_js.async = 'true';
		stripe_creditcard_sdk_js.src = 'https://js.stripe.com/v3/';
		document.body.appendChild(stripe_creditcard_sdk_js);
		stripe_creditcard_sdk_js.addEventListener('load', function(){
		    readyStripeCreditCardPay();
		}, false);
	}
	let card_empty = true,
		expiration_empty = true,
		cvv_empty = true,
		card_load = false,
		expiration_load = false,
		cvv_load = false;
    function readyStripeCreditCardPay() {
        const stripeCardStripe = Stripe("<?php echo $config['app_key'];?>");
        const elements = stripeCardStripe.elements();
        var cardNumber = elements.create("cardNumber", { style: style, showIcon: true });
        cardNumber.mount("#card-field");
        var cardExpiry = elements.create("cardExpiry", { style: style });
        cardExpiry.mount("#expiration-field");
        var cardCvc = elements.create("cardCvc", { style: style });
        cardCvc.mount("#cvv-field");
        cardNumber.on('ready', function(event) {
            card_load = true;
            initBtn();
        });
        cardExpiry.on('ready', function(event) {
            expiration_load = true;
            initBtn();
        });
        cardCvc.on('ready', function(event) {
            cvv_load = true;
            initBtn();
        });
        //监听错误
        cardNumber.on('change', function(event) {
            if (event.error) {
                card_empty = true;
                $('#card-errors').text(event.error.message);
            } else {
                card_empty = event.empty;
                $('#card-errors').text('');
            }
        });
        cardExpiry.on('change', function(event) {
            if (event.error) {
                expiration_empty = true;
                $('#card-errors').text(event.error.message);
            } else {
                expiration_empty = event.empty;
                $('#card-errors').text('');
            }
        });
        cardCvc.on('change', function(event) {
            if (event.error) {
                cvv_empty = true;
                $('#card-errors').text(event.error.message);
            } else {
                cvv_empty = event.empty;
                $('#card-errors').text('');
            }
        });
        //付款
        $('#on-submit').on('click', function(event){
            event.preventDefault();
            if ($('#card-holder').val() === '') {
                $('#card-holder').focus();
                $('#card-errors').text("<?php echo appT('enter_name');?>");
                return false;
            }
            if ($(this).attr('disabled')==='disabled') {
                return false;
            }
            if (card_empty) {
                cardNumber.focus();
                $('#card-errors').text("<?php echo appT('enter_card_number');?>");
                return false;
            }
            if (expiration_empty) {
                cardExpiry.focus();
                $('#card-errors').text("<?php echo appT('enter_card_expiration');?>");
                return false;
            }
            if (cvv_empty) {
                cardCvc.focus();
                $('#card-errors').text("<?php echo appT('enter_cvv_number');?>");
                return false;
            }
            load_btn($(this), true);
            var _thisobj = $(this);
            $('#card-errors').text('');
            //发送请求
            var cardHolderName = $('#card-holder').val();
            //获取CLIENT_SECRET
            $.post('<?php echo url('checkout/payOrderAjax');?>', {order_id: "<?php echo $orderData['base']['order_id'];?>", method: "<?php echo $method;?>"}, function(res) {
                if (res.code == 0) {
                    //请求付款
                    stripe.confirmCardPayment(res.data, {
                        payment_method: {
                            card: cardNumber,
                            billing_details : {
                                name: "<?php echo $orderData['billing_address']['first_name'] . ' ' . $orderData['billing_address']['last_name'] ;?>",
                                email: "<?php echo $orderData['billing_address']['email'] ?? '';?>",
                                phone: "<?php echo $orderData['billing_address']['phone'] ;?>",
                                address: {
                                    city: "<?php echo $orderData['billing_address']['city'] ;?>",
                                    country: "<?php echo $orderData['billing_address']['country_code2'] ;?>",
                                    state: "<?php echo $orderData['billing_address']['state'] ;?>",
                                    postal_code: "<?php echo $orderData['billing_address']['postcode'] ;?>",
                                    line1: "<?php echo $orderData['billing_address']['address1'] ;?>",
                                    line2: "<?php echo $orderData['billing_address']['address2'] ;?>",
                                }
                            },
                        },
                    }).then(function(result) {
                        if (result.error) {
                            load_btn(_thisobj);
                            $('#card-errors').text(result.error.message);
                        } else {
                            if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
                                $('[name="payment_pay_id"]').val(result.paymentIntent.id);
                                $('#pay-return').submit();
                            } else {
                                load_btn(_thisobj);
                                $('#card-errors').text('<?php echo appT("order_status");?> '+result.paymentIntent.status);
                            }
                        }
                    });
                } else {
                    load_btn(_thisobj);
                    $('#card-errors').text(res.msg);
                }
            });
        });
    }
    //按钮初始化
    function initBtn()
    {
        if (card_load && expiration_load && cvv_load) {
            load_btn($('#on-submit'));
            //移除遮罩
            hideStcPaymentContentMask();
        } else {
            load_btn($('#on-submit'), true);
        }
    }
    //按钮状态
    function load_btn(obj, loading)
    {
        if (!loading) {
            obj.attr('disabled', false).text("<?php echo appT('pay');?>");
        } else {
            obj.attr('disabled', true).text("<?php echo appT('loading');?>...");
        }
    }
</script>