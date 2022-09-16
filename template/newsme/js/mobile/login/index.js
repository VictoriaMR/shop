const LOGIN = {
	init: function() {
		const _this = this;
		$('#login-page .input').on('focus', function(){
			$(this).parent().removeClass('error').removeClass('success').parent().find('.message-tips').remove();
		});
		$('#login-page .verify-code .input').on('input', function(event){
			const val = $(this).val();
			const reg = /^\+?[0-9]*$/;
			if (!reg.test(val)) {
				$(this).val('');
				return false;
			}
			$(this).next().focus();
		});
		$('#login-page .verify-code .input').on('keydown', function(event){
			const val = $(this).val();
			$(this).val('');
			$(this).parent().find('.input').removeClass('error');
			if (event.keyCode === 8) {
				if (val === '') {
					$(this).prev().val('');
					$(this).prev().focus();
				} else {
					$(this).val('');
					return false;
				}
			}
		}).on('focus', function(){
			$(this).parent().addClass('focus');
		}).on('blur', function(){
			$(this).parent().removeClass('focus');
		});
		$('#login-page .change-verify-btn').on('click', function(){
			const verifyObj = $('#login-page .verify-code');
			const passwordObj = $('#login-page .password');
			if (verifyObj.is(':visible')) {
				verifyObj.hide();
				passwordObj.show();
				$('#login-page .login .email .send-email').hide();
				$(this).text('Sign in with Verify Code');
			} else {
				passwordObj.hide();
				verifyObj.show();
				$('#login-page .login .email .send-email').show();
				$(this).text('Sign in with Password');
			}
		});
		$('#login-page .help-btn').on('click', function(e){
			e.stopPropagation();
			$('#login-page .help-tips').toggle();
		});
		$('body').on('click', function(){
			$('#login-page .help-tips').hide();
		});
		$('#login-page .change-login-sign').on('click', function(){
			$('#login-page .login').toggle();
			$('#login-page .register').toggle();
		});
		$('#login-page .agreement').on('click', function(){
			if ($(this).find('.icon-fangxingweixuanzhong').length > 0) {
				$(this).find('.iconfont').removeClass('icon-fangxingweixuanzhong').addClass('icon-fangxingxuanzhongfill');
				$(this).find('input').val(1);
			} else {
				$(this).find('.iconfont').removeClass('icon-fangxingxuanzhongfill').addClass('icon-fangxingweixuanzhong');
				$(this).find('input').val(0);
			}
		});
		//send email
		$('#login-page .send-email').on('click', function(){
			const obj = $('.login [name="email"]');
			const email = obj.val();
			if (email === '') {
				_this.loginError(obj.parent(), 'This Email is required.');
				return false;
			}
			if (!VERIFY.email(email)) {
				_this.loginError(obj.parent(), 'This Email is Invalid.');
				return false;
			}
			const _thisObj = $(this);
			TIPS.loadingBtn(_thisObj, 'Send...');
			$.post('/login/sengCode', {email: email}, function(res) {
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
			let obj = $('.login [name="email"]');
			let param = {};
			const email = obj.val();
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
				let code = '';
				let check = false;
				const obj = $('.login .input-group .input');
				obj.each(function(){
					const val = $(this).val();
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
				const password = obj.val();
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
			const _thisObj = $(this);
			TIPS.loadingBtn(_thisObj, 'LOGGING IN...');
			$.post('/login/login', param, function(res) {
				if (res.code === '200') {
					localStorage.setItem('login_token', res.data.token);
					window.location.href = res.data.url;
				} else {
					TIPS.loadoutBtn(_thisObj);
					for (let i in res.message) {
						_this.loginError($('.login [name="'+i+'"]').parent(), res.message[i]);
					}
				}
			});
		});
		//验证注册邮箱
		$('#login-page .register [name="email"]').on('blur', function(){
			const email = $(this).val();
			let obj = $(this).parent();
			if (email === '') {
				_this.loginError(obj, 'This Email is required.');
				return false;
			}
			if (!VERIFY.email(email)) {
				_this.loginError(obj, 'This Email is Invalid.');
				return false;
			}
			TIPS.loading();
			$.post('/login/checkRegister', {email: email}, function(res){
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
			let obj = $('.register [name="email"]');
			let param = {};
			const email = obj.val();
			if (email === '') {
				_this.loginError(obj.parent(), 'This Email is required.');
				return false;
			}
			if (!VERIFY.email(email)) {
				_this.loginError(obj.parent(), 'This Email is Invalid.');
				return false;
			}
			let pObj = $('.register [name="password"]');
			const password = pObj.val();
			if (password === '') {
				_this.loginError(pObj.parent(), 'This Password is required.');
				return false;
			}
			if (!VERIFY.password(password)) {
				_this.loginError(pObj.parent(), 'This Password is Invalid.');
				return false;
			}
			let rpObj = $('.register [name="repassword"]');
			const repassword = rpObj.val();
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
			const _thisObj = $(this);
			TIPS.loadingBtn(_thisObj, 'CREATING AN ACCOUNT...');
			$.post('/login/register', param, function(res) {
				if (res.code === '200') {
					localStorage.setItem('login_token', res.data.token);
					TIPS.success(res.message);
					setTimeout(function(){
						window.location.href = res.data.url;
					});
				} else {
					TIPS.loadoutBtn(_thisObj);
					for (let i in res.message) {
						_this.loginError($('.register [name="'+i+'"]').parent(), res.message[i]);
					}
				}
			});
		});
		TIPS.timeout();
	},
	initSendCode: function(time){
		const _this = this;
		const btnObj = $('#login-page .send-email');
		if (time > 0) {
			btnObj.attr('disabled', true);
			btnObj.data('text', btnObj.text())
		}
		btnObj.text(time + ' s');
		const timeobj = setInterval(function() {
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