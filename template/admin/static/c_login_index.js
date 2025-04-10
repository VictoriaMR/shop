/* login_index */
function post(url, param, callback) {
	$.post(url, param, callback);
}
function get(url, param, callback) {
	$.get(url, param, callback);
}
function addRightTips(info, type, delay) {
	if (!info) {
		return false;
	}
    if (!delay) {
        delay = 5000;
    }
    info = info.replace(/\n/g,'<br>');
    var obj = document.getElementById('rightTips');
    if (!obj) {
		obj = document.createElement('div');
		obj.id = 'rightTips';
		document.getElementsByTagName('body')[0].appendChild(obj);
    }
    obj.innerHTML = '<div class="info '+type+'"><i class="glyphicon glyphicon-remove"></i>'+info+'</div>';
    obj.querySelector('.glyphicon-remove').onclick = function(e) {
    	e.parentNode.remove();
    };
    setTimeout(function(){
    	obj.remove();
    }, 5000);
}
function successTips(msg) {
	addRightTips(msg, 'success');
}
function errorTips(msg) {
	addRightTips(msg, 'error');
}
function showTips(res) {
	if (res.code == 200) {
		successTips(res.msg);
	} else {
		errorTips(res.msg);
	}
}
/* login_index */
const VERIFY = {
    mobile: function (mobile) {
        return this.check(mobile, /^1[3456789]\d{9}$/);
    },
    email: function (email) {
        return this.check(email, /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/);
    },
    password: function (password) {
        return this.check(password, /^[0-9A-Za-z]{6,}/);
    },
    code: function(code) {
        return this.check(code, /^[a-zA-Z0-9]{4,}/);
    },
    check: function(input, reg) {
        return reg.test(input?input.trim():'');
    }
};
/* login_index */
$(function(){
	$('#login-page [name="mobile"]').val(localStorage.getItem('login_number'));
	$('#login-page form').on('submit', function(){
		var _thisObj = $('#login-btn');
		var mobile = $('input[name="mobile"]').val();
		if (!VERIFY['mobile'](mobile)) {
			errorTips('手机号码格式不正确');
			return false;
		}
		var password = $('input[name="password"]').val();
		if (!VERIFY['password'](password)) {
			errorTips('密码格式不正确');
			return false;
		}
		var code = $('input[name="code"]').val();
		if (!VERIFY['code'](code)) {
			errorTips('验证码格式不正确');
			return false;
		}
		_thisObj.button('loading');
		post('/login/login', {mobile:mobile,password:password,code:code}, function(res) {
			if (res.code === 200) {
				localStorage.setItem('login_number', mobile);
				window.location.href = res.data.url;
			} else {
				errorTips(res.msg);
				_thisObj.button('reset');
			}
		});
		return false;
	});
});