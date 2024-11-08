<?php $this->load('common/header_all');?>
<div class="con12" id="page-content">
	<form id="forgot-page" method="post" action="" onSubmit="return false;">
		<div class="title">Forgot Password</div>
		<div class="input-group">
			<div class="input-title">
				<span class="c5">Enter your email address</span>
			</div>
			<div class="input-content">
				<input type="text" name="email" autocomplete="off" placeholder="Your email address" maxlength="64" required="required"><i class="remove"></i>
			</div>
		</div>
		<div class="button-content f0">
			<button class="btn btn-black btn-save">Send reset password email</button>
			<a class="btn btn-white no-border back-btn" href="<?php echo url('login', ['back'=>1]);?>">Back to Sign in</a>
		</div>
	</form>
</div>
<?php $this->load('common/footer_all');?>