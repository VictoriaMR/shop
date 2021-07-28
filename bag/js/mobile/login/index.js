const LOGIN = {
	init: function() {
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
			if ($('#login-page .verify-code').is(':visible')) {
				$('#login-page .verify-code').hide();
				$('#login-page .password').show();
				$(this).text('Sign in with Verify Code');
			} else {
				$('#login-page .password').hide();
				$('#login-page .verify-code').show();
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
	},
};
$(function(){
	LOGIN.init();
});