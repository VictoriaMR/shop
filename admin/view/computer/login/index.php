<div id="login-bg">
	<img src="<?php echo siteUrl('image/computer/login_bg.jpg');?>">
</div>
<div class="login-box">
	<div class="poptip-content hidden">扫码登录更安全</div>
	<div class="title">
		<a class="f16 f800 select" href="javascript:void(0);">密码登录</a>
		<a class="f16 f800 hidden" href="javascript:void(0);">短信登录</a>
	</div>
	<div class="clear"></div>
	<form class="relative" style="padding-top: 50px;">
		<input type="hidden" name="is_ajax" value="1">
		<div id="login-error" class="hidden">
			<span class="glyphicon glyphicon-warning-sign left orange"></span>
			<div id="login-error-msg" class="left ml4">请输入帐户名</div>
		</div>
		<div class="">
			<input type="text" class="form-control w100" name="phone" placeholder="请输入手机号码" autocomplete="off">
		</div>
		<div class="mt20 ">
			<input type="password" class="form-control w100" name="password" placeholder="请输入密码" autocomplete="off">
		</div>
		<div class="mt20">
			<input type="text" class="pl12 form-control w50 left" name="code" placeholder="验证码" autocomplete="off">
			<img id="refresh" class="left pointer ml10" height="34" width="80" src="<?php echo url('login/loginCode', ['is_ajax'=>1]);?>" onclick="document.getElementById('refresh').src='<?php echo url('login/loginCode', ['is_ajax'=>1]);?>'" title="看不清？换一张">
			<div class="clear"></div>
		</div>
		<button id="login-btn" type="button" class="btn btn-primary btn-lg w100 mt20">登录</button>
	</form>
</div>