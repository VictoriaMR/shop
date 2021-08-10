<div id="cart-page">
	<?php if (empty($checkedList)) {?>
	<p class="empty-title">YOUR CART IS EMPTY</p>
	<div class="continue-btn">
		<a href="<?php echo url('');?>" class="btn btn-black">CONTINUE SHOPPING</a>
	</div>
	<?php } else {?>
	<div class="layer pb24">
		<ul class="cart-list checked">
			<?php foreach($checkedList as $value) {?>
			<li class="item" data-id="<?php echo $value['cart_id'];?>">
				<div class="table<?php echo $value['out_of_stock'] ? ' opac5' : '';?>">
					<div class="image tcell">
						<div class="relative">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
							<button class="like-block" data-id="<?php echo $value['spu_id'];?>">
								<span class="iconfont icon-xihuan<?php echo in_array($value['spu_id'], $collectList) ? 'fill' : '';?>"></span>
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
				<div class="table mt8 w100">
					<div class="btn-content tcell w75 tl">
						<button class="move-cart save-for-later">Save for later</button>
						<button class="remove-btn">
							<i class="iconfont icon-shanchu"></i>
							<span>Remove</span>
						</button>
						<?php if ($value['out_of_stock']) {?>
						<button class="btn-error">Out of stock</button>
						<?php } ?>
					</div>
					<div class="quantity tcell w25" data-stock="<?php echo $value['stock'];?>">
						<button class="plus"><span class="iconfont icon-jiahao1"></span></button>
						<input type="text" class="num" value="<?php echo $value['quantity'];?>" maxlength="4">
						<button class="minus disabled" disabled="disabled"><span class="iconfont icon-jianhao"></span></button>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
	<?php if (!empty($unCheckList)){?>
	<div class="layer pb24">
		<div class="list-title flex">
			<div class="tcell">
				<p class="line"></p>
			</div>
			<p class="title">MY SAVED ITEMS</p>
			<div class="tcell">
				<p class="line"></p>
			</div>
		</div>
		<ul class="cart-list mt20">
			<?php foreach($unCheckList as $value) {?>
			<li class="item" data-id="<?php echo $value['cart_id'];?>">
				<div class="table<?php echo $value['out_of_stock'] ? ' opac5' : '';?>">
					<div class="image tcell">
						<div class="relative">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
							<button class="like-block" data-id="<?php echo $value['spu_id'];?>">
								<span class="iconfont icon-xihuan<?php echo in_array($value['spu_id'], $collectList) ? 'fill' : '';?>"></span>
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
								<div class="product-price">
									<p class="price e1"><?php echo $value['price'];?></p>
									<p class="original_price e1"><?php echo $value['original_price'];?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="table mt8 w100">
					<div class="btn-content tcell w75 tl">
						<button class="move-cart move-to-cart">Move to cart</button>
						<button class="remove-btn">
							<i class="iconfont icon-shanchu"></i>
							<span>Remove</span>
						</button>
						<?php if ($value['out_of_stock']) {?>
						<button class="btn-error">Out of stock</button>
						<?php } ?>
					</div>
					<div class="quantity tcell w25 tr">
						<button class="quantity-num">x <?php echo $value['quantity'];?></button>
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
<div class="m-modal hide" id="sku-select-modal">
	<div class="mask"></div>
	<div class="dialog layer">
		<span class="iconfont icon-guanbi2"></span>
		<div class="contentfill">
		</div>
		<div class="dialog-footer">
			<button class="btn btn-black w100 confirm-btn">CONFIRM</button>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	CARTPAGE.init();
});
</script>