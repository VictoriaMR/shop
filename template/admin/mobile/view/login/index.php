<div id="login-page">
	<div class="login-bg">
		<i class="fa fa-shield-halved login-bg-icon" aria-hidden="true"></i>
		<div class="login-bg-mask"></div>
	</div>

	<div class="login-box">
		<div class="login-header">
			<div class="brand-icon">
				<i class="fa fa-shield"></i>
			</div>
			<h1 class="brand-title"><?php echo htmlspecialchars(\App::get('domain', 'name', '管理后台'), ENT_QUOTES, 'UTF-8');?></h1>
			<p class="brand-subtitle">欢迎登录系统后台管理中心</p>
		</div>

		<form class="login-form" action="" method="post" id="login-form" novalidate>
			<div class="form-group">
				<label for="mobile" class="sr-only">账号</label>
				<div class="input-wrap">
					<i class="fa fa-user input-icon" aria-hidden="true"></i>
					<input type="text" class="input" name="mobile" id="mobile" placeholder="请输入手机号码或邮箱" autocomplete="username" required>
				</div>
			</div>

			<div class="form-group">
				<label for="password" class="sr-only">密码</label>
				<div class="input-wrap">
					<i class="fa fa-lock input-icon" aria-hidden="true"></i>
					<input type="password" class="input" name="password" id="password" placeholder="请输入登录密码" autocomplete="current-password" required>
					<button type="button" class="btn-toggle-pwd pointer" id="toggle-pwd" title="显示/隐藏密码" tabindex="-1">
						<i class="fa fa-eye-slash" aria-hidden="true"></i>
					</button>
				</div>
			</div>

			<div class="form-group">
				<label for="code" class="sr-only">验证码</label>
				<div class="code-wrap">
					<div class="input-wrap code-input-wrap">
						<i class="fa fa-shield input-icon" aria-hidden="true"></i>
						<input type="text" class="input" name="code" id="code" placeholder="请输入验证码" maxlength="4" autocomplete="off" required>
					</div>
					<div class="captcha-img-box pointer" id="captcha-box" title="点击更换验证码">
						<img id="refresh" src="<?php echo adminUrl('login/loginCode');?>" alt="验证码" data-src="<?php echo adminUrl('login/loginCode', ['is_ajax' => 1]);?>">
					</div>
				</div>
			</div>

			<div class="form-group mt24">
				<button type="submit" id="login-btn" class="btn btn-primary btn-submit w100" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 正在登录...">
					<span>登 录</span>
					<i class="fa fa-arrow-right ml12"></i>
				</button>
			</div>
		</form>

		<div class="login-footer">
			<span>安全防护中 · 请妥善保管您的管理权限</span>
		</div>
	</div>
</div>