<?php $this->load('common/base_header');?>
<div class="nav-list mt12">
	<div class="layer bg-f">
		<table width="100%" >
			<tbody>
				<tr>
					<td style="min-width: 320px;vertical-align: top;">
						<div class="newservice f14">
							<p class="f500 f18">Category</p>
						</div>
					</td>
					<td width="860" class="pl30">
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
									<li data-index="1">
										<a href="">
											<img src="<?php echo siteUrl('image/computer/banner/1.png');?>">
										</a>
									</li>
									<li data-index="2">
										<a href="">
											<img src="<?php echo siteUrl('image/computer/banner/2.jpg');?>">
										</a>
									</li>
									<li data-index="3">
										<a href="">
											<img src="<?php echo siteUrl('image/computer/banner/3.jpg');?>">
										</a>
									</li>
									<li data-index="4">
										<a href="">
											<img src="<?php echo siteUrl('image/computer/banner/4.jpg');?>">
										</a>
									</li>
								</ul>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>