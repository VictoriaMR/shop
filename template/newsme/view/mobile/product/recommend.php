<?php $list = $list??service('product/Spu')->getRecommend(1, 6);$isLogin=empty($show_like)?false:userId();?>
<?php if (!empty($list)) {?><div id="recommend-page">
	<?php if (!empty($title)){?><div class="list-title flex layer">
		<div class="tcell">
			<p class="line"></p>
		</div>
		<p class="title"><?php echo appT('you_may_also_like');?></p>
		<div class="tcell">
			<p class="line"></p>
		</div>
	</div>
	<?php }?>
	<div class="product-list">
		<ul>
			<?php foreach ($list as $key => $value){?>
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
</div>
<?php } ?>