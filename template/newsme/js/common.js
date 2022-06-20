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
		if (!input) {
			return false;
		}
		input = input.trim();
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
	loadingBtn: function(obj, msg) {
		obj.data('text', obj.text());
		obj.text(msg ? msg : appT('loading')).attr('disabled', true);
	},
	loadoutBtn: function(obj) {
		obj.text(obj.data('text')).attr('disabled', false);
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
				<p class="layer mt20 tc f16 f600 pb20">'+message+'</p>\
				<div class="footer layer">\
					<button class="btn32 close-btn">Cancel</button>\
					<button class="btn32 btn-black right confirm-btn">Confirm</button>\
				</div>\
			</div>\
		</div>');
		this.stop();
		$('body').on('click', '#confirm-modal .close-btn, #confirm-modal .mask', function(){
			TIPS.confirmClose();
		});
		$('body').on('click', '#confirm-modal .confirm-btn', function(){
			if (callback) {
				callback($(this))
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
				$('.icon-gouwuchefill').parent().append('<span class="red-number">'+(res.data > 99 ? 99 : res.data)+'</>');
			} else {
				$('.icon-gouwuche').parent().find('.red-number').remove();
				$('.icon-gouwuchefill').not('.footer').addClass('icon-gouwuche').removeClass('icon-gouwuchefill');
			}
		});
	}
};
function S4() {
	return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
}
function guid() {
	return (S4()+S4()+'-'+S4()+'-'+S4()+'-'+S4()+'-'+S4()+S4()+S4());
}
function appT(name) {
	if (typeof js_language_text !== 'undefined') {
		if (typeof js_language_text[name] !== 'undefined') {
			return js_language_text[name];
		}
	}
	if (typeof js_language_text_common !== 'undefined') {
		if (typeof js_language_text_common[name] !== 'undefined') {
			return js_language_text_common[name];
		}
	}
	return name;
}
(function($){
	$.fn.bigImage = function(){
		const obj = $(this);
		obj.css({cursor: 'pointer'});
		obj.attr('title', '点击查看大图');
		obj.on('click', function(){
			let bigImageObj = $('#dealbox-bigimage');
			if (bigImageObj.length == 0) {
				var html = '<div id="dealbox-bigimage">\
								<div class="mask"></div>\
								<div class="centerShow">\
									<img src="'+URI+'image/common/noimg.png">\
								</div>\
							</div>';
				$('body').append(html);
				bigImageObj = $('#dealbox-bigimage');
			}
			const src = obj.attr('src').replace('/200', '').replace('/400', '').replace('/600', '');
			bigImageObj.find('.centerShow img').attr('src', src);
			bigImageObj.find('.centerShow img').on('load', function(){
				bigImageObj.offsetCenter().dealboxShow();
			});
		});
	};
	$.fn.imageUpload = function(name, cate, callback) {
		const obj = $(this);
		obj.each(function(){
			const thisobj = $(this);
			thisobj.css({cursor: 'pointer'});
			const guid_name = guid();
			thisobj.data('file', guid_name);
			thisobj.parent().append('<input name="'+guid_name+'" type="file" accept=".bmp,.jpg,.png,.jpeg,image/bmp,image/jpg,image/png,image/jpeg" class="hide" readonly="readonly"/>');
			thisobj.on('click', function(){
				const file = $(this).data('file');
				$('[name="'+file+'"]').click();
			});
			$('[name="'+guid_name+'"]').on('change', function (e) {
	            const thissrc = thisobj.attr('src');
	            thisobj.data('src', thissrc);
	            thisobj.attr('src', URI+'image/common/loading.png').addClass('loading');
				const files = $(this).prop('files');
				const data = new FormData();
            	data.append('file', files[0]);
            	data.append('cate', cate);
  				$.ajax({
					url: URI+'api/upload',
					type: 'POST',
					data: data,
					cache: false,
					processData: false,
					contentType: false,
					success: function(res) {
	                    if (res.code == 200) {
	                    	thisobj.removeClass('loading').attr('src', res.data.url);
	                    	const $inputObj = thisobj.parent().find('[name="'+name+'"]');
	                    	if ($inputObj.length === 0) {
	                    		thisobj.parent().append('<input name="'+name+'" value="'+(res.data.cate+'/'+res.data.name+'.'+res.data.type)+'" class="hide" />');
	                    	} else {
	                    		$inputObj.val(res.data.cate+'/'+res.data.name+'.'+res.data.type);
	                    	}
	                    	if (callback) {
	                    		callback(res.data);
	                    	}
	                    } else {
	                    	TIPS.error(res.message);
	                    	thisobj.removeClass('loading').attr('src', thisobj.data('src'));
	                    }
	                },
	                error: function(res) {
	                	TIPS.error('网络错误, 上传失败');
	                	thisobj.removeClass('loading').attr('src', thisobj.data('src'));
	                }
				});
			});
		});
	};
}(jQuery));
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
	if ($('.icon-gouwuche, .icon-gouwuchefill').length > 0) {
		CART.init();
	}
	$.post(URI+'api/stat', {url: window.location.pathname});
	$('img.lazyload').lazyload();
});