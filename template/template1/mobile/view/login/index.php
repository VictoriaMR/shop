<?php $this->load('common/header_all');?>
<div class="con12">
	<div id="login-page">
		<div class="title">SIGN IN</div>
		<div class="input-group">
			<div class="input-title">
				<span class="c5">Email</span>
			</div>
			<div class="input-content">
				<input type="text" name="company_name" autocomplete="off" placeholder="Your email address" maxlength="64" required="required"><i class="remove"></i>
			</div>
		</div>
		<div class="input-group">
			<div class="input-title relative">
				<span class="c5">Verification Code</span>
				<i class="icon12 icon-notice"></i>
			</div>
			<div class="input-content">
				<input type="text" name="company_name" autocomplete="off" placeholder="Your email address" maxlength="64" required="required"><i class="remove"></i>
			</div>
		</div>
	</div>
</div>
<div class="modal verify-code-modal">
	<div class="content">
		<span>When you click 'Send Code', we will send you a 6 digit code to your email. You can use this code to quick login our website and check your order status. If you don't receive verification code, please check spam box.</span>
		<i class="icon-16 icon-close"></i>
	</div>
</div>
<div class="mask"></div>
<?php $this->load('common/footer_all');?>