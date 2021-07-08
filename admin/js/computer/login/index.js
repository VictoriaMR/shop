var LOGIN = {
	init: function () {
		$('#login-btn').on('click', function() {
			var msg = '';
			$('#login-error').addClass('hidden');
			$(this).parent('form').find('input:visible').each(function(){
				var name = $(this).attr('name');
				if (!VERIFY[name]($(this).val())) {
					$(this).focus();
					switch (name) {
						case 'phone':
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
				$('#login-error').removeClass('hidden').find('#login-error-msg').text(msg);
				return false;
			}
			$(this).button('loading');
			$.post(URI+'login/login', $(this).parent('form').serializeArray(), function(res) {
				if (res.code == 200) {
					window.location.href = res.data.url;
				} else {
					$('#login-error').removeClass('hidden').find('#login-error-msg').text(res.message);
				}
			});
			$(this).button('reset');
		});
		//验证码自动校正
		$('input[name="code"]').on('blur', function(){
			var code = $(this).val();
			var thisobj = $(this);
			if (!VERIFY.code(code)) {
				$('#login-error').removeClass('hidden').find('#login-error-msg').text('验证码格式不正确');
				thisobj.parent().find('iconfont').remove();
				return false;
			}
			$.post(URI+'login/checkCode', {code: code}, function(res) {
				if (res.code == 200) {
					$('#login-error').addClass('hidden');
				} else {
				}
			});
		});
		$('input').on('focus', function(){
			$('#login-error').addClass('hidden');
		});
		document.onkeydown = function(e){
	        var ev = document.all ? window.event : e;
	        if(ev.keyCode==13) {
	            $('#login-btn').trigger('click');
	        }
	    }
	}
};