<?php $this->load('common/nav_header');?>
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
<?php if (!empty($cateList)){?>
<div class="category-content">
	<div class="bg-round"></div>
	<div class="pb4 bg-f">
		<ul class="hot-category-list">
			<?php foreach ($cateList as $item){?>
			<li>
				<?php foreach ($item as $value){?>
				<a href="<?php echo $value['url'];?>" class="item">
					<div class="table w100">
						<div class="cate-img">
							<img data-src="<?php echo $value['image'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
						</div>
					</div>
					<p class="e1"><?php echo $value['name'];?></p>
				</a>
				<?php } ?>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php } ?>
<?php $this->load('common/nav_footer');?>