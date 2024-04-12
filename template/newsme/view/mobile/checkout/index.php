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
			<?php if (empty($shipAddress)){?>
			<span class="f600 c40"><?php echo appT('shipping_address');?></span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span><?php echo appT('delivery_method');?></span>
			<span class="iconfont icon-xiangyou1"></span>
			<span><?php echo appT('payment_info');?></span>
			<?php } else {?>
			<span class="iconfont icon-xuanze f600 c40"></span>
			<span class="f600 c40"><?php echo appT('shipping_address');?></span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span class="f600 c40"><?php echo appT('delivery_method');?></span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span><?php echo appT('payment_info');?></span>
			<?php }?>
		</div>
		<?php } ?>
	</div>
	<?php if (empty($error)) {?>
	<form id="checkout-form">
		<?php if (empty(userId())) {?>
		<div class="info-content bg-f">
			<p class="f14 f700 title"><?php echo distT('guest_checkout');?></p>
			<div class="guest-email-item">
				<p class="f700 f14 mt8">
					<span class="text"><?php echo distT('email');?></span>
					<span class="cred">*</span>
				</p>
				<div class="relative">
					<input type="text" class="input mt2<?php echo $email?' success':'';?>" name="email" value="<?php echo $email;?>" placeholder="<?php echo appT('email_tips');?>">
					<?php if ($email){?>
					<span class="iconfont icon-yuanxingxuanzhongfill success"></span>
					<?php }?>
				</div>
				<p class="guest-email-tips mt4 c9"><?php echo distT('guest_email_tips', ['{url}'=>url('login')]);?></p>
			</div>
		</div>
		<?php }?>
		<input type="hidden" name="id" value="<?php echo $skuId;?>">
		<input type="hidden" name="quantity" value="<?php echo $quantity;?>">
		<div class="bg-f info-content">
			<div class="address">
				<div class="item shipping-address-item relative" data-id="<?php echo $shipAddress['address_id'] ?? 0;?>">
					<p class="f14 f700 title"><?php echo appT('shipping_address');?></p>
					<?php if (empty($shipAddress)) {?>
					<a href="javascript:;" class="empty-address address-info-content mt6">
						<div class="tcell f14 tl">
							<span><?php echo distT('address_empty');?></span>
						</div>
						<div class="tcell iconfont-tcell c9">
							<span class="iconfont icon-xiangyou1"></span>
						</div>
					</a>
					<?php } else {?>
					<a href="javascript:;" class="address-info-content mt6">
						<div class="address-info">
							<p><?php echo trim($shipAddress['first_name'].' '.$shipAddress['last_name']);?></p>
							<p><?php echo $shipAddress['city'].' '.($shipAddress['state'] ? $shipAddress['state'].' ' : '').service('address/Country')->getName($shipAddress['country_code2']).', '.$shipAddress['postcode'];?></p>
							<p><?php echo trim($shipAddress['address1'].' '.$shipAddress['address2']);?></p>
							<p><?php echo $shipAddress['phone'];?></p>
							<?php if (!empty($shipAddress['tax_number'])){?>
							<p><span class="f12 c6">Tax:&nbsp;</span><?php echo $shipAddress['tax_number'];?></p>
							<?php } ?>
						</div>
						<div class="tcell iconfont-tcell c9">
							<span class="iconfont icon-xiangyou1"></span>
						</div>
					</a>
					<input type="hidden" name="shipping_address_id" value="<?php echo $shipAddress['address_id'] ?? 0;?>">
					<?php } ?>
					<div class="border mt6"></div>
				</div>
				<?php if (!empty($shipAddress)) {?>
				<div class="set-billing-as-shipping mt6">
					<span class="iconfont icon-<?php echo $shipAddress['is_bill'] ? 'fangxingxuanzhongfill' : 'fangxingweixuanzhong';?>"></span>
					<span class="tips"><?php echo distT('same_billing_address');?></span>
				</div>
				<?php }?>
				<?php if (!empty($billAddress)) {?>
				<div class="item billing-address-item relative mt12<?php echo $shipAddress['is_bill'] ? ' hide' : '';?>" data-id="<?php echo $billAddress['address_id'] ?? 0;?>">
					<p class="f14 f700 title"><?php echo appT('billing_address');?></p>
					<a href="javascript:;" class="address-info-content mt6">
						<div class="address-info">
							<p><?php echo trim($billAddress['first_name'].' '.$billAddress['last_name']);?></p>
							<p><?php echo $billAddress['city'].' '.($billAddress['state'] ? $billAddress['state'].' ' : '').service('address/Country')->getName($billAddress['country_code2']).', '.$billAddress['postcode'];?></p>
							<p><?php echo trim($billAddress['address1'].' '.$billAddress['address2']);?></p>
							<p><?php echo $billAddress['phone'];?></p>
							<?php if (!empty($billAddress['tax_number'])){?>
							<p><span class="f12 c6">Tax:&nbsp;</span><?php echo $billAddress['tax_number'];?></p>
							<?php } ?>
						</div>
						<div class="tcell iconfont-tcell c9">
							<span class="iconfont icon-xiangyou1"></span>
						</div>
					</a>
					<div class="border mt6"></div>
					<input type="hidden" name="billing_address_id" value="<?php echo $billAddress['address_id'] ?? 0;?>">
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="info-content bg-f shipping-method-content">
			<p class="f14 f700 title"><?php echo appT('shipping_method');?></p>
			<?php if (empty($logisticsList)){?>
			<div class="empty-tips f14 mt12">
				<p><?php echo distT('set_address_first');?></p>
			</div>
			<?php } else { ?>
			<div class="logistics-list">
				<?php foreach ($logisticsList as $key => $value){?>
				<div class="item">
					<span class="iconfont icon-<?php echo $key==0?'yuanxingxuanzhongfill':'yuanxingweixuanzhong';?>"></span>
					<div class="row f14 f700">
						<span><?php echo $value['name'];?></span>
						<span class="ml12"><?php echo $value['fee'];?></span>
					</div>
					<?php if (!empty($value['tips'])){?>
					<p class="c6 mt4"><?php echo $value['tips'];?></p>
					<?php }?>
				</div>
				<?php }?>
			</div>
			<input type="hidden" name="logistics_id" value="1">
			<?php if (!empty($insuranceFee)) {?>
			<div class="mt6 insurance-btn">
				<span class="iconfont icon-fangxingweixuanzhong"></span>
				<span class="c6"><?php echo distT('add_insurance');?></span>
				<span class="f600"><?php echo $insuranceFee;?></span>
				<span class="iconfont icon-tishi relative"></span>
				<div class="help-tips">
					<div class="border-up-empty">
						<span></span>
					</div>
					<span><?php echo distT('insurance_tips');?></span>
				</div>
				<input type="hidden" name="insurance" value="0">
			</div>
			<?php }?>
			<?php }?>
		</div>
		<div class="info-content bg-f">
			<p class="f14 f700 title"><?php echo distT('purchase_products');?></p>
			<ul class="product-list">
				<?php foreach($skuList as $key => $value) {?>
				<li class="item" data-id="<?php echo $key;?>">
					<div class="table w100">
						<div class="image tcell">
							<div class="image-tcell tcell">
								<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
							</div>
						</div>
						<div class="info tcell">
							<p class="e2 product-name"><?php echo $value['name'];?></p>
							<div class="field-row">
								<div class="attr-content">
								<?php foreach ($value['skuAttv'][$key] as $k => $v) {?>
									<?php if (!empty($value['attvImage'][$v]['url'])) {?>
									<div class="attr-item attr-image">
										<img data-src="<?php echo $value['attvImage'][$v]['url'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
										<span class="e1"><?php echo $value['attv'][$v];?></span>
										<div class="clear"></div>
									</div>
									<?php } else { ?>
									<div class="attr-item">
										<span class="e1"><?php echo $value['attv'][$v];?></span>
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
		<div class="info-content bg-f order-summary-content">
			<p class="f14 f700 title"><?php echo appT('order_summary');?></p>
			<div class="order-content"></div>
			<button type="button" class="btn btn-black w100" id="place-order-btn<?php echo empty($shipAddress) ? ' disabled':'';?>" <?php echo empty($shipAddress) ? 'disabled="disabled"':'';?>><?php echo appT('place_order');?></button>
		</div>
	</form>
	<?php } else {?>
	<div class="info-content bg-f tc">
		<p class="f14"><?php echo $error;?></p>
		<a href="<?php echo url('cart');?>" class="btn btn-black iblock w50 mt20"><?php echo distT('back_to_cart');?></a>
	</div>
	<?php }?>
</div>
<?php $this->load('common/address');?>
<div class="modal hidden" id="my-address-list">
	<div class="mask"></div>
	<button type="button" class="close-btn btn-white">
		<span class="iconfont icon-guanbi1"></span>
	</button>
	<div class="content">
		<div class="list-title flex">
			<div class="tcell">
				<p class="line bg-f5"></p>
			</div>
			<p class="title"><?php echo distT('my_addresses');?></p>
			<div class="tcell">
				<p class="line bg-f5"></p>
			</div>
		</div>
		<div class="mt10 tr">
			<button type="button" class="btn24 btn-black add-new-address"><?php echo distT('new_address');?></button>
		</div>
		<div class="address-list" data-page="0" data-size="10"></div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	CHECKOUTINDEX.init({
		email_empty: '<?php echo distT('email_empty');?>',
		email_not_match: '<?php echo distT('email_not_match');?>',
	});
});
</script>
<?php $this->load('common/simple_footer');?>