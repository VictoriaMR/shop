<?php $this->load('common/back_header', ['_simple_title'=>distT('my_coupon')]);?>
<div id="coupon-page">
	<div class="layer">
		<div class="list-title flex">
			<div class="tcell">
				<p class="line"></p>
			</div>
			<p class="title"><?php echo distT('coupon_list');?></p>
			<div class="tcell">
				<p class="line"></p>
			</div>
		</div>
		<div class="address-content mt18">
			<?php if (empty($list)) {?>
			<p class="empty-tips"><?php echo distT('coupon_empty');?></p>
			<?php } else {?>
			<ul class="address-list mt22" data-page="<?php echo $page;?>" data-size="<?php echo $size;?>">
				<?php foreach ($list as $value){?>
				<li class="item<?php echo $value['is_default']||$value['is_bill']?' active':'';?>" data-id="<?php echo $value['address_id'];?>">
					<div class="info">
						<p class="e2"><?php echo $value['first_name'].' '.$value['last_name'];?></p>
						<p class="e2"><?php echo $value['phone'];?></p>
						<p class="e2"><?php echo $value['address1'].' '.$value['address2'];?></p>
						<p class="e2"><?php echo $value['city'];?> / <?php echo $value['state'];?> / <?php echo $value['country'];?> / <?php echo $value['postcode'];?></p>
						<?php if (!empty($value['tax_number'])){?>
						<p class="e2"><?php echo $value['tax_number'];?></p>
						<?php }?>
						<span class="iconfont default-btn icon-wuliu<?php echo $value['is_default']?' active':'';?>"></span>
						<span class="iconfont default-bill-btn icon-dingdan<?php echo $value['is_bill']?' active':'';?>"></span>
					</div>
					<div class="btn-content mt14">
						<button class="btn24 btn-black edit-btn"><?php echo distT('edit');?></button>
						<button class="btn24 ml16 delete-btn"><?php echo distT('delete');?></button>
					</div>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>
		</div>
	</div>
	<?php if (empty($list)){?>
	<?php $this->load('common/recommend');?>
	<?php }?>
</div>
<?php $this->load('common/simple_footer');?>