<div class="login-content">
	<div class="title-content">
		<a href="<?php echo url('/');?>">Welcome to <?php echo \App::get('base_info', 'name');?></a>
	</div>
	<table width="100%" border="0">
		<tr>
			<td width="50%">
				<div class="signin-content">
					<p class="title f26 f600">LOG IN</p>
					<form>
						<div class="form-item">
							<div class="f16 f600 input-tips">Email</div>
							<input class="input" type="text" name="email" value="" placeholder="Email Address">
						</div>
						<div class="form-item">
							<div class="f16 f600 input-tips">Password</div>
							<input class="input" type="text" name="password" value="" placeholder="Your Password">
						</div>
						<button type="button" class="btn btn-black bottom-btn">LOG IN</button>
						<div class="help relative">
							<span class="pointer">Help?</span>
							<div class="help-tips">
								<div class="border-up-empty">
									<span></span>
								</div>
								<span>When you click 'Send', we will send you a 6 digit code to your email. You can use this code to quick login our website and check your order status. If you don't receive verification code, please check spam box.</span>
							</div>
						</div>
					</form>
				</div>
			</td>
			<td width="50%">
				<div class="register-content">
					<p class="title f26 f600">REGISTER</p>
					<form>
						<div class="form-item">
							<div class="f16 f600 input-tips">Email</div>
							<input class="input" type="text" name="email" value="" placeholder="Email Address">
						</div>
						<div class="form-item">
							<div class="f16 f600 input-tips">Password</div>
							<input class="input" type="text" name="password" value="" placeholder="Your Password">
						</div>
						<div class="form-item">
							<div class="f16 f600 input-tips">Confirm Password</div>
							<input class="input" type="text" name="re_password" value="" placeholder="Your Confirm Password">
						</div>
						<button type="button" class="btn btn-black bottom-btn">REGISTER</button>
						<p>Your privacy is very important to us, we'll keep your details safe and secure.For more information,<br/>read our privacy policy.</p>
					</form>
				</div>
			</td>
		</tr>
	</table>
</div>