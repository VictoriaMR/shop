const LOGIN = {
	init: function() {
		const _this = this;
		$('#login-page .input').on('focus', function(){
			$(this).parent().removeClass('error').parent().find('.error-msg').remove();
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
			if ($(this).find('.icon-squarecheck').length > 0) {
				$(this).find('.iconfont').removeClass('icon-squarecheck').addClass('icon-squarecheckfill');
				$(this).find('input').val(1);
			} else {
				$(this).find('.iconfont').removeClass('icon-squarecheckfill').addClass('icon-squarecheck');
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
			_this.loading(_thisObj, 'Send...');
			$.post(URI+'login/sengCode', {email: email}, function(res) {
				_this.loaded(_thisObj);
				if (res.code === 200 || res.code === '200') {
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
			_this.loading(_thisObj, 'LOGGING IN...');
			$.post(URI+'login/login', param, function(res) {
				if (res.code === 200 || res.code === '200') {
					localStorage.setItem('login_token', res.data.token);
					window.location.href = res.data.url ? res.data.url : URI;
				} else {
					_this.loaded(_thisObj);
					for (let i in res.message) {
						_this.loginError($('.login [name="'+i+'"]').parent(), res.message[i]);
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
		obj.parent().find('.error-msg').remove();
		obj.parent().append('<p class="error-msg">'+msg+'</p>');
	},
	loading: function(obj, msg) {
		obj.data('text', obj.text());
		obj.text(msg).attr('disabled', true);
	},
	loaded: function(obj) {
		obj.text(obj.data('text')).attr('disabled', false);
	}
};