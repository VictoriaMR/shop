<div id="checkout-page">
	<div class="layer">
		<div class="header">
			<img src="<?php echo siteUrl('image/common/locked.png');?>">
			<span class="checkout-tips">SECURE CHECKOUT</span>
			<a class="right f20 relative" href="<?php echo url('cart');?>">
				<span class="iconfont icon-gouwuche"></span>
			</a>
		</div>
		<?php if (empty($error)) {?>
		<div class="header-nav">
			<?php if (empty($shipAddress)){?>
			<span class="f600 c40">Shipping Address</span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span>Delivery Method</span>
			<span class="iconfont icon-xiangyou1"></span>
			<span>Payment Info</span>
			<?php } else {?>
			<span class="iconfont icon-xuanze f600 c40"></span>
			<span class="f600 c40">Shipping Address</span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span class="f600 c40">Delivery Method</span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span>Payment Info</span>
			<?php }?>
		</div>
		<?php } ?>
	</div>
	<?php if (empty($error)) {?>
	<form id="checkout-form">
		<input type="hidden" name="id" value="<?php echo $skuId;?>">
		<input type="hidden" name="quantity" value="<?php echo $quantity;?>">
		<div class="bg-f info-content mt0">
			<div class="address">
				<div class="item shipping-address-item relative" data-id="<?php echo $shipAddress['address_id'] ?? 0;?>">
					<p class="f16 f600 title">Shipping Address</p>
					<?php if (empty($shipAddress)) {?>
					<a href="javascript:;" class="empty-address address-info-content mt6">
						<div class="tcell f14 tl">
							<span>Your address was empty! Click to add address.</span>
						</div>
						<div class="tcell iconfont-tcell c9">
							<span class="iconfont icon-xiangyou1"></span>
						</div>
					</a>
					<?php } else {?>
					<a href="javascript:;" class="address-info-content mt6">
						<div class="address-info">
							<p><?php echo trim($shipAddress['first_name'].' '.$shipAddress['last_name']);?></p>
							<p><?php echo $shipAddress['city'].' '.($shipAddress['state'] ? $shipAddress['state'].' ' : '').make('app/service/address/CountryService')->getName($shipAddress['country_code2']).', '.$shipAddress['postcode'];?></p>
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
					<?php } ?>
					<div class="border mt6"></div>
					<input type="hidden" name="shipping_address_id" value="<?php echo $shipAddress['address_id'] ?? 0;?>">
				</div>
				<div class="set-billing-as-shipping mt6">
					<span class="iconfont icon-<?php echo $shipAddress['address_id'] == $billAddress['address_id'] ? 'fangxingxuanzhongfill' : 'fangxingweixuanzhong';?>"></span>
					<span class="tips">Billing address same as shipping</span>
				</div>
				<?php if (!empty($billAddress)) {?>
				<div class="item billing-address-item relative mt12<?php echo $shipAddress['address_id'] == $billAddress['address_id'] ? ' hide' : '';?>" data-id="<?php echo $billAddress['address_id'];?>">
					<p class="f16 f600 title">Billing Address</p>
					<a href="javascript:;" class="address-info-content mt6">
						<div class="address-info">
							<p><?php echo trim($billAddress['first_name'].' '.$billAddress['last_name']);?></p>
							<p><?php echo $billAddress['city'].' '.($billAddress['state'] ? $billAddress['state'].' ' : '').make('app/service/address/CountryService')->getName($billAddress['country_code2']).', '.$billAddress['postcode'];?></p>
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
					<input type="hidden" name="billing_address_id" value="<?php echo $billAddress['address_id'];?>">
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="info-content bg-f shipping-method-content">
			<p class="f16 f600 title">Shipping Method</p>
			<?php if (empty($logisticsList)){?>
			<div class="empty-tips f14 mt12">
				<p>Please set your address first!</p>
			</div>
			<?php } else { ?>
			<div class="logistics-list">
				<?php foreach ($logisticsList as $key => $value){?>
				<div class="item">
					<span class="iconfont icon-<?php echo $key==0?'yuanxingxuanzhongfill':'yuanxingweixuanzhong';?>"></span>
					<div class="row f16 f600">
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
				<span class="c6">Add Shipping Insurance to your order</span>
				<span class="f600"><?php echo $insuranceFee;?></span>
				<span class="iconfont icon-tishi relative"></span>
				<div class="help-tips">
					<div class="border-up-empty">
						<span></span>
					</div>
					<span>Insurance offers premium protection and safety for your valuable items during international shipping. We'll reship your package immediately at no extra charge if it's reported lost or damaged.</span>
				</div>
				<input type="hidden" name="insurance" value="0">
			</div>
			<?php }?>
			<?php }?>
		</div>
		<div class="info-content bg-f">
			<p class="f16 f600 title">Shipping Bag</p>
			<ul class="product-list">
				<?php foreach($skuList as $key => $value) {?>
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
								<?php foreach ($value['attv'] as $k => $v) {?>
									<?php if (!empty($value['attvImage'][$k])) {?>
									<div class="attr-item attr-image">
										<img data-src="<?php echo $value['attvImage'][$k];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
										<span class="e1"><?php echo $v;?></span>
										<div class="clear"></div>
									</div>
									<?php } else { ?>
									<div class="attr-item">
										<span class="e1"><?php echo $v;?></span>
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
			<p class="f16 f600 title">Order Summary</p>
			<div class="order-content">
				<div class="row">
					<p class="name left">Product Total:</p>
					<p class="value f600 right">$86.7</p>
					<p class="clear"></p>
				</div>
				<div class="row">
					<p class="name left">Product Total:</p>
					<p class="value f600 right">$86.7</p>
					<p class="clear"></p>
				</div>
				<div class="line"></div>
				<div class="row mt10 f600">
					<p class="name left">Product Total:</p>
					<p class="value f600 right">$86.7</p>
					<p class="clear"></p>
				</div>
			</div>
			<button type="button" class="btn btn-black w100 mt20">PLACE ORDER</button>
		</div>
	</form>
	<?php } else {?>
	<div class="info-content bg-f tc">
		<p class="f14"><?php echo $error;?></p>
		<a href="<?php echo url('cart');?>" class="btn btn-black iblock w50 mt20">BACK TO CART</a>
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
				<p class="line"></p>
			</div>
			<p class="title">MY ADDRESSES</p>
			<div class="tcell">
				<p class="line"></p>
			</div>
		</div>
		<div class="mt10 tr">
			<button type="button" class="btn24 btn-black add-new-address">New Address</button>
		</div>
		<div class="address-list" data-page="0" data-size="10">

		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	CHECKOUTINDEX.init();
})
</script>