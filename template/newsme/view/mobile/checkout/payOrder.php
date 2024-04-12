<div id="checkout-page">
	<div class="layer">
		<div class="header">
			<img src="<?php echo siteUrl('image/common/locked.png');?>">
			<span class="checkout-tips"><?php echo appT('secure_checkout');?></span>
			<a class="right f20 relative" href="<?php echo url('cart');?>">
				<span class="iconfont icon-gouwuche"></span>
			</a>
		</div>
		<?php if (empty($error)) {?>
		<div class="header-nav">
			<span class="f600 c40"><?php echo appT('shipping_address');?></span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span class="f600 c40"><?php echo appT('delivery_method');?></span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span class="f600 c40"><?php echo appT('payment_info');?></span>
		</div>
		<?php } ?>
	</div>
	<?php if (empty($error)) {?>
	<div class="bg-f info-content payment-info-content">
		<div class="item">
			<p class="f14 f700 title payment-info-title">
				<span><?php echo appT('payment_method');?></span>
				<a href="javascript:;" class="right">
					<span><?php echo empty($methodList[$method]['name']) ? '' : $methodList[$method]['name'];?></span>
					<span class="iconfont icon-xiangyou1 f600"></span>
				</a>
			</p>
			<?php $methodTotal = count($methodList); if ($methodTotal){?>
			<div class="payment-content">
				<?php echo make($methodList[$method]['class'])->pay($orderInfo['base']['order_id']);?>
			</div>
			<?php } ?>
			<?php $payTemplate = make('app/payment/stripe/Wallet')->pay([
				'order_id' => $orderInfo['base']['order_id'],
				'payment_init_success'=> $methodTotal ? 'stripeWalletPayInit' : '',
				'style' => [
					'theme' => 'dark',
				],
			]);?>
			<?php if ($payTemplate) {?>
			<div class="payment-stripe-wallet-pay-content">
				<?php echo $payTemplate;?>
			</div>
			<?php if ($methodTotal){?>
			<script type="text/javascript">
			function stripeWalletPayInit (ev) {
			$('.payment-stripe-wallet-pay-content').prepend('<div class="list-title flex mt8 pb8">\
					<div class="tcell">\
						<p class="line"></p>\
					</div>\
					<p class="title f16 f800"><?php echo appT('or');?></p>\
					<div class="tcell">\
						<p class="line"></p>\
					</div>\
				</div>');
			}
			</script>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
	<div class="bg-f info-content">
		<div class="item">
			<p class="f14 f600 title">
				<span class="f700"><?php echo appT('order_no');?>: </span>
				<span class="f600"><?php echo $orderInfo['base']['order_no'];?></span>
			</p>
			<div class="order-content">
				<?php foreach ($orderInfo['fee_list'] as $value){?>
				<div class="row<?php echo $value['type']==0?' originalprice-row':'';?>">
					<p class="name left"><?php echo $value['name'];?>:</p>
					<p class="value f600 right"><?php echo $value['value_format'];?></p>
					<p class="clear"></p>
				</div>
				<?php }?>
				<div class="line mt12"></div>
				<div class="row mt12 f600">
					<p class="name left"><?php echo appT('order_total');?>:</p>
					<p class="value f600 right"><?php echo $orderInfo['base']['order_total_format'];?></p>
					<p class="clear"></p>
				</div>
			</div>
		</div>
	</div>
	<div class="bg-f info-content">
		<div class="item">
			<p class="title">
				<span class="f14 f700"><?php echo appT('shipping_address');?></span>
			</p>
			<a href="javascript:;" class="address-info-content mt6">
				<div class="address-info">
					<p><?php echo trim($orderInfo['shipping_address']['first_name'].' '.$orderInfo['shipping_address']['last_name']);?></p>
					<p><?php echo $orderInfo['shipping_address']['city'].' '.($orderInfo['shipping_address']['state'] ? $orderInfo['shipping_address']['state'].' ' : '').service('address/Country')->getName($orderInfo['shipping_address']['country_code2']).', '.$orderInfo['shipping_address']['postcode'];?></p>
					<p><?php echo trim($orderInfo['shipping_address']['address1'].' '.$orderInfo['shipping_address']['address2']);?></p>
					<p><?php echo $orderInfo['shipping_address']['phone'];?></p>
					<?php if (!empty($orderInfo['shipping_address']['tax_number'])){?>
					<p><span class="f12 c6"><?php echo appT('tax');?>:&nbsp;</span><?php echo $orderInfo['shipping_address']['tax_number'];?></p>
					<?php } ?>
				</div>
			</a>
			<div class="border mt6"></div>
		</div>
	</div>
	<div class="bg-f info-content">
		<div class="item">
			<p class="title">
				<span class="f14 f700"><?php echo appT('billing_address');?></span>
				<button class="btn24 btn-black right address-edit-btn" data-order_id="<?php echo $orderInfo['base']['order_id'];?>">Edit</button>
			</p>
			<a href="javascript:;" class="address-info-content mt6">
				<div class="address-info">
					<p><?php echo trim($orderInfo['billing_address']['first_name'].' '.$orderInfo['billing_address']['last_name']);?></p>
					<p><?php echo $orderInfo['billing_address']['city'].' '.($orderInfo['billing_address']['state'] ? $orderInfo['billing_address']['state'].' ' : '').service('address/Country')->getName($orderInfo['billing_address']['country_code2']).', '.$orderInfo['billing_address']['postcode'];?></p>
					<p><?php echo trim($orderInfo['billing_address']['address1'].' '.$orderInfo['billing_address']['address2']);?></p>
					<p><?php echo $orderInfo['billing_address']['phone'];?></p>
					<?php if (!empty($orderInfo['billing_address']['tax_number'])){?>
					<p><span class="f12 c6">Tax:&nbsp;</span><?php echo $orderInfo['billing_address']['tax_number'];?></p>
					<?php } ?>
				</div>
			</a>
			<div class="border mt6"></div>
		</div>
	</div>
	<div class="info-content bg-f">
		<p class="f14 f700 title"><?php echo appT('order_product');?></p>
		<ul class="product-list">
			<?php foreach($orderInfo['product'] as $key => $value) {?>
			<li class="item" data-id="<?php echo $key;?>">
				<div class="table">
					<div class="image tcell">
						<div class="image-tcell tcell">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
						</div>
					</div>
					<div class="info tcell">
						<p class="e2 product-name"><?php echo $value['name'];?></p>
						<div class="field-row">
							<div class="attr-content">
							<?php foreach ($value['attr'] as $k => $v) {?>
								<?php if (empty($v['image'])) {?>
								<div class="attr-item">
									<span class="e1"><?php echo $v['attv_name'];?></span>
								</div>
								<?php } else { ?>
								<div class="attr-item attr-image">
									<img data-src="<?php echo $v['image'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
									<span class="e1"><?php echo $v['attv_name'];?></span>
									<div class="clear"></div>
								</div>
								<?php } ?>
							<?php } ?>
							</div>
							<div class="edit-content">
								<p class="quantity-num">x <?php echo $value['quantity'];?></p>
								<div class="product-price">
									<p class="price e1"><?php echo $value['price_format'];?></p>
									<p class="original_price e1"><?php echo $value['original_price_format'];?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php $this->load('common/address');?>
	<?php } else {?>
	<div class="info-content bg-f tc">
		<p class="f16 f600 mt20"><?php echo $error;?></p>
		<a href="<?php echo url('cart');?>" class="btn btn-black iblock w50 mt30"><?php echo appT('BACK TO CART');?></a>
	</div>
	<?php }?>
</div>