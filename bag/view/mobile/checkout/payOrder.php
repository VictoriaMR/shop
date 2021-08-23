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
			<span class="f600 c40">Shipping Address</span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span class="f600 c40">Delivery Method</span>
			<span class="iconfont icon-xiangyou1 f600 c40"></span>
			<span class="f600 c40">Payment Info</span>
		</div>
		<?php } else {?>
		<div class="info-content bg-f tc">
			<p class="f14"><?php echo $error;?></p>
			<a href="<?php echo url('cart');?>" class="btn btn-black iblock w50 mt20">BACK TO CART</a>
		</div>
		<?php }?>
	</div>
</div>