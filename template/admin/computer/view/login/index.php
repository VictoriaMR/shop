<div id="login-page">
	<div class="login-bg">
		<img src="<?php echo siteUrl('image/login_bg.jpg');?>">
	</div>
	<div class="login-box">
		<div class="title">
			<h2 class="f16 select" href="javascript:void(0);">密码登录</h2>
		</div>
		<form class="relative" action="" method="post" id="form">
			<div class="mt20">
				<input type="text" class="input" name="mobile" id="mobile" placeholder="请输入手机号码" autocomplete="off">
			</div>
			<div class="mt20">
				<input type="password" class="input" name="password" id="password" placeholder="请输入密码" autocomplete="off">
			</div>
			<div class="mt20">
				<input type="text" class="pl12 input left" name="code" id="code" placeholder="请输入验证码" autocomplete="off">
				<img id="refresh" class="left pointer ml10" height="40" width="90" src="<?php echo adminUrl('login/loginCode');?>" onclick="document.getElementById('refresh').src='<?php echo adminUrl('login/loginCode');?>'" title="看不清？换一张">
				<div class="clear"></div>
			</div>
			<button id="login-btn" class="btn w100 mt20" type="submit">登录</button>
		</form>
	</div>
</div>