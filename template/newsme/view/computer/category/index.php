<?php $this->load('common/base_header');?>
<div class="category-page mt12 mb20">
	<div class="layer bg-f top">
		<?php $this->load('common/crumbs', ['crumbs'=>$crumbs]);?>
	</div>
	<div class="layer bg-f5 category-info">
	<?php if (empty($cateInfo)){?><div class="empty-info">
			<img src="<?php echo siteUrl('image/common/oooops.png');?>">
			<p class="mt12 f16">No item matched. Please try with other options.</p>
		</div>
		<?php $this->load('common/recommend');?>
		<?php } else {?><div>
			<p class="f24 f600"><?php echo $cateInfo['name_en'];?></p>
			<?php if (!empty($cateSon)) {?><ul class="category-son f0 mt12">
				<?php foreach ($cateSon as $key=>$value) {$cateSonTotal=count($cateSon);?>
				<li class="<?php echo $key>0&($key+1)%4==0?'last':''?><?php echo $cateSonTotal>4&$key<4?' mb8':'';?>">
					<a href="<?php echo url($value['name_en'].'-c', ['id'=>$value['cate_id']]);?>" title="<?php echo $value['name_en'];?>">
						<table border="0" width="100%">
							<tbody>
								<tr>
									<td width="64">
										<div class="image-content">
											<img class="lazyload" src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>">
										</div>
									</td>
									<td class="category-son-name"><?php echo $value['name_en'];?></td>
								</tr>
							</tbody>
						</table>
					</a>
				</li>
				<?php }?>
			</ul>
			<?php }?>
		</div>
		<?php }?>
	</div>
</div>
<?php $this->load('common/base_footer');?>