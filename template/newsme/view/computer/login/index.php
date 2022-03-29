<?php $this->load('common/simple_header');?>
<div class="login-content">
	<div class="title-content">
		<a href="<?php echo url();?>">Welcome to <?php echo \App::get('base_info', 'name');?></a>
	</div>
	<table width="100%" border="0">
		<tr>
			<td width="50%">
				<div class="signin-content">
					<p class="title f26 f600">LOG IN</p>
					<form>
						<div class="password-content hide">
							<div class="form-item mb20">
								<div class="f16 f600 input-tips">Email</div>
								<input class="input" type="text" name="email" value="" placeholder="Email Address">
							</div>
							<div class="form-item mb8">
								<div class="f16 f600 input-tips">Password</div>
								<input class="input" type="text" name="password" value="" placeholder="Your Password">
							</div>
							<p class="change-login-type">Sign in with Verification Code</p>
						</div>
						<div class="verification-content">
							<div class="form-item mb20 verification">
								<div class="f16 f600 input-tips">Email</div>
								<div class="relative">
									<input class="input" type="text" name="email" value="" placeholder="Email Address">
									<button type="button" class="btn btn-black">Send</button>
								</div>
							</div>
							<div class="form-item mb8">
								<div class="f16 f600 input-tips">Verification Code</div>
								<input class="input" type="text" name="verify_code" value="" placeholder="Verification Code">
							</div>
							<p class="change-login-type">Sign in with Password</p>
						</div>
						<button type="button" class="btn btn-black bottom-btn">LOG IN</button>
						<div class="help relative">
							<span class="pointer left">Help?</span>
							<a href="<?php echo url('forgot');?>" class="right forgot-password">Forgot your password?</a>
							<div class="help-tips">
								<div class="border-up-empty">
									<span></span>
								</div>
								<span>When you click 'Send', we will send you a 6 digit code to your email. You can use this code to quick login our website and check your order status. If you don't receive verification code, please check spam box.</span>
							</div>
							<div class="clear"></div>
						</div>
					</form>
				</div>
			</td>
			<td width="50%">
				<div class="register-content">
					<p class="title f26 f600">REGISTER</p>
					<form>
						<div class="form-item mb20">
							<div class="f16 f600 input-tips">Email</div>
							<input class="input" type="text" name="email" value="" placeholder="Email Address">
						</div>
						<div class="form-item mb8">
							<div class="f16 f600 input-tips">Password</div>
							<input class="input" type="text" name="password" value="" placeholder="Your Password">
						</div>
						<div class="agreement mb20 tl">
							<span class="iconfont icon-fangxingweixuanzhong f16"></span>
							<span class="c6">Sign up for our email list</span>
							<input type="hidden" name="agreement" value="0">
						</div>
						<button type="button" class="btn btn-black bottom-btn">REGISTER</button>
						<p>Your privacy is very important to us, we'll keep your details safe and secure.For more information,<br/>read our privacy policy.</p>
					</form>
				</div>
			</td>
		</tr>
	</table>
</div>