windowLoad(function(){
	LOGIN.init();
})
const LOGIN = {
	init: function () {
		var mobileObj = document.querySelector('input[name="mobile"]');
		var codeObj = document.querySelector('input[name="code"]');
		var passwordObj = document.querySelector('input[name="password"]');
		var loginBtnObj = document.querySelector('#login-btn');
		loginBtnObj.onclick = function() {
			if (loginBtnObj.getAttribute('data-loading') == '1') {
				return false;
			}
			var mobile = mobileObj.value;
			console.log(mobile)
			if (!VERIFY['mobile'](mobile)) {
				errorTips('手机号码格式不正确');
				return false;
			}
			var password = passwordObj.value;
			if (!VERIFY['password'](password)) {
				errorTips('密码格式不正确');
				return false;
			}
			var code = codeObj.value;
			if (!VERIFY['code'](code)) {
				errorTips('验证码格式不正确');
				return false;
			}
			var param = {
				mobile: mobile,
				password: password,
				code: code
			};
			loginBtnObj.setAttribute('data-loading', '1');
			post(URI+'login/login', param, function(res) {
				if (res.code === 200) {
					window.location.href = res.data.url;
				} else {
					errorTips(res.msg);
					loginBtnObj.setAttribute('data-loading', '0');
				}
			});
		}
	}
};