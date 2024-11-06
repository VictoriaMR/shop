/* login_index */
$(document).ready(function(){
	$('.verify-code-group').on('click', '.input-title', function(){
		showModal('.verify-code-modal');
	});
	$('#login-page').on('click', '.btn.verification', function(){
		$('.verification').hide();
		$('.password').show();
	});
	$('#login-page').on('click', '.btn.password', function(){
		$('.password').hide();
		$('.verification').show();
	});
	$('#login-page').on('click', '.new-account-btn', function(){

	});
	$('#page-content').on('click', '.icon-eye-close,.icon-eye-open', function(){
		if ($(this).hasClass('icon-eye-close')) {
			$(this).removeClass('icon-eye-close').addClass('icon-eye-open').parent().find('input').attr('type', 'text');
		} else {
			$(this).removeClass('icon-eye-open').addClass('icon-eye-close').parent().find('input').attr('type', 'password');
		}
	});
	$('#register-page').on('submit', function(){
		console.log('here')
		var obj = $('#register-page');
		var check = true;
		var btnObj = obj.find('.btn-save');
		var emailObj = obj.find('[name="email"]');
		var passwordObj = obj.find('[name="password"]');
		var confirmPasswordObj = obj.find('[name="confirm_password"]');
		if (!VERIFY.email(emailObj.val())) {
			inputError(emailObj, distT('email_invalid'));
			check = false;
		}
		if (!VERIFY.password(passwordObj.val())) {
			inputError(passwordObj, distT('password_invalid'));
			check = false;
		}
		if (!VERIFY.password(confirmPasswordObj.val())) {
			inputError(confirmPasswordObj, distT('confirm_password_invalid'));
			check = false;
		}
		if (passwordObj.val() && confirmPasswordObj.val() && passwordObj.val() != confirmPasswordObj.val()) {
			inputError(confirmPasswordObj, distT('confirm_password_not_match'));
			check = false;
		}
		if (!check) {
			return false;
		}
		loading(btnObj);
		post('login/register', obj.serializeArray(), function(res){
			if (res.code) {
				tips(res.msg);
				setTimeout(function(){
					window.location.href = res.data.url ? res.data.url : '/';
				}, 2000)
			} else {
				for (var i in res.msg) {
					if (i == 'email') {
						inputError(emailObj, res.msg[i]);
					} else if (i == 'password') {
						inputError(passwordObj, res.msg[i]);
					} else if (i == 'confirm_password') {
						inputError(confirmPasswordObj, res.msg[i]);
					}
				}
				loaded(btnObj);
			}
		});
		return false;
	});
	$('#register-page').on('click', '.back-btn', function(){
		$('#register-page').hide();
		$('#login-page').show();
	});
	$('#login-page').on('click', '.new-account-btn', function(){
		$('#login-page').hide();
		$('#register-page').show();
	});
	$('#login-page').on('click', '.send-code-btn', function(){
		var emailObj = $('#login-page [name="email"]');
		var email = emailObj.val();
		if (!VERIFY.email(email)) {
			inputError(emailObj, distT('email_invalid'));
			return false;
		}
		var btnObj = $(this);
		loading(btnObj);
		post('login/sengCode', {email: email}, function(res){
			if (res.code) {
				tips(res.msg);
				var interval = setInterval(function(){
					if (res.data <= 0) {
						loaded(btnObj);
						clearInterval(interval);
					} else {
						btnObj.html((res.data--)+' s');
					}
				}, 1000);
			} else {
				for (var i in res.msg) {
					if (i == 'email') {
						inputError(emailObj, res.msg[i]);
					} else if (i == 'password') {
						inputError(passwordObj, res.msg[i]);
					} else if (i == 'confirm_password') {
						inputError(confirmPasswordObj, res.msg[i]);
					}
				}
				loaded(btnObj);
			}
		});
	});
});
