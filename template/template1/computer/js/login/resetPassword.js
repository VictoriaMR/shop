document.addEventListener('DOMContentLoaded', function () {
	var form = document.getElementById('js-reset-password-form');
	if (!form) return;

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var pwdEl = document.getElementById('rp-password');
		var cpwdEl = document.getElementById('rp-confirm');
		var tokenEl = form.querySelector('[name="token"]');
		var btn = document.getElementById('js-reset-submit');

		var pwd = pwdEl ? pwdEl.value : '';
		var cpwd = cpwdEl ? cpwdEl.value : '';
		var token = tokenEl ? tokenEl.value : '';

		if (!pwd || pwd.length < 6) {
			Tool.tips('Password must be at least 6 characters.');
			if (pwdEl) pwdEl.focus();
			return false;
		}
		if (pwd !== cpwd) {
			Tool.tips('Passwords do not match.');
			if (cpwdEl) cpwdEl.focus();
			return false;
		}

		if (btn) {
			btn.disabled = true;
			btn.textContent = 'Updating...';
		}

		post(window.location.href, {
			token: token,
			password: pwd,
			confirm_password: cpwd
		}, function (res) {
			if (btn) {
				btn.disabled = false;
				btn.textContent = 'Update Password';
			}
			if (res && (res.code === 1 || res.code === 200)) {
				Tool.tips(res.msg || 'Password updated successfully.');
				setTimeout(function () {
					window.location.href = (res.data && res.data.url) ? res.data.url : '/login.html';
				}, 600);
			} else {
				Tool.tips((res && res.msg) ? res.msg : 'Failed to reset password.');
			}
		});
		return false;
	});
});
