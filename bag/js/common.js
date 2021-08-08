//缩放界面后重新设定html font size
let view_jsset_font, result_font, user_webset_font, clientWidth;
if(document.documentElement.currentStyle) {
    user_webset_font = document.documentElement.currentStyle['fontSize'];
} else {
    user_webset_font = getComputedStyle(document.documentElement,false)['fontSize'];
}
const xs = parseFloat(user_webset_font)/100;
const docEl = document.documentElement,
    resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
    recalc = function() {
        clientWidth = docEl.clientWidth;
        if (!clientWidth) return;
        if (!document.addEventListener) return;
        view_jsset_font = (clientWidth / 3.75);
        result_font = view_jsset_font/xs;
        docEl.style.fontSize = result_font + 'px';
    };
window.addEventListener(resizeEvt, recalc, false);
recalc();
const VERIFY = {
	phone: function (phone) {
		const reg = /^1[3456789]\d{9}$/;
		return VERIFY.check(phone, reg);
	},
	email: function (email) {
		return VERIFY.check(email, /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/);
	},
	password: function (password) {
		return VERIFY.check(password, /^[0-9A-Za-z]{6,}/);
	},
	code: function(code, len) {
		if (typeof len === 'undefined') {
			len = 4;
		}
		if (len === 4) {
			return VERIFY.check(code, /^[a-zA-Z0-9]{4}/);
		} else if(len === 6) {
			return VERIFY.check(code, /^[a-zA-Z0-9]{6}/);
		}
	},
	check: function(input, reg) {
		input = input.trim();
		if (input == '') return false;
		return reg.test(input);
	}
};
const TIPS = {
	error: function(msg) {
		this.init('error', msg)
	},
	success: function(msg, icon) {
		this.init('success', msg, icon)
	},
	init: function(type, msg, icon) {
		if (typeof msg === 'undefined' || msg === '') {
			return false;
		}
		if (typeof icon === 'undefined') {
			if (type === 'success') {
				icon = 'yuanxingxuanzhongfill';
			} else {
				icon = 'tishifill';
			}
		}
		const _this = this;
		$('#message-tips').remove();
		clearTimeout(_this.timeoutVal);
		let html = '<div id="message-tips" class="'+type+'">\
						<div class="content">\
							<div class="icon-content">\
								<span class="iconfont icon-'+icon+'"></span>\
							</div>\
							<div class="text-content">\
								<span>'+msg+'</span>\
							</div>\
						</div>\
						<span class="iconfont icon-guanbi1"></span>\
					</div>';
		$('body').append(html);
		setTimeout(function(){
			$('#message-tips').addClass('top');
		}, 100);
		_this.timeout();
		$('body').on('click', '#message-tips .icon-guanbi1', function(){
			clearTimeout(_this.timeoutVal);
			$('#message-tips').remove();
		});
	},
	timeout: function(obj) {
		this.timeoutVal = setTimeout(function(){
			$('#message-tips').fadeOut(300, function(){
				$(this).remove();
			});
		}, 5000);
	},
	loading: function(){
		$('#loading').remove();
		clearTimeout(this.timeoutVal);
		let html = '<div class="m-modal" id="loading">\
						<div class="mask"></div>\
						<div class="loading-block">\
							<div></div>\
							<div></div>\
							<div></div>\
						</div>\
					</div>';
		$('body').append(html);
		this.stop();
	},
	loadout: function(){
		$('#loading').fadeOut(150, function(){
			$(this).remove();
			TIPS.start();
		});
	},
	start: function() {
		$('body').css({'overflow': 'auto'});
	},
	stop: function() {
		$('body').css({'overflow': 'hidden'});
	},
};
$(function(){
	if ($('.icon-gouwuche').length > 0) {
		$.post(URI+'cart/cartCount', {}, function(res){
			console.log(res)
		});
	}
	$.post(URI+'api/stat', {url: window.location.pathname}, function(res){
		if (res.code === 10000 || res.code === '10000') {
			if (location.pathname.substring(0, 6) !== '/login') {
				const token = localStorage.getItem('login_token');
				$.post(URI+'login/loginToken', {token: token}, function(res){
					if (res.code === 200 || res.code === '200') {
						window.location.href = res.data.url ? res.data.url : URI;
					}
				});
			}
		}
	});
});