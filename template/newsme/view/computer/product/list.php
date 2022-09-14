<div id="right-list">
	<div class="top-content">
		<span class="result"><?php echo $total;?> Results</span>
		<div class="sortby">
			<?php $sortArr = [
				'1' => 'Recommended',
				'2' => 'Customer Rating',
				'3' => 'Price: Low-High',
				'4' => 'Price: High-Low',
			];?><div class="show-sort">
				<span class="name"><?php echo $sortArr[$param['sort']??'1'] ?? $sortArr['1'];?></span>
				<span class="iconfont icon-xiangxia2"></span>
			</div>
			<ul class="sort-list">
				<?php foreach($sortArr as $key=>$value) {?><li<?php echo $param['sort']==$key||(!$param['sort']&&$key==1)?' class="active"':'';?>>
					<a href="<?php echo url('', ['sort'=>$key, 'page'=>0]);?>" title="<?php echo $value;?>"><?php echo $value;?></a>
					<?php if ($param['sort']==$key||(!$param['sort']&&$key==1)){?><span class="iconfont icon-xuanze"></span>
				<?php }?></li>
				<?php }?>
			</ul>
		</div>
	</div>
	<?php if (!empty($list)){?><ul class="product-list f0">
		<?php foreach ($list as $value){?><li data-pid="<?php echo $value['spu_id'];?>">
			<a href="<?php echo $value['url'];?>" title="<?php echo $value['name'];?>">
				<div class="image-content">
					<img class="lazyload" src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" alt="<?php echo $value['name'];?>">
				</div>
				<p class="name"><?php echo $value['name'];?></p>
				<div class="price-content mt12">
					<span class="price"><?php echo $value['min_price_format'];?></span>
					<?php if ($value['show_price'] && $value['min_price'] < $value['original_price']) {?><span class="original-price"><?php echo $value['original_price_format'];?></span>
				<?php }?></div>
			</a>
			<div class="like">
				<span class="iconfont icon-<?php echo $value['is_liked']?'xihuanfill':'xihuan';?> f24"></span>
			</div>
			<div class="quickview">
				<span>Quick View</span>
			</div>
			<?php if ($value['is_hot']||($value['show_price'] && $value['min_price'] < $value['original_price'])){?><div class="label">
				<?php if ($value['is_hot']){?><div class="pdt-best item">
					<span>Best Sellers</span>
				</div>
				<?php }?>
				<?php if ($value['show_price'] && $value['min_price'] < $value['original_price']) {?><div class="pdt-discount item">
	                <span><?php echo number_format(1-$value['min_price']/$value['original_price'],2)*100;?>% off</span>
	            </div>
	        <?php }?></div>
		<?php }?></li>
		<?php }?>
	</ul>
	<?php echo $total > $size ? page($size, $total) : '';?>
	<?php } elseif($empty){?><div class="empty-info">
		<img src="<?php echo siteUrl('image/common/oooops.png');?>">
		<p class="mt12 f16">No item matched. Please try with other options.</p>
	</div>
	<?php }?>
</div>
<div id="quickview-modal">
	<div class="mask"></div>
	<div class="content">
		<div class="header">
			<span>Close</span>
		</div>
		<div class="body noselect"></div>
	</div>
</div>