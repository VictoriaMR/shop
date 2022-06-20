$(function(){
	
	//发送验证码
	
});
var LOGIN = {
	init: function() {
		var _this = this;
		$('#login-page .help .pointer').mouseover(function(){
			$(this).parent().find('.help-tips').show();
		}).mouseleave(function(){
			$(this).parent().find('.help-tips').hide();
		});
		$('#login-page .agreement span').click(function(){
			var obj = $(this).parent().find('.iconfont');
			if (obj.hasClass('icon-fangxingweixuanzhong')) {
				obj.removeClass('icon-fangxingweixuanzhong').addClass('icon-fangxingxuanzhong').next().val(0);
			} else {
				obj.removeClass('icon-fangxingxuanzhong').addClass('icon-fangxingweixuanzhong').next().val(1);
			}
		});
		$('#login-page .change-login-type').click(function(){
			var passwordObj = $('.password-content');
			var verificationObj = $('.verification-content');
			var forgotObj = $('.forgot-password');
			if (passwordObj.is(':visible')) {
				passwordObj.hide();
				verificationObj.show();
				forgotObj.hide();
			} else {
				verificationObj.hide();
				passwordObj.show();
				forgotObj.show();
			}
		});
		//send email
		$('#login-page .send-email').on('click', function(){
			var obj = $('.signin-content [name="email"]');
			var email = obj.val();
			if (email === '') {
				_this.loginError(obj.parent(), 'This Email is required.');
				return false;
			}
			if (!VERIFY.email(email)) {
				_this.loginError(obj.parent(), 'This Email is Invalid.');
				return false;
			}
			var _thisObj = $(this);
			TIPS.loadingBtn(_thisObj, 'Send...');
			$.post(URI+'login/sengCode', {email: email}, function(res) {
				TIPS.loadoutBtn(_thisObj);
				if (res.code === '200') {
					_this.initSendCode(res.data);
					TIPS.success(res.message);
				} else {
					TIPS.error(res.message);
				}
			});
		});
		//login btn
		$('#login-page .login-btn').on('click', function(){
			var obj = $('.login [name="email"]');
			var param = {};
			var email = obj.val();
			if (email === '') {
				_this.loginError(obj.parent(), 'This Email is required.');
				return false;
			}
			if (!VERIFY.email(email)) {
				_this.loginError(obj.parent(), 'This Email is Invalid.');
				return false;
			}
			param.email = email;
			if ($('#login-page .verify-code').is(':visible')) {
				var code = '';
				var check = false;
				var obj = $('.login .input-group .input');
				obj.each(function(){
					var val = $(this).val();
					if (val === '') {
						check = true;
					}
					code += val;
				});
				if (check) {
					_this.loginError(obj.parent(), 'This Verification code is required.');
					return false;
				}
				if (!VERIFY.code(code, 6)) {
					_this.loginError(obj.parent(), 'This Verification code is Invalid.');
					return false;
				}
				param.verify_code = code;
			} else {
				obj = $('.login [name="password"]');
				var password = obj.val();
				if (password === '') {
					_this.loginError(obj.parent(), 'This Password is required.');
					return false;
				}
				if (!VERIFY.password(password)) {
					_this.loginError(obj.parent(), 'This Password is Invalid.');
					return false;
				}
				param.password = password;
			}
			var _thisObj = $(this);
			TIPS.loadingBtn(_thisObj, 'LOGGING IN...');
			$.post(URI+'login/login', param, function(res) {
				if (res.code === '200') {
					localStorage.setItem('login_token', res.data.token);
					window.location.href = res.data.url ? res.data.url : URI;
				} else {
					TIPS.loadoutBtn(_thisObj);
					for (var i in res.message) {
						_this.loginError($('.login [name="'+i+'"]').parent(), res.message[i]);
					}
				}
			});
		});
		//验证注册邮箱
		$('#login-page .register [name="email"]').on('blur', function(){
			var email = $(this).val();
			var obj = $(this).parent();
			if (email === '') {
				_this.loginError(obj, 'This Email is required.');
				return false;
			}
			if (!VERIFY.email(email)) {
				_this.loginError(obj, 'This Email is Invalid.');
				return false;
			}
			TIPS.loading();
			$.post(URI+'login/checkRegister', {email: email}, function(res){
				TIPS.loadout();
				if (res.code === '200') {
					_this.loginSuccess(obj, res.message);
				} else {
					_this.loginError(obj, res.message);
				}
			});
		});
		//注册按钮
		$('#login-page .register-btn').on('click', function(){
			var obj = $('.register [name="email"]');
			var param = {};
			var email = obj.val();
			if (email === '') {
				_this.loginError(obj.parent(), 'This Email is required.');
				return false;
			}
			if (!VERIFY.email(email)) {
				_this.loginError(obj.parent(), 'This Email is Invalid.');
				return false;
			}
			var pObj = $('.register [name="password"]');
			var password = pObj.val();
			if (password === '') {
				_this.loginError(pObj.parent(), 'This Password is required.');
				return false;
			}
			if (!VERIFY.password(password)) {
				_this.loginError(pObj.parent(), 'This Password is Invalid.');
				return false;
			}
			var rpObj = $('.register [name="repassword"]');
			var repassword = rpObj.val();
			if (repassword === '') {
				_this.loginError(rpObj.parent(), 'This Confirm Password is required.');
				return false;
			}
			if (!VERIFY.password(repassword)) {
				_this.loginError(rpObj.parent(), 'This Confirm Password is Invalid.');
				return false;
			}
			if (password != repassword) {
				_this.loginError(pObj.parent(), 'This Password is not match.');
				_this.loginError(rpObj.parent(), 'This Confirm Password is not match.');
				return false;
			}
			param.email = email;
			param.password = password;
			var _thisObj = $(this);
			TIPS.loadingBtn(_thisObj, 'CREATING AN ACCOUNT...');
			$.post(URI+'login/register', param, function(res) {
				if (res.code === '200') {
					localStorage.setItem('login_token', res.data.token);
					TIPS.success(res.message);
					setTimeout(function(){
						window.location.href = res.data.url ? res.data.url : URI;
					});
				} else {
					TIPS.loadoutBtn(_thisObj);
					for (var i in res.message) {
						_this.loginError($('.register [name="'+i+'"]').parent(), res.message[i]);
					}
				}
			});
		});
	},
	initSendCode: function(time){
		var _this = this;
		var btnObj = $('#login-page .send-email');
		if (time > 0) {
			btnObj.attr('disabled', true);
			btnObj.data('text', btnObj.text())
		}
		btnObj.text(time + ' s');
		var timeobj = setInterval(function() {
			if (time === 1) {
				clearInterval(timeobj);
				btnObj.attr('disabled', false);
				btnObj.text(btnObj.data('text'));
			} else {
				time --;
				btnObj.text(time + ' s');
			}
	    }, 1000);
	},
	loginError(obj, msg){
		obj.addClass('error');
		obj.parent().find('.message-tips').remove();
		obj.parent().append('<p class="message-tips error">'+msg+'</p>');
	},
	loginSuccess(obj, msg){
		obj.addClass('success');
		obj.parent().find('.message-tips').remove();
		obj.parent().append('<p class="message-tips success">'+msg+'</p>');
	}
};
$(function(){
	LOGIN.init();
});