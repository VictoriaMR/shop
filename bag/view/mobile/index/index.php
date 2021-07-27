<?php if (!empty($hot_category)){?>
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
<?php } ?>