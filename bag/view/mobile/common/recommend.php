<?php $recommendList = make('app/service/product/SpuService')->getRecommend();?>
<?php if (!empty($recommendList)) {?>
<div id="recommend-page">
	<div class="list-title flex layer">
		<div class="tcell">
			<p class="line"></p>
		</div>
		<p class="title">YOU MAT ALSO LIKE</p>
		<div class="tcell">
			<p class="line"></p>
		</div>
	</div>
	<div class="product-list mt10">
		<ul>
			<?php foreach ($recommendList as $key => $value){?>
			<li class="item" data-id="<?php echo $value['spu_id'];?>">
				<a href="<?php echo $value['url'];?>">
					<div class="img">
						<img class="lazyload" data-src="<?php echo $value['image'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>">
						<button class="like-block">
							<span class="iconfont icon-xihuan<?php echo $value['is_liked'] ? 'fill' : '';?>"></span>
						</button>
					</div>
					<div class="layer pb10">
						<p class="name"><?php echo $value['name'];?></p>
						<div class="mt10">
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