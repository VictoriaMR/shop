$(function(){
	$('#login-btn').on('click', function(){
		var _thisObj = $(this);
		var mobile = $('input[name="mobile"]').val();
		if (!VERIFY['mobile'](mobile)) {
			errorTips('手机号码格式不正确');
			return false;
		}
		var password = $('input[name="password"]').val();
		if (!VERIFY['password'](password)) {
			errorTips('密码格式不正确');
			return false;
		}
		var code = $('input[name="code"]').val();
		if (!VERIFY['code'](code)) {
			errorTips('验证码格式不正确');
			return false;
		}
		// _thisObj.button('loading');
		post('/login/login', {mobile:mobile,password:password,code:code}, function(res) {
			console.log(res, 'res')
			if (res.code === 200) {
				window.location.href = res.data.url;
			} else {
				errorTips(res.msg);
				_thisObj.button('reset');
			}
		});
	});
});