<div class="cover">
	<div class="layer f16 f600">
		<div class="left">
			<div class="desc-title">
				<span>Discover endless dressing styles just right for you</span>
			</div>
		</div>
		<div class="right">
			<div class="desc-title">
				<?php if (userId()){?>
				<a class="userinfo" href="<?php echo url('userInfo');?>">Hello, <?php echo session()->get('home_info', 'name');?></a>
				<?php } else {?>
				<a class="userinfo" href="<?php echo url('login');?>">Registry</a>
				<?php }?>
				<a class="userinfo" href="javascript">English</a>
				<a class="userinfo" href="javascript">USD</a>
			</div>
		</div>
	</div>
</div>