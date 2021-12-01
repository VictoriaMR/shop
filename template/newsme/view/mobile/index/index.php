<?php $this->load('common/nav_header');?>
<?php if (!empty($banner)) { ?>
<div class="banner relative" id="top-banner">
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
<?php if (!empty($cateLeftList)) {?>
<div class="layer mt12">
	<div class="left w50 pr4">
		<div class="banner relative" id="left-banner">
			<ul>
				<?php foreach ($cateLeftList as $key => $value){?>
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
	</div>
	<div class="right w50 pl4">
		
	</div>
	<div class="clear"></div>
</div>
<?php }?>
<?php if (!empty($recommendList)) {
	$isLogin = userId();
?>
<div id="recommend-page">
	<div class="product-list">
		<ul>
			<?php foreach ($recommendList as $key => $value){?>
			<li class="item" data-id="<?php echo $value['spu_id'];?>">
				<a href="<?php echo $value['url'];?>">
					<div class="table w100">
						<div class="img">
							<img class="lazyload" data-src="<?php echo $value['image'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>">
							<?php if (!empty($isLogin)){?>
							<button class="like-block">
								<span class="iconfont icon-xihuan<?php echo $value['is_liked'] ? 'fill' : '';?>"></span>
							</button>
							<?php }?>
						</div>
					</div>
					<div class="layer4 pb4">
						<p class="name"><?php echo $value['name'];?></p>
						<div class="mt4">
							<span class="price f14 f600"><?php echo $value['min_price_format'];?></span>
							<span class="original_price"><?php echo $value['original_price_format'];?></span>
						</div>
					</div>
				</a>
			</li>
			<?php }?>
		</ul>
		<p class="clear"></p>
	</div>
	<?php echo make('app/service/Widget')->pageBar($page, ceil($total/$size));?>
</div>
<?php } ?>
<?php $this->load('common/nav_footer');?>