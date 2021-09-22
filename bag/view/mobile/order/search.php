<?php $this->load('common/order_search_header');?>
<div id="order-page">
	<?php if (empty($keyword)){?>
	<?php } else {?>
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
				<p class="status-title"><?php echo $item['status_text'];?></p>
				<div class="mt8 c6">
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
			<?php if ($item['status'] < 5){?>
			<div class="order-list-footer mt10">
				<?php if ($item['is_delete']){?>
				<button class="btn24 btn-black right repurchase-btn"><?php echo appT('repurchase');?></button>
				<?php } else {?>
				<?php if ($item['status'] == 0){?>
				<button class="btn24 delete-btn"><?php echo appT('delete');?></button>
				<button class="btn24 btn-black right repurchase-btn"><?php echo appT('repurchase');?></button>
				<?php } ?>
				<?php if ($item['status'] == 1){?>
				<button class="btn24 cancel-btn"><?php echo appT('cancel');?></button>	
				<?php }?>
				<?php if ($item['status'] == 1){?>
				<a class="btn24 btn-black right ml6" href="<?php echo url('checkout/payOrder', ['id'=>$item['order_id']]);?>"><?php echo appT('checkout');?></a>
				<?php } ?>
				<?php if ($item['status'] == 2){?>
				<button class="btn24 right ml6 refund-btn"><?php echo appT('refund');?></button>
				<?php } ?>
				<?php if ($item['status'] == 3){?>
				<button class="btn24 btn-black right ml6 complete-btn"><?php echo appT('complete');?></button>
				<?php } ?>
				<?php if ($item['status'] == 4 && !$item['is_review']){?>
				<a class="btn24 btn-black right ml6" href="<?php echo url('order/review', ['id'=>$item['order_id']]);?>"><?php echo appT('review');?></a>
				<?php } ?>
				<?php if (in_array($item['status'], [3, 4])){?>
				<a class="btn24 right ml6 bg-ef" href="<?php echo url('order/logistics', ['id'=>$item['order_id']]);?>"><?php echo appT('logistics');?></a>
				<?php } ?>
				<?php }?>
				<div class="clear"></div>
			</div>
			<?php }?>
		</div>
		<?php } ?>
	</div>
	<script type="text/javascript">
	const js_language_text = {
		no: '<?php echo appT('no');?>',
		delete: '<?php echo appT('delete');?>',
		repurchase: '<?php echo appT('repurchase');?>',
		cancel: '<?php echo appT('cancel');?>',
		checkout: '<?php echo appT('checkout');?>',
		refund: '<?php echo appT('refund');?>',
		complete: '<?php echo appT('complete');?>',
		review: '<?php echo appT('review');?>',
		logistics: '<?php echo appT('logistics');?>',
		order_repurchase_confirm: '<?php echo appT('order_repurchase_confirm');?>',
		order_delete_confirm: '<?php echo appT('order_delete_confirm');?>',
		order_refund_confirm: '<?php echo appT('order_refund_confirm');?>',
		order_complete_confirm: '<?php echo appT('order_complete_confirm');?>',
		order_cancel_confirm: '<?php echo appT('order_cancel_confirm');?>',
		loading: '<?php echo appT('loading');?>',
	};
	</script>
	<?php } ?>
	<?php }?>
</div>