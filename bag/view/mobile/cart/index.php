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
					<div class="image tcell">
						<div class="relative">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
							<button class="like-block">
								<span class="iconfont icon-xihuan"></span>
							</button>
						</div>
					</div>
					<div class="info tcell">
						<a class="e2 product-name" href="<?php echo $value['url'];?>"><?php echo $value['name'];?></a>
						<div class="field-row">
							<div class="attr-content">
								<?php foreach ($value['attv'] as $k => $v) {?>
								<?php if (!empty($value['attvImage'][$k])) {?>
								<div class="attr-item attr-image">
									<img data-src="<?php echo $value['attvImage'][$k];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
									<span class="e1"><?php echo $v;?></span>
									<i class="iconfont icon-xiangxia2"></i>
									<div class="clear"></div>
								</div>
								<?php } else { ?>
								<div class="attr-item">
									<span class="e1"><?php echo $v;?></span>
									<i class="iconfont icon-xiangxia2"></i>
								</div>
								<?php } ?>
								<?php } ?>
							</div>
							<div class="edit-content">
								<div class="product-price">
									<p class="price e1"><?php echo $value['price'];?></p>
									<p class="original_price e1"><?php echo $value['original_price'];?></p>
								</div>
								
							</div>
						</div>
					</div>
				</div>
				<div class="table mt8">
					<div class="quantity"></div>
					<div class="btn-content">
						<button class="remove-btn color-blue">
							<i class="iconfont icon-shanchu"></i>
							<span>Remove</span>
						</button>
						<button class="move-cart color-blue">Move to Cart</button>	
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