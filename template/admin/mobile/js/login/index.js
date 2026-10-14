$(function(){
	// 回填上次登录账号
	var savedNumber = localStorage.getItem('login_number');
	if (savedNumber) {
		$('#login-page #mobile').val(savedNumber);
	}

	// 验证码刷新处理（防缓存时间戳参数）
	function refreshCaptcha() {
		var imgObj = $('#login-page #refresh');
		var baseSrc = imgObj.data('src') || imgObj.attr('src');
		if (!baseSrc) return;
		baseSrc = baseSrc.split('&_=')[0].split('?_=')[0];
		var separator = baseSrc.indexOf('?') !== -1 ? '&' : '?';
		imgObj.attr('src', baseSrc + separator + '_=' + Date.now());
	}

	$('#login-page #captcha-box, #login-page #refresh').on('click', function(e){
		e.preventDefault();
		refreshCaptcha();
	});

	// 显示/隐藏密码切换
	$('#login-page #toggle-pwd').on('click', function(e){
		e.preventDefault();
		var pwdInput = $('#login-page #password');
		var icon = $(this).find('i');
		if (pwdInput.attr('type') === 'password') {
			pwdInput.attr('type', 'text');
			icon.removeClass('fa-eye-slash').addClass('fa-eye');
		} else {
			pwdInput.attr('type', 'password');
			icon.removeClass('fa-eye').addClass('fa-eye-slash');
		}
	});

	// 表单提交事件
	$('#login-page #login-form').on('submit', function(e){
		e.preventDefault();
		var _thisObj = $('#login-page #login-btn');
		var mobileObj = $('#login-page #mobile');
		var passwordObj = $('#login-page #password');
		var codeObj = $('#login-page #code');
		var mobile = $.trim(mobileObj.val());
		var password = $.trim(passwordObj.val());
		var code = $.trim(codeObj.val());

		if (!VERIFY['mobile'](mobile) && !VERIFY['email'](mobile)) {
			errorTips('手机/邮箱 格式不正确');
			mobileObj.closest('.input-wrap').addClass('error');
			mobileObj.focus();
			return false;
		}
		if (!VERIFY['password'](password)) {
			errorTips('密码格式不正确（至少6位）');
			passwordObj.closest('.input-wrap').addClass('error');
			passwordObj.focus();
			return false;
		}
		if (!VERIFY['code'](code)) {
			errorTips('验证码格式不正确');
			codeObj.closest('.input-wrap').addClass('error');
			codeObj.focus();
			return false;
		}

		_thisObj.button('loading');
		post('', {opn: 'login', mobile: mobile, password: password, code: code}, function(res) {
			if (res.code) {
				localStorage.setItem('login_number', mobile);
				window.location.href = res.data.url;
			} else {
				errorTips(res.msg);
				_thisObj.button('reset');
				refreshCaptcha();
				codeObj.val('').closest('.input-wrap').addClass('error');
			}
		});
		return false;
	});

	// 键入或聚焦时消除错误提示状态
	$('#login-page .input').on('input focus', function(){
		$(this).closest('.input-wrap, .form-group').removeClass('error');
	});
});
