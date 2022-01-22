$(function(){
	$('.login-content .help .pointer').mouseover(function(){
		$('.login-content .help .help-tips').show();
	}).mouseleave(function(){
		$('.login-content .help .help-tips').hide();
	});
	$('.login-content .agreement span').click(function(){
		var obj = $(this).parent().find('.iconfont');
		if (obj.hasClass('icon-fangxingweixuanzhong')) {
			obj.removeClass('icon-fangxingweixuanzhong').addClass('icon-fangxingxuanzhong').next().val(0);
		} else {
			obj.removeClass('icon-fangxingxuanzhong').addClass('icon-fangxingweixuanzhong').next().val(1);
		}
	});
	$('.login-content .change-login-type').click(function(){
		var passwordObj = $('.login-content .password-content');
		var verificationObj = $('.login-content .verification-content');
		var forgotObj = $('.login-content .forgot-password');
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
});