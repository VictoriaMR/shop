<div id="login-page">
	<div class="login-bg">
		<img height="100%" width="100%" src="<?php echo siteUrl('image/login_bg.jpg');?>">
	</div>
	<div class="login-box">
		<div class="title">
			<h2 class="f16 select" href="javascript:void(0);">密码登录</h2>
		</div>
		<form class="relative" action="" method="post" id="login-form">
			<div class="mt20">
				<input type="text" class="input" name="mobile" id="mobile" placeholder="请输入手机号码" autocomplete="off" required="required">
			</div>
			<div class="mt20">
				<input type="password" class="input" name="password" id="password" placeholder="请输入密码" autocomplete="off" required="required">
			</div>
			<div class="mt20">
				<input type="text" class="input left" name="code" id="code" placeholder="请输入验证码" autocomplete="off" required="required">
				<img id="refresh" class="left pointer ml12" height="40" width="80" src="<?php echo adminUrl('login/loginCode');?>" onclick="document.getElementById('refresh').src='<?php echo adminUrl('login/loginCode', ['is_ajax'=>1]);?>'" title="看不清？换一张">
				<div class="clear"></div>
			</div>
			<button type="button" id="login-btn" class="btn btn-primary w100 mt20">登录</button>
		</form>
	</div>
</div>