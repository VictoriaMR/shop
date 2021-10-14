<?php $this->load('common/back_header', ['_simple_title'=>appT('my_address')]);?>
<div id="address-page" class="layer mt20">
	<div class="list-title flex">
		<div class="tcell">
			<p class="line"></p>
		</div>
		<p class="title"><?php echo appT('address_list');?></p>
		<div class="tcell">
			<p class="line"></p>
		</div>
	</div>
	<div class="address-content mt18">
		<?php if (userId()){?>
		<div class="title">
			<span><?php echo appT('addresses');?></span>
			<button class="btn24 btn-black right add-new-address"><?php echo appT('add_address');?></button>
		</div>
		<?php }?>
		<?php if (empty($list)) {?>
		<p class="tc f14 mt24 c6"><?php echo appT('address_empty');?></p>
		<?php if (userId()){?>
		<p class="f14 mt18 tc"><?php echo appT('click_to_add_address');?></p>
		<?php }?>
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
					<button class="btn24 btn-black edit-btn"><?php echo appT('edit');?></button>
					<button class="btn24 ml16 delete-btn"><?php echo appT('delete');?></button>
				</div>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</div>
</div>
<?php if (userId()){?>
<?php $this->load('common/address');?>
<script type="text/javascript">
const js_language_text = {
	set_default_confirm: '<?php echo appT('set_default_confirm');?>',
	set_default_bill_confirm: '<?php echo appT('set_default_bill_confirm');?>',
};
</script>
<?php }?>