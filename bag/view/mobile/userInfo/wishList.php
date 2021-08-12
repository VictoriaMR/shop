<div id="wish-page">
	<div class="layer">
		<div class="list-title flex">
			<div class="tcell">
				<p class="line"></p>
			</div>
			<p class="title">WISH LIST</p>
			<div class="tcell">
				<p class="line"></p>
			</div>
		</div>
	</div>
	<div class="product-list mt10">
		<?php if (empty($list)) {?>
		<p class="tc f14 mt24 c6">Your wish list is empty.</p>
		<div class="mt24">
			<?php $this->load('common/recommend');?>
		</div>
		<?php } else { ?>
		<ul>
			<?php foreach ($list as $key => $value){?>
			<li class="item" data-id="<?php echo $value['spu_id'];?>">
				<a href="<?php echo $value['url'];?>">
					<div class="img">
						<img class="lazyload" data-src="<?php echo $value['image'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>">
					</div>
					<div class="layer pb10">
						<p class="name"><?php echo $value['name'];?></p>
						<div class="mt10">
							<span class="price f14 f600"><?php echo $value['min_price_format'];?></span>
							<span class="original_price"><?php echo $value['original_price_format'];?></span>
						</div>
					</div>
				</a>
				<button class="btn24 btn-black remove-btn">Romove</button>
			</li>
			<?php }?>
		</ul>
		<p class="clear"></p>
		<?php } ?>
	</div>
</div>
<script type="text/javascript">
$(function(){
	WISH.init();
});
</script>