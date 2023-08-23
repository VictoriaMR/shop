windowLoad(function(){
	LOGIN.init();
})
const LOGIN = {
	init: function () {
		var mobileObj = document.querySelector('input[name="mobile"]');
		var codeObj = document.querySelector('input[name="code"]');
		var passwordObj = document.querySelector('input[name="password"]');
		var loginBtnObj = document.querySelector('#login-btn');
		loginBtnObj.onclick = function() {
			var mobile = mobileObj.val;
			console.log(mobile, 'mobile')
			if (!VERIFY['mobile'](mobile)) {
				errorTips('手机号码格式不正确');
				return false;
			}
		}
		$('#login-btn').on('click', function() {
			let msg = '';
			$('#login-error').addClass('hidden');
			const thisobj = $(this);
			thisobj.parent('form').find('input:visible').each(function(){
				const name = $(this).attr('name');
				if (!VERIFY[name]($(this).val())) {
					$(this).focus();
					switch (name) {
						case 'mobile':
							msg = '手机号码格式不正确';
							break;
						case 'password':
							msg = '密码格式不正确';
							break;
						case 'code':
							msg = '验证码格式不正确';
							break;
						default:
							msg = '输入错误';
							break;
					}
					return false;
				}
			});
			if (msg != '') {
				errorObj.removeClass('hidden').find('#login-error-msg').text(msg);
				return false;
			}
			thisobj.button('loading');
			post(URI+'login/login', $(this).parent('form').serializeArray(), function(res) {
				if (res.code === 200) {
					window.location.href = res.data.url;
				} else {
					errorObj.removeClass('hidden').find('#login-error-msg').text(res.msg);
					thisobj.button('reset');
				}
			});
		});
		//验证码自动校正
		codeObj.on('blur', function(){
			const code = $(this).val();
			const thisobj = $(this);
			if (!VERIFY.code(code)) {
				errorObj.removeClass('hidden').find('#login-error-msg').text('验证码格式不正确');
				thisobj.parent().find('iconfont').remove();
				return false;
			}
			post(URI+'login/checkCode', {code: code}, function(res) {
				if (res.code === 200) {
					errorObj.addClass('hidden');
				} else {
					errorObj.removeClass('hidden').find('#login-error-msg').text(res.msg);
				}
			});
		});
		$('input').on('focus', function(){
			errorObj.addClass('hidden');
		});
		mobileObj.on('blur', function(){
			var value = $(this).val();
			if (value) {
				localStorage.setItem('login_name', value);
			}
		});
		document.onkeydown = function(e){
	        var ev = document.all ? window.event : e;
	        if(ev.keyCode==13) {
	            $('#login-btn').trigger('click');
	        }
	    }
	    var name = localStorage.getItem('login_name');
	    if (name) {
	    	mobileObj.val(name);
	    	passwordObj.focus();
	    }
	}
};