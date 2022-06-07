<?php $this->load('common/simple_header');?>
<div class="meau-header">
	<div class="layer">
		<table width="100%">
			<tbody>
				<tr>
					<td width="40%"></td>
					<td width="20%"></td>
					<td width="40%"></td>
				</tr>
			</tbody>
		</table>
		<div class="left">
			<table width="100%" style="height: 100%;">
				<tbody>
					<tr>
						<td width="28">
							<span class="iconfont icon-caidan f24 pointer" id="meau-modal-icon"></span>
							<div class="modal" id="meau-modal">
								<div class="mask"></div>
								<div class="popper">
									<div class="header">
										<span class="iconfont icon-guanbi1 f16 f600 pointer close"></span>
										<img src="<?php echo siteUrl('image/common/logo.png');?>">
									</div>
									<div class="body">
										<a href="<?php echo url('userInfo');?>" class="table w100">
											<div class="tcell icon">
												<span class="iconfont icon-wode f20 f600"></span>
											</div>
											<div class="tcell f500">
												<p class="f16">Account</p>
												<?php if(userId()){?><p class="cg mt2"><?php echo session()->get('home_info', 'email');?></p><?php }?>
											</div>
										</a>
										<a href="<?php echo url('order/list');?>" class="table w100">
											<div class="tcell icon">
												<span class="iconfont icon-dingdan f20 f600"></span>
											</div>
											<div class="tcell f500">
												<p class="f16">My Orders</p>
											</div>
										</a>
										<a href="<?php echo url('userInfo/history');?>" class="table w100">
											<div class="tcell icon">
												<span class="iconfont icon-shizhong f20 f600"></span>
											</div>
											<div class="tcell f500">
												<p class="f16">Recently Viewed</p>
											</div>
										</a>
										<a href="<?php echo url('userInfo/wishList');?>" class="table w100">
											<div class="tcell icon">
												<span class="iconfont icon-biaoxing f20 f600"></span>
											</div>
											<div class="tcell f500">
												<p class="f16">Favorites</p>
											</div>
										</a>
										<a href="<?php echo url('helper');?>" class="table w100">
											<div class="tcell icon">
												<span class="iconfont icon-gonglve f20 f600"></span>
											</div>
											<div class="tcell f500">
												<p class="f16">Help Center</p>
											</div>
										</a>
									</div>
									<div class="footer">
										<a href="<?php echo url(userId()?'login/logout':'login');?>" class="block btn btn-black w100"><?php echo userId()?'Sign Out':'Sign In';?></a>
									</div>
								</div>
							</div>
						</td>
						<td width="43">
							<span class="f500 f16 pointer" id="meau-modal-name">Menu</span>
						</td>
						<td>
							
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="left middle">
			<div class="tcell">
				<img src="<?php echo siteUrl('image/common/logo.png');?>">
			</div>
		</div>
		<div class="left">
			123123
		</div>
		<div class="clear"></div>
	</div>
</div>
<div class="nav-list">
	<div class="layer">
		<table width="100%">
			<tbody>
				<tr>
					<td>
						<a href="">Ceiling Lights</a>
					</td>
					<td>
						<a href="">Ceiling Lights</a>
					</td>
					<td>
						<a href="">Ceiling Lights</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>