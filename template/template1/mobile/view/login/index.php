<?php $this->load('common/header_all');?>
<div class="con12">
	<div id="login-page">
		<div class="title">SIGN IN</div>
		<div class="input-group">
			<div class="input-title">
				<span class="c5">Email</span>
			</div>
			<div class="input-content">
				<input type="text" name="email" autocomplete="off" placeholder="Your email address" maxlength="64" required="required"><i class="remove"></i>
			</div>
		</div>
		<div class="input-group verify-code-group">
			<div class="input-title relative">
				<span class="c5">Verification Code</span>
				<i class="icon12 icon-notice"></i>
			</div>
			<div class="input-content">
				<input type="text" name="verify_code" autocomplete="off" placeholder="Your email address" maxlength="64" required="required"><i class="remove"></i>
				<button class="btn send-code-btn" type="button">Send Code</button>
			</div>
		</div>
		<div class="button-content">
			<button class="btn btn-black">Sign In</button>
			<button class="btn btn-white">Log in with password</button>
		</div>
	</div>
</div>
<div class="modal verify-code-modal" data-type="white">
	<div class="content">
		<span>When you click 'Send Code', we will send you a 6 digit code to your email. You can use this code to login our website and buy quickly. If you don't receive verification code, please check the spam box.</span>
		<i class="icon16 icon-close close-btn"></i>
	</div>
</div>
<?php $this->load('common/footer_all');?>