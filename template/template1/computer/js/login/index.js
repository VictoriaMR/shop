document.addEventListener('DOMContentLoaded', function () {
	var authTabsWrap = document.getElementById('auth-tabs');
	var tabBtns = document.querySelectorAll('[data-auth-tab]');
	var panelSignin = document.getElementById('panel-signin');
	var panelRegister = document.getElementById('panel-register');
	var panelForgot = document.getElementById('panel-forgot');

	// 1. 切换登录 / 注册 / 找回密码面板
	function switchAuthView(viewName) {
		var isSignin = viewName === 'signin';
		var isRegister = viewName === 'register';
		var isForgot = viewName === 'forgot';

		if (authTabsWrap) {
			authTabsWrap.classList.toggle('hidden', isForgot);
		}
		for (var i = 0; i < tabBtns.length; i++) {
			var t = tabBtns[i].getAttribute('data-auth-tab');
			var active = t === viewName;
			tabBtns[i].classList.toggle('active', active);
			tabBtns[i].setAttribute('aria-selected', active ? 'true' : 'false');
		}
		if (panelSignin) panelSignin.classList.toggle('hidden', !isSignin);
		if (panelRegister) panelRegister.classList.toggle('hidden', !isRegister);
		if (panelForgot) panelForgot.classList.toggle('hidden', !isForgot);
	}

	document.addEventListener('click', function (e) {
		var trigger = e.target.closest ? e.target.closest('[data-switch-view]') : null;
		if (trigger) {
			e.preventDefault();
			var targetView = trigger.getAttribute('data-switch-view');
			if (targetView) {
				switchAuthView(targetView);
			}
			return;
		}

		// 2. 密码明文/密文切换
		var pwdToggle = e.target.closest ? e.target.closest('.pwd-toggle-btn') : null;
		if (pwdToggle) {
			var wrap = pwdToggle.closest('.input-wrap');
			var input = wrap ? wrap.querySelector('input') : null;
			if (!input) return;
			var isPwd = input.getAttribute('type') === 'password';
			input.setAttribute('type', isPwd ? 'text' : 'password');
			pwdToggle.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
			return;
		}
	});

	for (var b = 0; b < tabBtns.length; b++) {
		tabBtns[b].addEventListener('click', function () {
			switchAuthView(this.getAttribute('data-auth-tab'));
		});
	}

	// 3. 登录方式切换（密码登录 <-> 邮箱验证码登录）
	var loginModeInput = document.getElementById('signin-login-mode');
	var pwdGroup = document.getElementById('signin-password-group');
	var codeGroup = document.getElementById('signin-code-group');
	var modeToggleBtn = document.getElementById('signin-mode-toggle');
	var modeToggleText = document.getElementById('signin-mode-toggle-text');

	if (modeToggleBtn && loginModeInput) {
		modeToggleBtn.addEventListener('click', function () {
			var curMode = loginModeInput.value;
			var nextMode = curMode === 'password' ? 'code' : 'password';
			loginModeInput.value = nextMode;
			if (pwdGroup) pwdGroup.classList.toggle('hidden', nextMode !== 'password');
			if (codeGroup) codeGroup.classList.toggle('hidden', nextMode !== 'code');
			if (modeToggleText) {
				modeToggleText.textContent = nextMode === 'password'
					? 'Sign in with Email Verification Code instead'
					: 'Sign in with Password instead';
			}
		});
	}

	// 4. 表单字段错误提示辅助函数
	function setFieldError(formEl, fieldName, message) {
		var input = formEl.querySelector('[name="' + fieldName + '"]');
		if (!input) return;
		var group = input.closest('.form-group');
		if (!group) return;
		var errEl = group.querySelector('.field-error');
		group.classList.add('has-error');
		if (errEl) {
			errEl.textContent = message;
		}
	}

	function clearFormErrors(formEl) {
		var groups = formEl.querySelectorAll('.form-group.has-error');
		for (var i = 0; i < groups.length; i++) {
			groups[i].classList.remove('has-error');
		}
	}

	var allInputs = document.querySelectorAll('.auth-form-panel .form-input');
	for (var k = 0; k < allInputs.length; k++) {
		allInputs[k].addEventListener('input', function () {
			var grp = this.closest('.form-group');
			if (grp) grp.classList.remove('has-error');
		});
	}

	function handleResponseErrors(formEl, res) {
		if (!res) return;
		if (res.msg && typeof res.msg === 'object') {
			for (var key in res.msg) {
				if (Object.prototype.hasOwnProperty.call(res.msg, key)) {
					setFieldError(formEl, key, res.msg[key]);
				}
			}
		} else if (typeof res.msg === 'string' && res.msg) {
			Tool.tips(res.msg);
		} else {
			Tool.tips('Request failed. Please check your input and try again.');
		}
	}

	// 5. 发送邮箱验证码交互
	var sendCodeBtn = document.getElementById('btn-send-code');
	var signinForm = document.getElementById('form-signin');
	var countdownTimer = null;

	if (sendCodeBtn && signinForm) {
		sendCodeBtn.addEventListener('click', function () {
			clearFormErrors(signinForm);
			var emailInput = signinForm.querySelector('[name="email"]');
			var emailVal = emailInput ? emailInput.value.trim() : '';
			if (!VERIFY.email(emailVal)) {
				setFieldError(signinForm, 'email', distT('email_invalid'));
				if (emailInput) emailInput.focus();
				return;
			}

			sendCodeBtn.disabled = true;
			var origText = sendCodeBtn.textContent;
			sendCodeBtn.textContent = 'Sending...';

			post('/login/sendCode', { email: emailVal }, function (res) {
				if (res && res.code === 1) {
					Tool.tips(res.msg || 'Verification code sent!');
					var seconds = parseInt(res.data, 10) || 120;
					clearInterval(countdownTimer);
					countdownTimer = setInterval(function () {
						if (seconds <= 0) {
							clearInterval(countdownTimer);
							sendCodeBtn.disabled = false;
							sendCodeBtn.textContent = origText;
						} else {
							sendCodeBtn.textContent = seconds + 's';
							seconds--;
						}
					}, 1000);
				} else {
					sendCodeBtn.disabled = false;
					sendCodeBtn.textContent = origText;
					handleResponseErrors(signinForm, res);
				}
			});
		});
	}

	// 6. 登录表单提交
	if (signinForm) {
		signinForm.addEventListener('submit', function (e) {
			e.preventDefault();
			clearFormErrors(signinForm);

			var emailInput = signinForm.querySelector('[name="email"]');
			var pwdInput = signinForm.querySelector('[name="password"]');
			var codeInput = signinForm.querySelector('[name="verify_code"]');
			var mode = loginModeInput ? loginModeInput.value : 'password';

			var emailVal = emailInput ? emailInput.value.trim() : '';
			var pwdVal = pwdInput ? pwdInput.value : '';
			var codeVal = codeInput ? codeInput.value.trim() : '';
			var valid = true;

			if (!VERIFY.email(emailVal)) {
				setFieldError(signinForm, 'email', distT('email_invalid'));
				valid = false;
			}
			if (mode === 'code') {
				if (!/^[0-9]{6}$/.test(codeVal)) {
					setFieldError(signinForm, 'verify_code', distT('code_invalid'));
					valid = false;
				}
			} else {
				if (!VERIFY.password(pwdVal)) {
					setFieldError(signinForm, 'password', distT('password_invalid'));
					valid = false;
				}
			}
			if (!valid) return false;

			var submitBtn = document.getElementById('btn-signin-submit');
			var origLabel = submitBtn ? submitBtn.textContent : 'Sign In';
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Signing in...';
			}

			post('/login/login', {
				email: emailVal,
				password: mode === 'password' ? pwdVal : '',
				verify_code: mode === 'code' ? codeVal : '',
				login_mode: mode
			}, function (res) {
				if (res && res.code === 1) {
					Tool.tips(res.msg || 'Signed in successfully!');
					setTimeout(function () {
						window.location.href = (res.data && res.data.url) ? res.data.url : '/';
					}, 600);
				} else {
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.textContent = origLabel;
					}
					handleResponseErrors(signinForm, res);
				}
			});
			return false;
		});
	}

	// 7. 注册表单提交
	var registerForm = document.getElementById('form-register');
	if (registerForm) {
		registerForm.addEventListener('submit', function (e) {
			e.preventDefault();
			clearFormErrors(registerForm);

			var emailInput = registerForm.querySelector('[name="email"]');
			var pwdInput = registerForm.querySelector('[name="password"]');
			var confirmInput = registerForm.querySelector('[name="confirm_password"]');

			var emailVal = emailInput ? emailInput.value.trim() : '';
			var pwdVal = pwdInput ? pwdInput.value : '';
			var confirmVal = confirmInput ? confirmInput.value : '';
			var valid = true;

			if (!VERIFY.email(emailVal)) {
				setFieldError(registerForm, 'email', distT('email_invalid'));
				valid = false;
			}
			if (!VERIFY.password(pwdVal)) {
				setFieldError(registerForm, 'password', distT('password_invalid'));
				valid = false;
			}
			if (!VERIFY.password(confirmVal)) {
				setFieldError(registerForm, 'confirm_password', distT('confirm_password_invalid'));
				valid = false;
			} else if (pwdVal !== confirmVal) {
				setFieldError(registerForm, 'confirm_password', distT('confirm_password_not_match'));
				valid = false;
			}
			if (!valid) return false;

			var submitBtn = document.getElementById('btn-register-submit');
			var origLabel = submitBtn ? submitBtn.textContent : 'Create My Account';
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Creating account...';
			}

			post('/login/register', {
				email: emailVal,
				password: pwdVal,
				confirm_password: confirmVal
			}, function (res) {
				if (res && res.code === 1) {
					Tool.tips(res.msg || 'Account created successfully!');
					setTimeout(function () {
						window.location.href = (res.data && res.data.url) ? res.data.url : '/';
					}, 700);
				} else {
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.textContent = origLabel;
					}
					handleResponseErrors(registerForm, res);
				}
			});
			return false;
		});
	}

	// 8. 找回密码表单提交
	var forgotForm = document.getElementById('form-forgot');
	if (forgotForm) {
		forgotForm.addEventListener('submit', function (e) {
			e.preventDefault();
			clearFormErrors(forgotForm);

			var emailInput = forgotForm.querySelector('[name="email"]');
			var emailVal = emailInput ? emailInput.value.trim() : '';
			if (!VERIFY.email(emailVal)) {
				setFieldError(forgotForm, 'email', distT('email_invalid'));
				return false;
			}

			var submitBtn = document.getElementById('btn-forgot-submit');
			var origLabel = submitBtn ? submitBtn.textContent : 'Send Reset Link';
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Sending...';
			}

			post('/login/sendForgotEmail', { email: emailVal }, function (res) {
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.textContent = origLabel;
				}
				if (res && res.code === 1) {
					Tool.tips(res.msg || 'Password reset email sent!');
				} else {
					handleResponseErrors(forgotForm, res);
				}
			});
			return false;
		});
	}
});
