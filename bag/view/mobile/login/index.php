<div id="login-page">
	<a href="javascript:;" onclick="window.history.back(-1);" class="iconfont icon-guanbi1"></a>
	<div class="login mt32">
		<div class="title">
			<p>LOG IN</p>
		</div>
		<div class="content">
			<div class="email">
				<p class="name">Email</p>
				<div class="group flex">
					<input type="text" name="email" class="input" placeholder="name@example.com" />
					<button class="btn btn-black send-email">Send</button>
				</div>
			</div>
			<div class="verify mt18">
				<div class="verify-code">
					<p class="name">Verification code</p>
					<div class="input-group f0 flex">
						<input type="text" class="input" name="verify_code" maxlength="1" autocomplete="off">
						<input type="text" class="input" name="verify_code" maxlength="1" autocomplete="off">
						<input type="text" class="input" name="verify_code" maxlength="1" autocomplete="off">
						<input type="text" class="input" name="verify_code" maxlength="1" autocomplete="off">
						<input type="text" class="input" name="verify_code" maxlength="1" autocomplete="off">
						<input type="text" class="input" name="verify_code" maxlength="1" autocomplete="off">
					</div>
				</div>
				<div class="password hide">
					<p class="name">Password</p>
					<div class="group">
						<input type="password" name="password" class="input" autocomplete="off" />
					</div>
				</div>
				<div class="mt18">
					<span class="change-verify-btn">Sign in with Password</span>
				</div>
				<div class="mt32">
					<button class="btn btn-black login-btn w100">LOG IN</button>
					<div class="flex">
						<div class="relative verify-code">
							<p class="help-btn">Need Help?</p>
							<div class="help-tips">
								<div class="border-up-empty">
									<span></span>
								</div>
								<span>When you click 'Send', we will send you a 6 digit code to your email. You can use this code to quick login our website and check your order status. If you don't receive verification code, please check spam box.</span>
							</div>
						</div>
						<a class="password hide forget-btn" href="<?php echo url('login/forget');?>">Forgot your password?</a>
					</div>
				</div>
				<div class="mt18">
					<span class="change-login-sign">Not have an Account?</span>
				</div>
			</div>
		</div>
	</div>
	<div class="register mt32 hide">
		<div class="title">
			<p>CREATE AN ACCOUNT</p>
		</div>
		<div class="content">
			<div class="email">
				<p class="name">Email</p>
				<div class="group">
					<input type="text" name="email" class="input" placeholder="name@example.com" autocomplete="off" />
				</div>
			</div>
			<div class="password mt18">
				<p class="name">Password</p>
				<div class="group">
					<input type="password" name="password" class="input" autocomplete="off" />
				</div>
			</div>
			<div class="repassword mt18">
				<p class="name">Confirm Password</p>
				<div class="group">
					<input type="password" name="repassword" class="input" autocomplete="off" />
				</div>
			</div>
			<div class="mt18 agreement">
				<span class="iconfont icon-fangxingweixuanzhong f16"></span>
				<span class="c6">Sign up for our email list</span>
				<input type="hidden" name="agreement" value="0">
			</div>
			<button class="btn btn-black mt32 w100 register-btn">CREATE AN ACCOUNT</button>
			<div class="mt18">
				<span class="change-login-sign">Had an Account?</pspan>
			</div>
		</div>
	</div>
</div>
<?php if (!empty($error)) {?>
<div id="message-tips" class="error top">
	<div class="content">
		<div class="icon-content">
			<span class="iconfont icon-warn"></span>
		</div>
		<div class="text-content">
			<span><?php echo $error;?></span>
		</div>
	</div>
	<span class="iconfont icon-close"></span>
</div>
<?php } ?>
<script type="text/javascript">
$(function(){
	LOGIN.init();
});
</script>