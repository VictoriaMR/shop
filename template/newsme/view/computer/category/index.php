<?php $this->load('common/base_header');?>
<div class="category-page mt12 mb20">
	<?php $this->load('common/crumbs', ['crumbs'=>$crumbs]);?>
	<div class="layer bg-f<?php echo empty($cateInfo)?'':'5';?> category-info">
		<?php if (empty($cateInfo)){?><div class="empty-info">
			<img src="<?php echo siteUrl('image/common/oooops.png');?>">
			<p class="mt12 f16">No item matched. Please try with other options.</p>
		</div>
		<?php $this->load('product/recommend');?>
		<?php } else {?><p class="f24 f600"><?php echo $cateInfo['name'];?></p>
		<?php if (!empty($cateSon)) {?><ul class="category-son f0 mt12">
			<?php foreach ($cateSon as $key=>$value) {$cateSonTotal=count($cateSon);?><li class="<?php echo $key>0&($key+1)%4==0?'last':''; echo ($cateSonTotal>4&$key<4)||($cateSonTotal>8&&($key>3&$key<8))?' mb8':'';?>">
				<a href="<?php echo url($value['name_en'], ['c'=>$value['cate_id']]);?>" title="<?php echo $value['name'];?>">
					<table border="0" width="100%">
						<tbody>
							<tr>
								<td width="64">
									<div class="image-content">
										<img class="lazyload" src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" alt="<?php echo $value['name'];?>">
									</div>
								</td>
								<td class="category-son-name"><?php echo $value['name'];?></td>
							</tr>
						</tbody>
					</table>
				</a>
			</li>
			<?php }?>
		</ul>
	<?php }}?></div>
	<?php if (!empty($filter) || !empty($list)){?><div class="layer0 bg-f product-container">
		<?php $this->load('product/filter', array_merge(['list'=>$filter], $param));?>
		<?php $this->load('product/list', ['list'=>$list, 'total'=>$total, 'param'=>$param, 'size'=>$size]);?>
		<div class="clear"></div>
	</div>
	<?php }?>
</div>
<?php $this->load('common/base_footer');?>