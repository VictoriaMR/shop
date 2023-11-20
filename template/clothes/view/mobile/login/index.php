<?php $this->load('common/header_logo');?>
<div id="login-page">
	<div class="nav table">
		<div class="nav-item active" data-type="login">
			<span><?php echo distT('log_in');?></span>
		</div>
		<div class="nav-item" data-type="sign">
			<span><?php echo distT('sign_up');?></span>
		</div>
	</div>
	<div class="content">
		<div class="input-group email-item">
			<p class="title"><?php echo distT('email');?>:</p>
			<div class="input-content">
				<input type="text" name="email" class="input" placeholder="<?php echo distT('email_placeholder');?>" value="<?php echo $login_email;?>" maxlength="64">
				<span class="icon-box remove"><i class="icon icon18 icon-remove"></i></span>
			</div>
		</div>
		<div class="password-box">
			<div class="input-group">
				<p class="title"><?php echo distT('password');?>:</p>
				<div class="input-content password">
					<input type="password" name="password" class="input" placeholder="<?php echo distT('password_placeholder');?>" autocompleted="off" maxlength="32">
					<span class="icon-box remove"><i class="icon icon18 icon-remove"></i></span>
					<span class="icon-box eye"><i class="icon icon20 icon-eye-close"></i></span>
				</div>
			</div>
		</div>
		<div class="verify-code-box">
			<div class="input-group relative">
				<p class="title"><?php echo distT('verify_code');?>:</p>
				<div class="input-content code">
					<input type="number" name="verify_code" class="input" placeholder="<?php echo distT('verify_code_placeholder');?>" autocompleted="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'').slice(0, 6)" onpaste="this.value=this.value.replace(/[^\d]/g,'').slice(0, 6)" maxlength="6">
					<span class="verify-code-btn"><?php echo distT('send_code');?></span>
					<span class="icon-box remove"><i class="icon icon18 icon-remove"></i></span>
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
		<div id="login-box">
			<div class="password-box item">
				<div class="forget-content">
					<a href="<?php echo url('login/forget');?>"><span class="tips-title"><?php echo distT('forgot_tips');?></span></a>
				</div>
				<button type="button" class="btn btn-black login-btn"><?php echo distT('log_in');?></button>
				<button type="button" class="btn change-btn" data-type="code"><?php echo distT('log_in_width_code');?></button>
			</div>
			<div class="verify-code-box item">
				<div class="forget-content">
					<a href="javascript:;" class="relative">
						<span class="tips-title"><?php echo distT('help');?></span>
						<div class="tips">
							<div class="mask"></div>
	                        <div class="border-up-empty"><span></span></div>
	                        <span><?php echo distT('help_tips');?></span>
	                    </div>
					</a>
				</div>
				<button type="button" class="btn btn-black login-btn"><?php echo distT('log_in');?></button>
				<button type="button" class="btn change-btn" data-type="password"><?php echo distT('log_in_width_password');?></button>
			</div>
		</div>
		<div id="sign-box">
			<div class="git-box">
				<i class="icon icon20 icon-gift"></i>
				<span><?php echo distT('sign_gift');?></span>
			</div>
			<button type="button" class="btn btn-black sign-btn"><?php echo distT('sign_in');?></button>
			<p class="join-tips">
				<a href="<?php echo url('faq', 'terms-conditions', false);?>"><?php echo distT('agree_tips', ['code'=>'Terms & Conditions']);?></span>
			</p>
		</div>
	</div>
</div>