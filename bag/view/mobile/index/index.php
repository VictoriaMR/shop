<?php if (!empty($banner)) { ?>
<div class="banner relative">
	<ul>
		<?php foreach ($banner as $key => $value){?>
		<li class="slider">
			<a href="<?php echo $value['url'];?>">
				<img src="<?php echo $value['image'];?>">
			</a>
		</li>
		<?php } ?>
	</ul>
	<ol class="pop-content">
		<?php foreach ($banner as $key => $value){?>
		<li <?php echo $key==0?'class="active"':'';?>></li>
		<?php } ?>
	</ol>
</div>
<?php } ?>
<?php if (!empty($hot_category)){?>
<div class="category-content">
	<div class="bg-round"></div>
	<ul class="hot-category-list">
		<?php foreach ($hot_category as $value){?>
		<li>
			<?php foreach ($value as $item){?>
			<a href="<?php echo $item['url'];?>" class="block f12">
				<div class="cate-img">
					<img data-src="<?php echo $item['avatar'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
				</div>
				<p class="e1"><?php echo $item['name'];?></p>
			</a>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>
<script type="text/javascript">
$('.banner').slider();
</script>