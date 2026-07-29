$(function(){
	$('#login-page #mobile').val(localStorage.getItem('login_number'));
	$('#login-page #login-form').on('submit', function(e){
		e.preventDefault();
		var _thisObj = $('#login-page #login-btn');
		var mobileObj = $('#login-page #mobile');
		var passwordObj = $('#login-page #password');
		var codeObj = $('#login-page #code');
		var mobile = mobileObj.val();
		var password = passwordObj.val();
		var code = codeObj.val();

		if (!VERIFY['mobile'](mobile) && !VERIFY['email'](mobile)) {
			errorTips('手机/邮箱 格式不正确');
			mobileObj.parent().addClass('error');
			return false;
		}
		if (!VERIFY['password'](password)) {
			errorTips('密码 格式不正确');
			passwordObj.parent().addClass('error');
			return false;
		}
		if (!VERIFY['code'](code)) {
			errorTips('验证码 格式不正确');
			codeObj.parent().addClass('error');
			return false;
		}
		_thisObj.button('loading');
		post('', {opn:'login', mobile:mobile, password:password, code:code}, function(res) {
			if (res.code) {
				localStorage.setItem('login_number', mobile);
				window.location.href = res.data.url;
			} else {
				errorTips(res.msg);
				_thisObj.button('reset');
			}
		});
		return false;
	});
	$('#login-page .input').on('input', function(){
		$(this).parent().removeClass('error');
	});
});