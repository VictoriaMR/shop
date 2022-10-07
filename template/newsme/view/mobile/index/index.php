<?php $this->load('common/nav_header');?>
<?php if (!empty($banner)) { ?>
<div class="banner relative" id="top-banner">
	<ul>
		<?php foreach ($banner as $key => $value){?><li class="slider">
			<a href="<?php echo $value['url'];?>">
				<img src="<?php echo $value['image'];?>" alt="<?php echo $value['name_en'];?>">
			</a>
		</li>
		<?php } ?>
	</ul>
	<ol class="pop-content">
		<?php foreach ($banner as $key => $value){?><li <?php echo $key==0?'class="active"':'';?>></li>
		<?php } ?>
	</ol>
</div>
<?php } ?>
<?php if (!empty($popularCate)){?>
<div class="category-content">
	<div class="bg-round"></div>
	<div class="pb4 bg-f">
		<ul class="hot-category-list">
			<?php foreach ($popularCate as $item){?><li>
				<?php foreach ($item as $value){?><a href="<?php echo url($value['name_en'], ['c'=>$value['cate_id']]);?>" title="<?php echo $value['name_en'];?>" class="item">
					<div class="image-content">
						<span class="clothes-iconfont icon-<?php echo $value['icon'];?>"></span>
					</div>
					<p class="e1"><?php echo $value['name_en'];?></p>
				</a>
				<?php }?>
			</li>
			<?php }?>
		</ul>
	</div>
</div>
<?php } ?>
<?php if (!empty($bestSeller)) {?>
<?php $this->load('product/recommend', ['title'=>false, 'show_like'=>true, 'list'=>$bestSeller]);?>
<?php if ($total > $size){ echo page($size, $total);}?>
<?php } ?>
<?php $this->load('common/nav_footer');?>