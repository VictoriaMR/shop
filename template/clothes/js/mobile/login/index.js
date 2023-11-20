function autoChange(obj1, obj2) {
	for (var i=0; i<obj1.length; i++) {
		obj1[i].style.display = 'none';
	}
	for (var i=0; i<obj2.length; i++) {
		obj2[i].style.display = 'block';
	}
}
pageReady(function(){
	// 设置最小高度
	var pageObj = document.getElementById('login-page');
	pageObj.style.minHeight = document.documentElement.clientHeight-document.querySelector('header').clientHeight-document.querySelector('footer').clientHeight+'px';
	// 登录注册切换
	$click('.nav .nav-item', function(){
		if (this.classList.contains('active')) {
			return false;
		}
		this.classList.add('active');
		var loginBoxObj = pageObj.querySelector('#login-box');
		var signBoxObj = pageObj.querySelector('#sign-box');
		var passwordBoxObj = pageObj.querySelectorAll('.password-box');
		var verifyBoxObj = pageObj.querySelectorAll('.verify-code-box');
		if (this.getAttribute('data-type') == 'login') {
			pageObj.querySelector('.nav .nav-item[data-type="sign"]').classList.remove('active');
			signBoxObj.style.display = 'none';
			loginBoxObj.style.display = 'block';
			autoChange(passwordBoxObj, verifyBoxObj);
		} else {
			pageObj.querySelector('.nav .nav-item[data-type="login"]').classList.remove('active');
			loginBoxObj.style.display = 'none';
			signBoxObj.style.display = 'block';
			autoChange(verifyBoxObj, passwordBoxObj);
		}
	});
	// 验证码密码切换
	$click('.footer .change-btn', function(){
		var passwordBoxObj = pageObj.querySelectorAll('.password-box');
		var verifyBoxObj = pageObj.querySelectorAll('.verify-code-box');
		if (this.getAttribute('data-type') == 'code') {
			autoChange(passwordBoxObj, verifyBoxObj);
		} else {
			autoChange(verifyBoxObj, passwordBoxObj);
		}
	});
	// 验证码登录帮助
	$click('.verify-code-box .tips-title', function(){
		pageObj.querySelector('.verify-code-box .tips').style.display = 'block';
	});
	// 验证码登录帮助关闭
	$click('.verify-code-box .tips .mask', function(){
		pageObj.querySelector('.verify-code-box .tips').style.display = 'none';
	});
	// 发送验证码
	$click('.verify-code-btn', function(){
		var emailObj = pageObj.querySelector('input[name="email"]');
		if (!VERIFY.email(emailObj.value)) {
			inputError(emailObj, distT('email_error'));
			return false;
		}
		Tool.loading(this, 2);
		post('/login/sengCode', {email:emailObj.value}, function(res){
			Tool.hide();
			if (res.code == 200) {

			} else {
				inputError(emailObj, res.msg);
			}
		});
	});
	//验证码登录login 按钮点击
	$click('.verify-code-box .login-btn', function(){
		var emailObj = pageObj.querySelector('input[name="email"]');
		if (!VERIFY.email(emailObj.value)) {
			inputError(emailObj, distT('email_error'));
			return false;
		}
		var codeObj = pageObj.querySelector('input[name="verify_code"]');
		if (!VERIFY.code(codeObj.value)) {
			inputError(codeObj, distT('code_error'));
			return false;
		}
		Tool.loading(this);
		post('/login', {email:emailObj.value, verify_code: codeObj.value}, function(){
			Tool.hide();
		});
	});
	// 注册登录
	$click('#sign-box .sign-btn', function(){
		var emailObj = pageObj.querySelector('input[name="email"]');
		if (!VERIFY.email(emailObj.value)) {
			inputError(emailObj, distT('email_error'));
			return false;
		}
		var codeObj = pageObj.querySelector('input[name="verify_code"]');
		if (!VERIFY.code(codeObj.value)) {
			inputError(codeObj, distT('code_error'));
			return false;
		}
		Tool.loading(this);
		post('/login/register', {email:emailObj.value, password:passwordObj.value}, function(){
			Tool.hide();
		});
	});
});