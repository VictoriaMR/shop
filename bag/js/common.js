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
	loading: function(obj) {
		let style = ''; 
		if (obj) {
			style = 'style="position:absolute;"'
		} else {
			obj = $('body');
		}
		obj.find('.loading').remove();
		clearTimeout(this.timeoutVal);
		let html = '<div class="m-modal loading" '+style+'>\
						<div class="mask" '+style+'></div>\
						<div class="loading-block">\
							<div></div>\
							<div></div>\
							<div></div>\
						</div>\
					</div>';
		obj.append(html);
		this.stop();
	},
	loadout: function(obj, nostop){
		if (!obj) {
			obj = $('body');
		}
		obj.find('.loading').fadeOut(150, function(){
			$(this).remove();
			if (!nostop) {
				TIPS.start();
			}
		});
	},
	start: function() {
		$('body').css({'overflow': 'auto'});
	},
	stop: function() {
		$('body').css({'overflow': 'hidden'});
	},
	confirm: function(message, callback) {
		$('#confirm-modal').remove();
		$('body').append('<div class="confirm-modal" id="confirm-modal">\
			<div class="mask"></div>\
			<div class="content">\
				<button class="btn24 btn-black close-btn top-close-btn">Close</button>\
				<p class="layer mt32 tc f16 f600">'+message+'</p>\
				<div class="footer layer">\
					<button class="btn32 close-btn">CANCEL</button>\
					<button class="btn32 btn-black right confirm-btn">CONFIRM</button>\
				</div>\
			</div>\
		</div>');
		this.stop();
		$('body').on('click', '#confirm-modal .close-btn, #confirm-modal .mask', function(){
			TIPS.confirmClose();
		});
		$('body').on('click', '#confirm-modal .confirm-btn', function(){
			if (callback) {
				callback()
			} else {
				TIPS.confirmClose();
			}
		});
	},
	confirmClose: function() {
		$('#confirm-modal').fadeOut(200, function(){
			$(this).remove();
			TIPS.start();
		});
	}
};
const CART = {
	init: function() {
		$.post(URI+'cart/cartCount', {}, function(res){
			if (res.code === '200') {
				$('.icon-gouwuche').addClass('icon-gouwuchefill').removeClass('icon-gouwuche');
				$('.icon-gouwuchefill').parent().append('<span class="cart-number">'+(res.data > 99 ? 99 : res.data)+'</>');
			} else {
				$('.icon-gouwuchefill').addClass('icon-gouwuche').removeClass('icon-gouwuchefill');
				$('.icon-gouwuche').parent().find('.cart-number').remove();
			}
		});
	}
};
$(function(){
	//回顶按钮
	if (document.body.scrollHeight - 300 > window.screen.height) {
		$('body').append('<div id="scroll-top"><span class="iconfont icon-xiangshang3"></span></div>');
		window.addEventListener('scroll', function (){
			const top = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
			if (top > 300) {
				$('#scroll-top').addClass('popup');
			} else {
				$('#scroll-top').removeClass('popup');
			}
		});
		$('body').on('click', '#scroll-top', function(){
			$('html,body').animate({scrollTop: 0}, 300);
		});
	}
	if ($('.icon-gouwuche').length > 0) {
		CART.init();
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