<?php $this->load('common/back_header', ['_simple_title'=>appT('order_detail')]);?>
<div id="order-page" class="mt10 layer">
	<?php if (empty($orderInfo)) { ?>
	<?php } else {?>
	<?php if ($orderInfo['base']['status'] == 1){?>
	<div class="wait-pay-content">
		<p class="mt10 f16 f600 tc">Payment Pending</p>
		<p class="mt10 f14 tc">Your order will be canceled after more than <?php echo $orderInfo['base']['last_pay_time'];?> days.</p>
		<a href="<?php echo url('checkout/payOrder', ['id'=>$orderInfo['base']['order_id']]);?>" class="btn">PROCEED TO PAYMENT</a>
	</div>
	<?php } else {?>
	<div class="status-history">
		<p class="f14 f600">Order status:</p>
		<ul class="mt16">
			<?php $statusCount = count($orderInfo['status_history']) - 1;
			 foreach ($orderInfo['status_history'] as $key => $value){?>
			<li>
				<div class="table relative">
					<div class="tcell s-line-content<?php echo $key==0?' vb':'';?>">
						<?php if ($key > 0){?>
						<div class="s-line"></div>
						<?php }?>
						<div class="s-circular"></div>
						<?php if ($statusCount > $key) {?>
						<div class="s-line"></div>
						<?php } ?>
					</div>
					<div class="tcell s-time">
						<span><?php echo $value['add_time'];?></span>
						<span>:</span>
					</div>
					<div class="tcell s-info"><?php echo $value['info'];?></div>
				</div>
			</li>
			<?php if ($statusCount > $key) {?>
			<li class="empty-line-content">
				<div class="empty-line"></div>
			</li>
			<?php } ?>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
	<div class="order-list mt10">
		<div class="item" data-id="<?php echo $orderInfo['base']['order_id'];?>">
			<p class="status-title"><?php echo $orderInfo['base']['status_text'];?></p>
			<div class="mt8 c6">
				<p class="left e1 w50"><?php echo $orderInfo['base']['add_time_format'];?></p>
				<p class="e1 w50 right tr"><?php echo appT('no');?>: <?php echo $orderInfo['base']['order_no'];?></p>
				<div class="clear"></div>
			</div>
			<div class="order-product-content">
			<?php foreach ($orderInfo['product'] as $value){?>
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
									<p class="price e1"><?php echo $value['price_format'];?></p>
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
			<p class="f14 f700 mt10 tr">Total: <?php echo $orderInfo['base']['order_total_format'];?></p>
			<?php if ($orderInfo['base']['status'] < 5){?>
			<div class="order-list-footer mt16">
				<?php if ($orderInfo['base']['is_delete']){?>
				<button class="btn24 btn-black right repurchase-btn"><?php echo appT('repurchase');?></button>
				<?php } else {?>
				<?php if ($orderInfo['base']['status'] == 0){?>
				<button class="btn24 delete-btn"><?php echo appT('delete');?></button>
				<button class="btn24 btn-black right repurchase-btn"><?php echo appT('repurchase');?></button>
				<?php } ?>
				<?php if ($orderInfo['base']['status'] == 1){?>
				<button class="btn24 cancel-btn"><?php echo appT('cancel');?></button>	
				<?php }?>
				<?php if ($orderInfo['base']['status'] == 1){?>
				<a class="btn24 btn-black right ml6" href="<?php echo url('checkout/payOrder', ['id'=>$orderInfo['base']['order_id']]);?>"><?php echo appT('checkout');?></a>
				<?php } ?>
				<?php if ($orderInfo['base']['status'] == 2){?>
				<button class="btn24 right ml6 refund-btn"><?php echo appT('refund');?></button>
				<?php } ?>
				<?php if ($orderInfo['base']['status'] == 3){?>
				<button class="btn24 btn-black right ml6 complete-btn"><?php echo appT('complete');?></button>
				<?php } ?>
				<?php if ($orderInfo['base']['status'] == 4 && !$orderInfo['base']['is_review']){?>
				<a class="btn24 btn-black right ml6" href="<?php echo url('order/review', ['id'=>$orderInfo['base']['order_id']]);?>"><?php echo appT('review');?></a>
				<?php } ?>
				<?php if (in_array($orderInfo['base']['status'], [3, 4])){?>
				<a class="btn24 right ml6 bg-ef" href="<?php echo url('order/logistics', ['id'=>$orderInfo['base']['order_id']]);?>"><?php echo appT('logistics');?></a>
				<?php } ?>
				<?php }?>
				<div class="clear"></div>
			</div>
			<?php }?>
		</div>
		<div class="item">
			<p class="status-title">Order Summary</p>
			<div class="order-content">
				<?php foreach ($orderInfo['fee_list'] as $value){?>
				<div class="row<?php echo $value['type']==0?' originalprice-row':'';?>">
					<p class="name left"><?php echo $value['name'];?>:</p>
					<p class="value f600 right"><?php echo $value['value_format'];?></p>
					<p class="clear"></p>
				</div>
				<?php }?>
				<div class="line mt12"></div>
				<div class="row mt12 f600">
					<p class="name left">TOTAL:</p>
					<p class="value f14 f700 right"><?php echo $orderInfo['base']['order_total_format'];?></p>
					<p class="clear"></p>
				</div>
			</div>
		</div>
		<div class="item">
			<p class="status-title"><?php echo appT('shipping_address');?></p>
			<a href="javascript:;" class="address-info-content mt12">
				<div class="address-info">
					<p><?php echo trim($orderInfo['shipping_address']['first_name'].' '.$orderInfo['shipping_address']['last_name']);?></p>
					<p><?php echo $orderInfo['shipping_address']['city'].' '.($orderInfo['shipping_address']['state'] ? $orderInfo['shipping_address']['state'].' ' : '').make('app/service/address/Country')->getName($orderInfo['shipping_address']['country_code2']).', '.$orderInfo['shipping_address']['postcode'];?></p>
					<p><?php echo trim($orderInfo['shipping_address']['address1'].' '.$orderInfo['shipping_address']['address2']);?></p>
					<p><?php echo $orderInfo['shipping_address']['phone'];?></p>
					<?php if (!empty($orderInfo['shipping_address']['tax_number'])){?>
					<p><span class="f12 c6"><?php echo appT('tax');?>:&nbsp;</span><?php echo $orderInfo['shipping_address']['tax_number'];?></p>
					<?php } ?>
				</div>
			</a>
			<div class="border mt6"></div>
		</div>
		<div class="item">
			<p class="status-title"><?php echo appT('billing_address');?></p>
			<?php if ($orderInfo['base']['status'] == 1){?>
			<button class="btn24 btn-black right address-edit-btn" data-order_id="<?php echo $orderInfo['base']['order_id'];?>">Edit</button>
			<?php } ?>
			<a href="javascript:;" class="address-info-content mt12">
				<div class="address-info">
					<p><?php echo trim($orderInfo['billing_address']['first_name'].' '.$orderInfo['billing_address']['last_name']);?></p>
					<p><?php echo $orderInfo['billing_address']['city'].' '.($orderInfo['billing_address']['state'] ? $orderInfo['billing_address']['state'].' ' : '').make('app/service/address/Country')->getName($orderInfo['billing_address']['country_code2']).', '.$orderInfo['billing_address']['postcode'];?></p>
					<p><?php echo trim($orderInfo['billing_address']['address1'].' '.$orderInfo['billing_address']['address2']);?></p>
					<p><?php echo $orderInfo['billing_address']['phone'];?></p>
					<?php if (!empty($orderInfo['billing_address']['tax_number'])){?>
					<p><span class="f12 c6">Tax:&nbsp;</span><?php echo $orderInfo['billing_address']['tax_number'];?></p>
					<?php } ?>
				</div>
			</a>
			<div class="border mt6"></div>
		</div>
	</div>
	<?php } ?>
</div>