/* login_forgot */
$(document).ready(function(){
	$('#forgot-page').on('submit', function(){
		var obj = $(this);
		var check = true;
		var btnObj = obj.find('.btn-save');
		var emailObj = obj.find('[name="email"]');
		if (!VERIFY.email(emailObj.val())) {
			inputError(emailObj, distT('email_invalid'));
			check = false;
		}
		if (!check) {
			return false;
		}
		loading(btnObj);
		post('/login/sendForgotEmail', obj.serializeArray(), function(res){
			if (res.code) {
				tips(res.msg);
				setTimeout(function(){
					window.location.href = $('#forgot-page .back-btn').attr('href');
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
});
