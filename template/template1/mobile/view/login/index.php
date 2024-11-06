<?php $this->load('common/header_all');?>
<div class="con12" id="page-content">
	<form id="login-page">
		<div class="title">SIGN IN</div>
		<div class="input-group">
			<div class="input-title">
				<span class="c5">Email</span>
			</div>
			<div class="input-content">
				<input type="text" name="email" autocomplete="off" placeholder="Your email address" maxlength="64" required="required"><i class="remove"></i>
			</div>
		</div>
		<div class="input-group verify-code-group verification">
			<div class="input-title relative">
				<span class="c5">Verification Code</span>
				<i class="icon12 icon-notice"></i>
			</div>
			<div class="input-content">
				<input type="text" name="verify_code" autocomplete="off" placeholder="Code" maxlength="64" required="required"><i class="remove"></i>
				<button class="btn send-code-btn" type="button">Send Code</button>
			</div>
		</div>
		<div class="input-group password">
			<div class="input-title">
				<span class="c5">Password</span>
			</div>
			<div class="input-content">
				<input type="password" name="password" autocomplete="off" placeholder="Your password" maxlength="64" required="required"><i class="remove"></i>
			</div>
		</div>
		<div class="button-content">
			<button class="btn btn-black">Sign In</button>
			<button class="btn btn-white verification" type="button">Log in with password</button>
			<button class="btn btn-white password" type="button">Log in with Verification Code</button>
		</div>
		<div class="line-content" style="margin-top: 0.4rem;">
			<div class="line-content">
				<div class="line-tcell">
					<div class="line"></div>
				</div>
				<div class="line-title">Don't have an account yet?</div>
				<div class="line-tcell">
					<div class="line"></div>
				</div>
			</div>
		</div>
		<button class="btn btn-white no-border new-account-btn" type="button">Create a New Account</button>
	</form>
	<form id="register-page" method="post" action="" onSubmit="return false;" style="display: none;">
		<div class="title">SIGN UP</div>
		<div class="input-group">
			<div class="input-title">
				<span class="c5">Email</span>
			</div>
			<div class="input-content">
				<input type="text" name="email" autocomplete="off" placeholder="Your email address" maxlength="64" required="required"><i class="remove"></i>
			</div>
		</div>
		<div class="input-group password-group">
			<div class="input-title">
				<span class="c5">Password</span>
			</div>
			<div class="input-content">
				<input type="password" name="password" autocomplete="off" placeholder="Your password" maxlength="64" required="required"><i class="remove"></i><i class="icon24 icon-eye-close"></i>
			</div>
		</div>
		<div class="input-group password-group" style="margin-bottom: 0.16rem;">
			<div class="input-title">
				<span class="c5">Confirm password</span>
			</div>
			<div class="input-content">
				<input type="password" name="confirm_password" autocomplete="off" placeholder="Your password" maxlength="64" required="required"><i class="remove"></i><i class="icon24 icon-eye-close"></i>
			</div>
		</div>
		<div class="sign-up-agree">
            <p class="join_tips">By joining, you agree to our <a href="<?php echo url('faq/terms-and-conditions');?>">Terms & Conditions.</a></p>
        </div>
		<div class="button-content">
			<button class="btn btn-black btn-save">Create a New Account</button>
			<button class="btn btn-white no-border back-btn" type="button">Back to Sign in</button>
		</div>
	</form>
</div>
<div class="modal verify-code-modal" data-type="white">
	<div class="content">
		<span>When you click 'Send Code', we will send you a 6 digit code to your email. You can use this code to login our website and buy quickly. If you don't receive verification code, please check the spam box.</span>
		<i class="icon16 icon-close close-btn"></i>
	</div>
</div>
<?php $this->load('common/footer_all');?>