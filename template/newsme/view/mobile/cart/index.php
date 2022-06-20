<?php $this->load('common/back_header', ['_simple_title' => distT('shopping_bag')]);?>
<div id="cart-page" class="min-page">
	<?php if (empty($checkedList)) {?>
	<div class="empty-bag-image">
		<img src="<?php echo siteUrl('image/common/empty-bag.png');?>">
	</div>
	<p class="empty-title"><?php echo distT('cart_empty_tips');?></p>
	<div class="continue-btn">
		<a href="<?php echo url('');?>" class="btn btn-black block"><?php echo distT('continue_shopping')?></a>
	</div>
	<?php } else {?>
	<div class="layer">
		<ul class="cart-list checked">
			<?php foreach($checkedList as $value) {?>
			<li class="item" data-id="<?php echo $value['cart_id'];?>">
				<div class="w100 table<?php echo $value['out_of_stock'] || empty($value['status']) ? ' opac5' : '';?>">
					<div class="image tcell">
						<div class="image-tcell tcell">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
							<?php if (!empty($isLogin)){?>
							<button class="like-block" data-id="<?php echo $value['spu_id'];?>">
								<span class="iconfont icon-xihuan<?php echo in_array($value['spu_id'], $collectList) ? 'fill' : '';?>"></span>
							</button>
							<?php }?>
						</div>
					</div>
					<div class="info tcell">
						<a class="e2 product-name" href="<?php echo $value['url'];?>"><?php echo $value['name'];?></a>
						<div class="field-row">
							<div class="attr-content">
								<?php foreach ($value['skuAttv'][$value['sku_id']] as $k => $v) {?>
								<?php if (!empty($value['attvImage'][$v]['url'])) {?>
								<div class="attr-item attr-image">
									<img data-src="<?php echo $value['attvImage'][$v]['url'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
									<span class="e1"><?php echo $value['attv'][$v];?></span>
									<i class="iconfont icon-xiangxia2"></i>
									<div class="clear"></div>
								</div>
								<?php } else { ?>
								<div class="attr-item">
									<span class="e1"><?php echo $value['attv'][$v];?></span>
									<i class="iconfont icon-xiangxia2"></i>
								</div>
								<?php } ?>
								<?php } ?>
							</div>
							<div class="edit-content">
								<div class="product-price">
									<p class="price e1"><?php echo $value['price_format'];?></p>
									<p class="original_price e1"><?php echo $value['original_price_format'];?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="table mt8 w100">
					<div class="btn-content tcell w75 tl">
						<button class="move-cart save-for-later"><?php echo distT('save_for_later');?></button>
						<button class="remove-btn">
							<i class="iconfont icon-shanchu"></i>
							<span><?php echo distT('remove');?></span>
						</button>
						<?php if ($value['out_of_stock'] || empty($value['status'])) {?>
						<button class="btn-error"><?php echo empty($value['status']) ? 'Disabled' : 'Out of stock';?></button>
						<?php } ?>
					</div>
					<div class="quantity tcell w25" data-stock="<?php echo $value['stock'];?>">
						<button class="plus"><span class="iconfont icon-jiahao1"></span></button>
						<input type="text" class="num" value="<?php echo $value['quantity'];?>" maxlength="3">
						<button class="minus disabled" disabled="disabled"><span class="iconfont icon-jianhao"></span></button>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
	<div id="cart-summary">
		<p class="f18"><?php echo distT('cart_summary');?></p>
		<div class="content">
			<ul>
				<?php foreach ($summary as $value){?>
				<li <?php echo $value['type'] == 2 ? 'class="f700 f16"' : '';?>>
					<span><?php echo $value['name'];?></span>
					<span class="right"><?php echo $value['price_format'];?></span>
				</li>
				<?php } ?>
			</ul>
		</div>
		<button class="btn btn-black w100 checkout-btn">
			<span><?php echo appT('secure_checkout');?></span>
		</button>
		<div class="mt10 paypal-btn">
			
		</div>
	</div>
	<div class="m-modal hide" id="sku-select-modal">
		<div class="mask"></div>
		<div class="dialog layer">
			<span class="iconfont icon-guanbi2"></span>
			<div class="contentfill">
			</div>
			<div class="dialog-footer">
				<button class="btn btn-black w100 confirm-btn"><?php echo appT('confirm');?></button>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php if (!empty($unCheckList)){?>
	<div class="layer pb10">
		<div class="list-title flex">
			<div class="tcell">
				<p class="line"></p>
			</div>
			<p class="title"><?php echo distT('saved_items');?></p>
			<div class="tcell">
				<p class="line"></p>
			</div>
		</div>
		<ul class="cart-list mt10">
			<?php foreach($unCheckList as $value) {?>
			<li class="item" data-id="<?php echo $value['cart_id'];?>">
				<div class="w100 table<?php echo $value['out_of_stock'] || empty($value['status']) ? ' opac5' : '';?>">
					<div class="image tcell">
						<div class="image-tcell tcell">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
							<?php if (!empty($isLogin)){?>
							<button class="like-block" data-id="<?php echo $value['spu_id'];?>">
								<span class="iconfont icon-xihuan<?php echo in_array($value['spu_id'], $collectList) ? 'fill' : '';?>"></span>
							</button>
							<?php }?>
						</div>
					</div>
					<div class="info tcell">
						<a class="e2 product-name" href="<?php echo $value['url'];?>"><?php echo $value['name'];?></a>
						<div class="field-row">
							<div class="attr-content">
								<?php foreach ($value['skuAttv'][$value['sku_id']] as $k => $v) {?>
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
								<div class="product-price">
									<p class="price e1"><?php echo $value['price_format'];?></p>
									<p class="original_price e1"><?php echo $value['original_price_format'];?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="table mt8 w100">
					<div class="btn-content tcell w75 tl">
						<button class="move-cart move-to-cart"><?php echo distT('move_to_cart');?></button>
						<button class="remove-btn">
							<i class="iconfont icon-shanchu"></i>
							<span><?php echo distT('remove');?></span>
						</button>
						<?php if ($value['out_of_stock'] || empty($value['status'])) {?>
						<button class="btn-error"><?php echo empty($value['status']) ? distT('Disabled') : distT('out_of_stock');?></button>
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
	<div class="recommend-content">
		<?php $this->load('common/recommend');?>
	</div>
</div>
<?php $this->load('common/simple_footer');?>