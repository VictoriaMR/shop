<div id="login-page">
	<div class="login-bg">
		<img src="<?php echo siteUrl('image/computer/login_bg.jpg');?>">
	</div>
	<div class="login-box">
		<div class="title">
			<a class="f16 f800 select" href="javascript:void(0);">密码登录</a>
		</div>
		<div class="clear"></div>
		<form class="relative">
			<div class="mt20">
				<input type="text" class="input w100" name="mobile" placeholder="请输入手机号码" autocomplete="off">
			</div>
			<div class="mt20">
				<input type="password" class="input w100" name="password" placeholder="请输入密码" autocomplete="off">
			</div>
			<div class="mt20">
				<input type="text" class="pl12 input left" name="code" placeholder="请输入验证码" autocomplete="off">
				<img id="refresh" class="left pointer ml10" height="34" width="80" src="<?php echo adminUrl('login/loginCode');?>" onclick="document.getElementById('refresh').src='<?php echo adminUrl('login/loginCode', ['is_ajax'=>1]);?>'" title="看不清？换一张">
				<div class="clear"></div>
			</div>
			<button id="login-btn" class="btn w100 mt20">登录</button>
		</form>
	</div>
</div>