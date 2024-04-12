<?php $recommendList = service('product/Spu')->getRecommend(1, 6);?>
<?php if (!empty($recommendList)) {?><div id="recommend-page">
	<p class="title f24 f600 mb12 ml8">You May Also Like</p>
	<div class="product-list">
		<ul class="f0">
			<?php foreach ($recommendList as $key => $value){?><li>
				<a href="<?php echo $value['url'];?>" title="<?php echo $value['name'];?>">
					<div class="image-content">
						<img class="lazyload" src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>">
					</div>
					<p class="f16 mt12 mb12 e2"><?php echo $value['name'];?></p>
					<div class="price-content">
						<span class="price"><?php echo $value['min_price_format'];?></span>
						<?php if ($value['show_price'] && $value['min_price'] < $value['original_price']) {?><span class="original-price"><?php echo $value['original_price_format'];?></span>
					<?php }?></div>
				</a>
			</li>
		<?php }?></ul>
	</div>
</div>
<?php } ?>