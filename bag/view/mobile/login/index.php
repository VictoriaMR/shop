<div id="login-page">
	<div class="login">
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
						<input type="text" class="input" name="verify_code" maxlength="1">
						<input type="text" class="input" name="verify_code" maxlength="1">
						<input type="text" class="input" name="verify_code" maxlength="1">
						<input type="text" class="input" name="verify_code" maxlength="1">
						<input type="text" class="input" name="verify_code" maxlength="1">
						<input type="text" class="input" name="verify_code" maxlength="1">
					</div>
				</div>
				<div class="password hide">
					<p class="name">Password</p>
					<div class="group">
						<input type="password" name="password" class="input"/>
					</div>
				</div>
				<div class="mt18">
					<span class="change-verify-btn">Sign in with Password</span>
				</div>
				<div class="mt32">
					<div class="flex">
						<button class="btn btn-black login-btn">LOG IN</button>
						<div class="relative verify-code">
							<p class="help-btn">Need Help?</p>
							<div class="help-tips">
								<div class="border-up-empty">
									<span></span>
								</div>
								<span>When you click 'Send', we will send you a 6 digit code to your email. You can use this code to quick login our website and check your order status. If you don't receive verification code, please check spam box.</span>
							</div>
						</div>
						<a class="password hide forget-btn" href="<?php echo url('login/fotget');?>">Forgot your password?</a>
					</div>
				</div>
				<div class="mt18">
					<span class="change-login-sign">Not have an Account?</span>
				</div>
			</div>
		</div>
	</div>
	<div class="register hide">
		<div class="title">
			<p>CREATE AN ACCOUNT</p>
		</div>
		<div class="content">
			<div class="email">
				<p class="name">Email</p>
				<div class="group">
					<input type="text" name="email" class="input" placeholder="name@example.com" />
				</div>
			</div>
			<div class="password mt18">
				<p class="name">Password</p>
				<div class="group">
					<input type="password" name="password" class="input"/>
				</div>
			</div>
			<div class="repassword mt18">
				<p class="name">Confirm Password</p>
				<div class="group">
					<input type="password" name="repassword" class="input"/>
				</div>
			</div>
			<div class="mt18 agreement">
				<span class="iconfont icon-squarecheck f16"></span>
				<span class="c6">Sign up for our email list</span>
				<input type="hidden" name="agreement" value="0">
			</div>
			<div class="mt18">
				<span class="change-login-sign">Had an Account?</pspan>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	LOGIN.init();
});
</script>