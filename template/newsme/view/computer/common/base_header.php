<?php $this->load('common/simple_header');?>
<div class="meau-header-content">
	<div class="meau-header">
		<div class="layer bg-f">
			<table width="100%">
				<tbody>
					<tr>
						<td width="250">
							<table width="100%">
								<tbody>
									<tr>
										<td width="28">
											<span class="iconfont icon-caidan f24 pointer" id="meau-modal-icon"></span>
											<div class="modal" id="meau-modal">
												<div class="mask"></div>
												<div class="popper">
													<div class="header">
														<span class="iconfont icon-guanbi1 f16 f600 pointer close"></span>
														<img src="<?php echo siteUrl('image/common/logo.png');?>" alt="logo">
													</div>
													<div class="body">
														<a href="<?php echo url('userInfo/');?>" class="table w100">
															<div class="tcell icon">
																<span class="iconfont icon-wode f20 f600"></span>
															</div>
															<div class="tcell f500">
																<p class="f16">Account</p>
																<p class="cg mt2 email-empty">your@email.com</p>
																<p class="cg mt2 email hidden"></p>
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
														<a href="<?php echo url('faq');?>" class="table w100">
															<div class="tcell icon">
																<span class="iconfont icon-gonglve f20 f600"></span>
															</div>
															<div class="tcell f500">
																<p class="f16">Help Center</p>
															</div>
														</a>
													</div>
													<div class="footer">
														<a href="<?php echo url('login');?>" class="block btn btn-black w100 login" title="Sign In">Sign In</a>
														<a href="<?php echo url('logout');?>" class="block btn btn-black w100 logout hidden" title="Sign Out">Sign Out</a>
													</div>
												</div>
											</div>
										</td>
										<td width="68">
											<span class="f500 f16 pointer" id="meau-modal-name">Menu</span>
										</td>
										<td class="f0">
											<a href="<?php echo url();?>">
												<img class="logo" src="<?php echo siteUrl('image/common/logo.png');?>" alt="logo">
											</a>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td>
							<div class="search-cell relative">
								<form class="relative">
									<input class="input" type="text" name="keyword" value="<?php echo iget('keyword', '');?>" placeholder="Find anything here..." autocomplete="off">
									<span class="iconfont icon-sousuo f600 f22"></span>
								</form>
								<div class="hot-search mt8 c6 relative">
									<span class="hot-icon">
										<img src="<?php echo siteUrl('image/common/hot.png');?>" alt="Hot Search">
									</span>
									<a href="<?php echo url('search', ['keyword'=>'dress'], false);?>" title="Search Dress">Dress</a>
									<a href="<?php echo url('search', ['keyword'=>'sweater'], false);?>" title="Search Sweater">Sweater</a>
									<a href="<?php echo url('search', ['keyword'=>'shit'], false);?>" title="Search Shit">Shit</a>
									<a href="<?php echo url('search', ['keyword'=>'jeans'], false);?>" title="Search Jeans">Jeans</a>
									<a href="<?php echo url('search', ['keyword'=>'skirt'], false);?>" title="Search Skirt">Skirt</a>
									<a href="<?php echo url('search', ['keyword'=>'summer'], false);?>" title="Search Summer">Summer</a>
								</div>
							</div>
						</td>
						<td width="68">
							<a id="cart" class="block right pointer relative" href="<?php echo url('cart');?>">
								<span class="iconfont icon-gouwuche f26"></span>
								<span class="f16 ml4">Cart</span>
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
