<div id="userinfo-page">
	<div class="top layer16 mt22">
		<div class="tcell image-avatar f0">
			<img data-src="<?php echo $info['avatar'] ?? '';?>" src="<?php echo siteUrl('image/common/female.jpg');?>" class="lazyload">
		</div>
		<div class="tcell name-content">
			<?php if (empty($info['mem_id'])){?>
			<a href="<?php echo url('login');?>" class="f20 f600"><?php echo distT('login');?> / <?php echo distT('register');?></a>
			<?php } else {?>
			<?php if (empty($info['name'])){?>
			<p class="e1 name f16 f600"><?php echo $info['email'];?></p>
			<?php } else { ?>
			<p class="e1 name f16 f600"><?php echo $info['name'];?></p>
			<p class="e1 mt4"><?php echo $info['email'];?></p>
			<?php } ?>
			<span class="iconfont icon-fankui"></span>
			<?php }?>
		</div>
	</div>
	<div class="nav-list mt22 f0">
		<ul>
			<li>
				<a href="<?php echo url('userInfo/wishList');?>" class="block">
					<span class="iconfont icon-xihuan"></span>
					<p><?php echo distT('wish');?></p>
					<?php if ($collectionTotal > 0){?>
					<span class="red-number"><?php echo $collectionTotal > 99 ? 99 : $collectionTotal;?></span>
					<?php }?>
				</a>
			</li>
			<li>
				<a href="<?php echo url('userInfo/history');?>" class="block">
					<span class="iconfont icon-zuji"></span>
					<p><?php echo distT('history');?></p>
					<?php if ($historyTotal > 0){?>
					<span class="red-number"><?php echo $historyTotal > 99 ? 99 : $historyTotal;?></span>
					<?php }?>
				</a>
			</li>
			<li>
				<a href="<?php echo url('userInfo/address');?>" class="block">
					<span class="iconfont icon-zuobiao"></span>
					<p><?php echo distT('address');?></p>
					<?php if ($addressTotal > 99 ? 99 : $addressTotal > 0){?>
					<span class="red-number"><?php echo $addressTotal > 99 ? 99 : $addressTotal;?></span>
					<?php }?>
				</a>
			</li>
			<li>
				<a href="<?php echo url('userInfo/coupon');?>" class="block">
					<span class="iconfont icon-youhuiquan"></span>
					<p><?php echo distT('coupon');?></p>
				</a>
			</li>
		</ul>
	</div>
	<div class="layer">
		<div class="order-content mt12">
			<a class="relative top-title block" href="<?php echo url('order');?>">
				<p class="f14 f600 left"><?php echo distT('my_order');?></p>
				<p class="right c9" >
					<span><?php echo distT('all');?></span>
					<span class="iconfont icon-xiangyou1"></span>
				</p>
				<div class="clear"></div>
			</a>
			<div class="mt10 order-status-list">
				<ul class="f0">
					<li>
						<a href="<?php echo url('order', ['status'=>1]);?>" class="block">
							<span class="iconfont icon-xinyongqia"></span>
							<p><?php echo distT('pending');?></p>
							<?php if (!empty($orderTotal[1])){?>
							<span class="red-number"><?php echo $orderTotal[1] > 99 ? 99 : $orderTotal[1];?></span>
							<?php } ?>
						</a>
					</li>
					<li>
						<a href="<?php echo url('order', ['status'=>2]);?>" class="block">
							<span class="iconfont icon-zengsong"></span>
							<p><?php echo distT('processing');?></p>
							<?php if (!empty($orderTotal[2])){?>
							<span class="red-number"><?php echo $orderTotal[2] > 99 ? 99 : $orderTotal[2];?></span>
							<?php } ?>
						</a>
					</li>
					<li>
						<a href="<?php echo url('order', ['status'=>3]);?>" class="block">
							<span class="iconfont icon-wuliu"></span>
							<p><?php echo distT('shipped');?></p>
							<?php if (!empty($orderTotal[3])){?>
							<span class="red-number"><?php echo $orderTotal[3] > 99 ? 99 : $orderTotal[3];?></span>
							<?php } ?>
						</a>
					</li>
					<li>
						<a href="<?php echo url('order', ['status'=>4]);?>" class="block">
							<span class="iconfont icon-xinxi"></span>
							<p><?php echo distT('review');?></p>
							<?php if (!empty($orderTotal[4])){?>
							<span class="red-number"><?php echo $orderTotal[4] > 99 ? 99 : $orderTotal[4];?></span>
							<?php } ?>
						</a>
					</li>
					<li>
						<a href="<?php echo url('order', ['status'=>5]);?>" class="block">
							<span class="iconfont icon-tuikuan"></span>
							<p><?php echo distT('refund');?></p>
							<?php if (!empty($orderTotal[5])){?>
							<span class="red-number"><?php echo $orderTotal[5] > 99 ? 99 : $orderTotal[5];?></span>
							<?php } ?>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="order-content mt12">
			<a class="relative top-title block" href="<?php echo url('order');?>">
				<p class="f14 f600 left"><?php echo distT('more_services');?></p>
				<div class="clear"></div>
			</a>
			<div class="mt10 order-status-list">
				<ul class="f0">
					<li>
						<a href="<?php echo url('order', ['status'=>1]);?>" class="block">
							<span class="iconfont icon-xinyongqia"></span>
							<p>Pending</p>
							<?php if (!empty($orderTotal[1])){?>
							<span class="red-number"><?php echo $orderTotal[1] > 99 ? 99 : $orderTotal[1];?></span>
							<?php } ?>
						</a>
					</li>
					<li>
						<a href="<?php echo url('order', ['status'=>2]);?>" class="block">
							<span class="iconfont icon-zengsong"></span>
							<p>Processing</p>
							<?php if (!empty($orderTotal[2])){?>
							<span class="red-number"><?php echo $orderTotal[2] > 99 ? 99 : $orderTotal[2];?></span>
							<?php } ?>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<?php if (empty($info)){?>
		<a class="btn btn-black w100 mt32 block" href="<?php echo url('login');?>"><?php echo distT('login');?> / <?php echo distT('register');?></a>
		<?php } else {?>
		<a class="btn btn-black w100 mt32 block" href="<?php echo url('login/logout');?>"><?php echo distT('sign_out');?></a>
		<?php }?>
	</div>
</div>
<?php if (!empty($info['mem_id'])){?>
<div id="info-edit-modal" class="modal hidden">
	<div class="mask"></div>
	<div class="dialog">
		<button class="btn24 btn-black top-close-btn">Close</button>
		<div class="layer">
			<div class="list-title flex mt20">
				<div class="tcell">
					<p class="line"></p>
				</div>
				<p class="title">EDIT INFO</p>
				<div class="tcell">
					<p class="line"></p>
				</div>
			</div>
		</div>
		<form class="content layer">
			<div class="item">
				<p class="name">
					<span class="text">Email</span>
					<span class="cred">*</span>
				</p>
				<div class="input-group flex">
					<input type="text" class="input" readonly="readonly" value="<?php echo $info['email'];?>" placeholder="Your Email">
					<button type="button" class="btn btn-white c6">Not editable</button>
				</div>
			</div>
			<div class="item">
				<p class="name">
					<span class="text">First Name</span>
					<span class="cred">*</span>
				</p>
				<input type="text" class="input" name="first_name" value="<?php echo $info['first_name'];?>" placeholder="First Name" maxlength="32" required="required">
			</div>
			<div class="item">
				<p class="name">
					<span class="text">Last Name</span>
					<span class="cred">*</span>
				</p>
				<input type="text" class="input" name="last_name" value="<?php echo $info['last_name'];?>" placeholder="Last Name" maxlength="32" required="required">
			</div>
			<div class="item">
				<p class="name">
					<span class="text">Phone Number</span>
					<span class="cred">*</span>
				</p>
				<div class="input-group flex">
					<div class="dialing-list">
						<input type="text" class="input" placeholder="Search">
						<ul></ul>
						<p class="empty-result hide f12 c6 tc">Empty</p>
					</div>
					<button type="button" class="btn btn-white dialing-code-btn">
						<span class="text"><?php echo $info['dialing_code'] ? $info['dialing_code'] : '+1';?></span>
						<span class="iconfont icon-xiangxia2"></span>
					</button>
					<input type="text" class="input" name="phone" placeholder="Your phone" maxlength="16" value="<?php echo $info['phone'];?>" required="required">
					<input type="hidden" name="dialing_code" value="<?php echo $info['dialing_code'] ? $info['dialing_code'] : '+1';?>">
				</div>
			</div>
		</form>
		<div class="footer">
			<button class="btn cancel-btn">Cancel</button>
			<button class="btn btn-black right save-btn">Save</button>
		</div>
	</div>
</div>
<?php }?>
<?php $this->load('common/txt_footer');?>