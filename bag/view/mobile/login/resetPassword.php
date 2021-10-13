<div id="resetpassword-page" class="layer24">
	<div class="title">
		<p>RESET YOUR PASSWORD?</p>
	</div>
	<form class="content" method="get" action="<?php echo url('login/resetPassword');?>">
		<?php if (empty($error)) { ?>
		<div class="password">
			<p class="f14 f700 c40">Password</p>
			<div class="flex mt4">
				<input type="password" name="password" class="input" autocomplete="off" />
				<input type="hidden" name="token" value="<?php echo $token;?>">
				<button type="button" class="btn btn-black password-btn">Send</button>
			</div>
		</div>
		<?php } else { ?>
		<p class="tips">Enter your token, and we'll help you to reset your password.</p>
		<div class="token mt16">
			<p class="f14 f700 c40">Token</p>
			<div class="mt4">
				<input type="text" name="token" class="input" placeholder="token from email" maxlength="32">
			</div>
		</div>
		<button type="button" class="btn btn-black mt16 token-btn w100">Confirm</button>
		<?php }?>
	</form>
</div>
<script type="text/javascript">
$(function(){
	<?php if (!empty($error)){ ?>
	TIPS.error('<?php echo $error;?>');
	<?php } ?>
	RESETPASSWD.init();
});
</script>