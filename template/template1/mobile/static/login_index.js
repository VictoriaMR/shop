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
		var emailObj = obj.find('[name="email"]');
		var passwordObj = obj.find('[name="password"]');
		var confirmPasswordObj = obj.find('[name="confirm_password"]');
		if (!VERIFY.email(emailObj.val())) {
			inputError(emailObj, 'Email invalid');
			check = false;
		}
		if (!VERIFY.password(passwordObj.val())) {
			inputError(passwordObj, 'Password invalid');
			check = false;
		}
		if (!VERIFY.password(confirmPasswordObj.val())) {
			inputError(confirmPasswordObj, 'Confirm password invalid');
			check = false;
		}
		if (passwordObj.val() && confirmPasswordObj.val() && passwordObj.val() != confirmPasswordObj.val()) {
			inputError(confirmPasswordObj, 'Confirm password not match');
			check = false;
		}
		if (!check) {
			return false;
		}
		post('', obj.serializeArray(), function(res){

		});
		return false;
	});
});
