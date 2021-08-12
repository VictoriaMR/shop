<div id="userinfo-page">
	<div class="top layer16 mt22">
		<div class="tcell image-avatar f0">
			<img data-src="<?php echo $info['avatar'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
		</div>
		<div class="tcell name-content">
			<?php if (empty($info['name'])){?>
			<p class="e1 name f16 f600"><?php echo $info['email'];?></p>
			<?php } else { ?>
			<p class="e1 name f16 f600"><?php echo $info['name'];?></p>
			<p class="e1 mt4"><?php echo $info['email'];?></p>
			<?php } ?>
			<span class="iconfont icon-fankui"></span>
		</div>
	</div>
	<div class="nav-list mt22 f0">
		<ul>
			<li>
				<a href="<?php echo url('userInfo/wishList');?>" class="block">
					<span class="iconfont icon-xihuan"></span>
					<p>Wish</p>
				</a>
			</li>
			<li>
				<a href="<?php echo url('userInfo/history');?>" class="block">
					<span class="iconfont icon-zuji"></span>
					<p>History</p>
				</a>
			</li>
			<li>
				<a href="<?php echo url('userInfo/address');?>" class="block">
					<span class="iconfont icon-zuobiao"></span>
					<p>Address</p>
				</a>
			</li>
			<li>
				<a href="<?php echo url('userInfo/coupon');?>" class="block">
					<span class="iconfont icon-youhuiquan"></span>
					<p>Coupon</p>
				</a>
			</li>
		</ul>
	</div>
</div>
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
<script type="text/javascript">
$(function(){
	USERINFO.init();
});
</script>