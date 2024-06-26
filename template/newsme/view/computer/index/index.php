<?php $this->load('common/base_header');?>
<div class="nav-list mt12">
	<div class="layer bg-f">
		<table width="100%" >
			<tbody>
				<tr>
					<td class="nav-category">
						<div class="newservice">
							<p class="f500 f18 mb18">Category</p>
							<ul class="f16">
								<?php foreach ($leftCate as $key=>$value){
									if ($value['icon'] && $value['level']==1){ $index=$key;?><li class="e1">
									<span class="clothes-iconfont icon-<?php echo $value['icon']??'';?>"></span>
									<a href="<?php echo url($value['name_en'], ['c'=>$value['cate_id']]);?>" title="<?php echo $value['name_en'];?>"><?php echo $value['name_en'];?></a>
									<?php $count=0; for ($i=$index+1; $i<99; $i++) {
										if (isset($leftCate[$i]) && $leftCate[$i]['level']>1 && $leftCate[$i]['is_show'] && !$leftCate[$i]['is_hot'] ){?><span class="service-slash">/</span>
									<a href="<?php echo url($leftCate[$i]['name_en'], ['c'=>$leftCate[$i]['cate_id']]);?>" title="<?php echo $leftCate[$i]['name_en'];?>"><?php echo $leftCate[$i]['name_en'];?></a>
								<?php $count++; if ($count==2) break; }}?></li>
								<?php }}?></ul>
						</div>
					</td>
					<td class="nav-banner" width="860" class="pl30">
						<div class="newnav">
							<div class="top">
								<table width="100%">
									<tbody>
										<tr>
											<?php foreach ($hotArr as $value) {?><td>
												<a href="<?php echo url($value['name_en'], ['c'=>$value['cate_id']]);?>" class="e1" title="<?php echo $value['name_en'];?>"><?php echo $value['name_en'];?></a>
											</td>
											<?php }?></tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="mt10">
							<div class="slider" id="nav-banner">
								<ul>
									<?php foreach ($banner as $value){?><li data-index="1">
										<a href="<?php echo $value['url'];?>" title="<?php echo $value['name_en'];?>">
											<img src="<?php echo $value['image'];?>" alt="<?php echo $value['name_en'];?>">
										</a>
									</li>
									<?php }?></ul>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="category-wrap">
	<div class="layer bg-f">
		<p class="title f24 f600 mb12 ml8">Popular Categories</p>
		<ul class="popular-catgory f0">
			<?php foreach($popularCate as $value){?><li>
				<a href="<?php echo url($value['name_en'], ['c'=>$value['cate_id']]);?>" title="<?php echo $value['name_en'];?>">
					<div class="image-content">
						<span class="clothes-iconfont icon-<?php echo $value['icon'];?>"></span>
					</div>
					<p class="e1"><?php echo $value['name_en'];?></p>
				</a>
			</li>
			<?php }?></ul>
	</div>
</div>
<div class="best-seller mb24">
	<div class="layer bg-f">
		<p class="title f24 f600 mb12 ml8">Best Sellers</p>
		<ul class="seller-list f0">
			<?php foreach($bestSeller as $value){?><li>
				<a href="<?php echo $value['url'];?>" title="<?php echo $value['name'];?>">
					<div class="image-content">
						<img class="lazyload" src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" alt="<?php echo $value['name'];?>">
					</div>
					<p class="f16 mt12 mb12 e2"><?php echo $value['name'];?></p>
					<div class="price-content">
						<span class="price"><?php echo $value['min_price_format'];?></span>
						<?php if ($value['show_price'] && $value['min_price'] < $value['original_price']) {?><span class="original-price"><?php echo $value['original_price_format'];?></span>
					<?php }?></div>
				</a>
			</li>
		<?php }?></ul>
		<?php if ($total > $size){ echo page($size, $total);}?>
	</div>
</div>
<?php $this->load('common/base_footer');?>