<?php $this->load('common/base_header');?>
<div class="nav-list mt12 mb20">
	<div class="layer bg-f">
		<table width="100%" >
			<tbody>
				<tr>
					<td style="min-width: 268px;vertical-align: top;background-color: #F7F9FA;border-radius: 12px;">
						<div class="newservice f14">
							<p class="f500 f18 mb18">Category</p>
							<ul>
								<?php foreach ($cateArr ?? [] as $key=>$value){
									if ($value['level'] == 1 && $value['icon']){ $index=$key;?>
								<li class="e1">
									<span class="clothes-iconfont icon-<?php echo $value['icon']??'';?>"></span>
									<a href="<?php echo url($value['name_en'].'-c', ['id'=>$value['cate_id']]);?>"><?php echo $value['name_en'];?></a>
									<?php if (isset($cateArr[$index+1]) && $cateArr[$index+1]['level']>1){?>
									<span class="service-slash">/</span>
									<a href="<?php echo url($cateArr[$index+1]['name_en'].'-c', ['id'=>$cateArr[$index+1]['cate_id']]);?>"><?php echo $cateArr[$index+1]['name_en'];?></a>
									<?php }?>
									<?php if (isset($cateArr[$index+2]) && $cateArr[$index+2]['level']>1){?>
									<span class="service-slash">/</span>
									<a href="<?php echo url($cateArr[$index+2]['name_en'].'-c', ['id'=>$cateArr[$index+2]['cate_id']]);?>"><?php echo $cateArr[$index+2]['name_en'];?></a>
									<?php }?>
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
											<td>
												<a href="<?php echo url('dresses-c', ['id'=>104]);?>" class="e1">Dresses</a>
											</td>
											<td>
												<a href="<?php echo url('tops-c', ['id'=>108]);?>" class="e1">Tops</a>
											</td>
											<td>
												<a href="<?php echo url('skirts-c', ['id'=>127]);?>" class="e1">Skirts</a>
											</td>
											<td>
												<a href="<?php echo url('pants-c', ['id'=>132]);?>" class="e1">Pants</a>
											</td>
											<td>
												<a href="<?php echo url('shorts-c', ['id'=>138]);?>" class="e1">Shorts</a>
											</td>
											<td>
												<a href="<?php echo url('sportswear-c', ['id'=>141]);?>" class="e1">Sportswear</a>
											</td>
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
<?php $this->load('common/base_footer');?>