<div id="checkout-page">
	<div class="layer">
		<div class="header">
			<img src="<?php echo siteUrl('image/common/locked.png');?>">
			<span class="checkout-tips">SECURE CHECKOUT</span>
			<a class="right f20 relative" href="<?php echo url('cart');?>">
				<span class="iconfont icon-gouwuche"></span>
			</a>
		</div>
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
	</div>
	<div class="bg-f info-content">
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
</div>
<?php $this->load('common/address');?>
<div class="modal hidden" id="my-address-list">
	<div class="mask"></div>
	<button class="close-btn btn-white">
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
			<button class="btn24 btn-black add-new-address">New Address</button>
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