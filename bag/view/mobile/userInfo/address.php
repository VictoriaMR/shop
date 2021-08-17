<div id="address-page" class="layer mt20">
	<div class="list-title flex">
		<div class="tcell">
			<p class="line"></p>
		</div>
		<p class="title">MY ADDRESSES</p>
		<div class="tcell">
			<p class="line"></p>
		</div>
	</div>
	<div class="address-content mt18">
		<div class="title">
			<span>ADDRESSES</span>
			<button class="btn24 btn-black right add-new-address">Add an Address</button>
		</div>
		<?php if (empty($list)) {?>
		<p class="f18 f600 tc mt32">Your address is empty</p>
		<p class="f14 mt18 tc">Please click the add button to add a new address.</p>
		<?php } else {?>
		<ul class="address-list mt22" data-page="<?php echo $page;?>" data-size="<?php echo $size;?>">
			<?php foreach ($list as $value){?>
			<li class="item<?php echo $value['is_default']?' active':'';?>" data-id="<?php echo $value['address_id'];?>">
				<div class="info">
					<p class="e2"><?php echo $value['first_name'].' '.$value['last_name'];?></p>
					<p class="e2"><?php echo $value['phone'];?></p>
					<p class="e2"><?php echo $value['address1'].' '.$value['address2'];?></p>
					<p class="e2"><?php echo $value['city'];?> / <?php echo $value['state'];?> / <?php echo $value['country'];?> / <?php echo $value['postcode'];?></p>
					<?php if (!empty($value['tax_number'])){?>
					<p class="e2"><?php echo $value['tax_number'];?></p>
					<?php }?>
					<button class="btn24 default-btn<?php echo $value['is_default']?' active':'';?>">DEFAULT</button>
				</div>
				<div class="btn-content mt14">
					<button class="btn24 btn-black edit-btn">Edit</button>
					<button class="btn24 ml16 delete-btn">Delete</button>
				</div>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</div>
</div>
<?php $this->load('common/address');?>
<script type="text/javascript">
$(function(){
	ADDRESS.init();
});
</script>