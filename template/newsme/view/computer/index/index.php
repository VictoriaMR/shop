<?php $this->load('common/base_header');?>
<div class="nav-list mt12">
	<div class="layer bg-f">
		<table width="100%" >
			<tbody>
				<tr>
					<td style="min-width: 268px;vertical-align: top;background-color: #F7F9FA;border-radius: 12px;">
						<div class="newservice">
							<p class="f500 f18 mb18">Category</p>
							<ul class="f16">
								<?php foreach ($cateArr ?? [] as $key=>$value){
									if ($value['level'] == 1 && $value['icon']){ $index=$key;?>
								<li class="e1">
									<span class="clothes-iconfont icon-<?php echo $value['icon']??'';?>"></span>
									<a href="<?php echo url($value['name_en'].'-c', ['id'=>$value['cate_id']]);?>"><?php echo $value['name_en'];?></a>
									<?php $count=0; for ($i=$index+1; $i<999; $i++) {?>
									<?php if (isset($cateArr[$i]) && $cateArr[$i]['level']>1 && $cateArr[$i]['is_show'] && !$cateArr[$i]['is_hot'] ){?>
									<span class="service-slash">/</span>
									<a href="<?php echo url($cateArr[$i]['name_en'].'-c', ['id'=>$cateArr[$i]['cate_id']]);?>"><?php echo $cateArr[$i]['name_en'];?></a>
									<?php $count++; if ($count==2) break; ?>
									<?php }}?>
								</li>	
								<?php }}?>
							</ul>
						</div>
					</td>
					<td width="860" class="pl30" style="vertical-align: top;">
						<div class="newnav">
							<div class="top">
								<table width="100%">
									<tbody>
										<tr>
											<?php foreach ($hotArr as $value) {?>
											<td>
												<a href="<?php echo url($value['name_en'].'-c', ['id'=>$value['cate_id']]);?>" class="e1"><?php echo $value['name_en'];?></a>
											</td>
											<?php }?>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="mt10">
							<div class="slider" id="nav-banner">
								<ul>
									<?php foreach ($banner as $value){?>
									<li data-index="1">
										<a href="<?php echo $value['url'];?>">
											<img src="<?php echo $value['image'];?>">
										</a>
									</li>
									<?php }?>
								</ul>
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
		<p class="title f24 f600 mb20">Popular Categories</p>
		<ul class="popular-catgory">
			<?php foreach($popularCate as $value){?>
			<li></li>
			<?php }?>
		</ul>
	</div>
</div>
<?php $this->load('common/base_footer');?>