<?php $this->load('common/back_header', ['_simple_title'=>appT('order_deleted')]);?>
<div id="order-page">
	<?php if (empty($list)){ ?>
	<div class="order-list-empty layer tc">
		<div>
			<img src="<?php echo siteUrl('image/common/no_info.png');?>">
		</div>
		<p class="mt20"><?php echo appT('empty_info');?></p>
	</div>
	<?php } else {?>
	<div class="order-list layer8 mt10" data-page="<?php echo $page;?>" data-size="<?php echo $size;?>">
		<?php foreach ($list as $item){?>
		<div class="item" data-id="<?php echo $item['order_id'];?>">
			<a class="block" href="<?php echo $item['url'];?>">
				<div class="c6">
					<p class="left e1 w50"><?php echo $item['add_time_format'];?></p>
					<p class="e1 w50 right tr"><?php echo appT('no');?>: <?php echo $item['order_no'];?></p>
					<div class="clear"></div>
				</div>
				<div class="order-product-content">
				<?php foreach ($item['product'] as $value){?>
				<div class="product-item mt12">
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
									<?php if (!empty($v['image'])) {?>
									<div class="attr-item attr-image">
										<img data-src="<?php echo $v['image'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
										<span class="e1"><?php echo $v['attv_name'];?></span>
										<div class="clear"></div>
									</div>
									<?php } else { ?>
									<div class="attr-item">
										<span class="e1"><?php echo $v['attv_name'];?></span>
									</div>
									<?php } ?>
									<?php } ?>
								</div>
								<div class="edit-content">
									<div class="product-price">
										<p class="price e1"><?php echo $item['currency_symbol'].$value['price'];?></p>
									</div>
									<div class="quantity tcell w25 tr">
										<button class="quantity-num">x <?php echo $value['quantity'];?></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php }?>
				</div>
			</a>
			<div class="order-list-footer mt10">
				<button class="btn24 btn-black right repurchase-btn"><?php echo appT('repurchase');?></button>
				<div class="clear"></div>
			</div>
		</div>
		<?php } ?>
	</div>
	<script type="text/javascript">
	const js_language_text = {
		no: '<?php echo appT('no');?>',
		repurchase: '<?php echo appT('repurchase');?>',
		order_repurchase_confirm: '<?php echo appT('order_repurchase_confirm');?>',
		loading: '<?php echo appT('loading');?>',
	};
	</script>
	<?php } ?>
</div>