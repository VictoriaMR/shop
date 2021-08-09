<div id="cart-page">
	<?php if (empty($list)) {?>
	<p class="empty-title">YOUR CART IS EMPTY</p>
	<div class="continue-btn">
		<a href="<?php echo url('');?>" class="btn btn-black">CONTINUE SHOPPING</a>
	</div>
	<?php } else {?>
	<div class="layer">
		<ul class="cart-list">
			<?php foreach($list as $value) {?>
			<li class="item">
				<div class="table">
					<a class="image tcell" href="<?php echo $value['url'];?>">
						<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
					</a>
					<div class="info tcell">
						<a class="e2 product-name" href="<?php echo $value['url'];?>"><?php echo $value['name'];?></a>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
	<div class="layer">
		<?php $this->load('common/recommend');?>
	</div>
</div>