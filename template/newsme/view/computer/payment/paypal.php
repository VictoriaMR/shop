<?php if(!empty(make('app/service/payment/PayPal')->getToken())){?><div id="paypal-js" style="display:none;">
    <form id="paypal-checkout-form" method="post" action="<?php echo url('checkout');?>" style="display:none;">
        <input type="hidden" name="paypal_order_id"/>
    </form>
    <div class="list-title">
        <div class="tcell">
            <p class="line"></p>
        </div>
        <p class="title">OR</p>
        <div class="tcell">
            <p class="line"></p>
        </div>
    </div>
    <div id="paypal-checkout-btn"></div>
</div>
<script type="text/javascript">
    var js_sdk_paypal = document.getElementById('js-sdk-paypal');
    if (js_sdk_paypal) {
        if (typeof paypal !== 'undefined') {
            readyPaypalJsSdk();
        } else {
            js_sdk_paypal.addEventListener('load', function(){
                readyPaypalJsSdk();
            }, false);
        }
    } else {
        js_sdk_paypal = document.createElement('script');
        js_sdk_paypal.src = "<?php echo make('app/service/payment/PayPal')->getJsSdk('', false, true);?>";
        js_sdk_paypal.id = 'js-sdk-paypal';
        js_sdk_paypal.async = 'true';
        document.head.appendChild(js_sdk_paypal);
        js_sdk_paypal.addEventListener('load', function(){
            readyPaypalJsSdk();
        }, false);
    }
    function readyPaypalJsSdk() {
        var paypalButtonObj = {
            style: {
                layout: 'horizontal',
                tagline: true,
                height: document.getElementById('checkout-btn').offsetHeight,
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        description: 'Purchase From <?php echo \App::get('base_info', 'name');?>',
                        amount: {
                            currency_code: '<?php echo currencyId();?>',
                            value: '<?php echo $orderTotal;?>',
                        }
                    }],
                    application_context: {
                        brand_name: '<?php echo \App::get('base_info', 'name');?>',
                        cancel_url: '<?php echo url('cart');?>',
                        return_url: '<?php echo url('checkout');?>'
                    }
                });
            },
            onApprove: function(data, actions) {
                document.querySelector('[name="paypal_order_id"]').value = data.orderID;
                document.getElementById('paypal-checkout-form').submit();
                popTool.loading();
            }
        };
        document.getElementById('paypal-js').style.display = 'block';
        paypal.Buttons(paypalButtonObj).render('#paypal-checkout-btn');
    }
</script>
<?php } ?>