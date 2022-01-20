$(function(){
	$('.login-content .help .pointer').mouseover(function(){
		$(this).next().show();
	}).mouseleave(function(){
		$(this).next().hide();
	});
});