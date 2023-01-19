//提示类
var TIPS = {
    error: function(msg) {
        this.init('error', msg);
    },
    success: function(msg) {
        this.init('success', msg);
    },
    init: function(type, msg) {
        var icon = 'tishifill';
        if (type === 'success') {
            icon = 'yuanxingxuanzhongfill';
        }
        var _this = this;
        var windowWidth = $(window).width();
        var left = (windowWidth - 1400) / 2;
        var width = windowWidth - 20;
        left = left > 0 ? left : 10;
        $('#message-tips').remove();
        clearTimeout(_this.timeoutVal);
        var html = '<div id="message-tips" class="'+type+'" style="left:'+left+'px;width:'+width+'px">\
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
    loading: function(obj, noStop) {
        var style = 'style="display:block;"'; 
        if (obj) {
            style = 'style="position:absolute;display:block;"';
        } else {
            obj = $('body');
        }
        obj.find('.loading').remove();
        clearTimeout(this.timeoutVal);
        var html = '<div class="modal loading" '+style+'>\
                        <div class="mask" '+style+'></div>\
                        <div class="loading-block">\
                            <div></div>\
                            <div></div>\
                            <div></div>\
                        </div>\
                    </div>';
        obj.append(html);
        if (!noStop) {
            this.stop();
        }
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
        var style = {overflow: 'auto'};
        if (this.scrollbarWidth > 0) {
            style['padding-right'] = 0;
            $('.meau-header.fixed').css({width: '100%'});
        }
        $('body').css(style);
    },
    stop: function() {
        if (typeof this.scrollbarWidth === 'undefined') {
            this.scrollbarWidth = this.getScrollbarWidth();
        }
        var style = {overflow: 'hidden'};
        if (this.scrollbarWidth > 0) {
            style['padding-right'] = this.scrollbarWidth;
            $('.meau-header.fixed').css({width: 'calc(100% - '+this.scrollbarWidth+'px)'});
        }
        $('body').css(style);
    },
    getScrollbarWidth: function () {
      var scrollDiv = document.createElement("div");
      scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
      document.body.appendChild(scrollDiv);
      var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
      document.body.removeChild(scrollDiv);
      return scrollbarWidth;
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
                callback($(this));
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
var CART = {
	init: function() {
		$.post('/cart/cartCount', {}, function(res){
			if (res.code === '200') {
				$('.icon-gouwuche').addClass('icon-gouwuchefill').removeClass('icon-gouwuche');
				$('.icon-gouwuchefill').parent().append('<span class="red-number">'+(res.data > 99 ? 99 : res.data)+'</>');
			} else {
				$('.icon-gouwuche').parent().find('.red-number').remove();
				$('.icon-gouwuchefill').not('.footer').addClass('icon-gouwuche').removeClass('icon-gouwuchefill');
			}
		});
	},
    count: function(count) {
        var obj = $('#cart');
        obj.find('.cart-count').remove();
        if (count > 0) {
            obj.append('<span class="cart-count">'+count+'</span>');
        }
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
		return js_language_text[name];
	}
	if (typeof js_language_text_common !== 'undefined') {
		return js_language_text_common[name];
	}
	return name;
}
function url(url, title, params) {
    var vars = {}, hash;
    var hashes = window.location.search.substr(1);
    var path = '';
    if (url) {
        var questionPos = url.indexOf('?');
        path = -1 === questionPos ? url : url.substring(0, questionPos);
        hashes = -1 !== questionPos ? url.substr(1 + questionPos) : '';
    } else {
        path = window.location.pathname;
    }
    if (hashes) {
        hashes = hashes.split('&');
    } else {
        hashes = [];
    }
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars[hash[0]] = hash[1];
    }
    var k;
    for (k in params) {
        if ('' === params[k]) {
            delete vars[k];
        } else {
            vars[k] = params[k];
        }
    }
    var k, q = '?';
    if (path) {
        q = path + q;
    }
    for (k in vars) {
        q += k + '=' + vars[k] + '&';
    }
    q = q.substr(0, q.length - 1);
    if (!'pushState' in window.history) {
        window.location.href=q;
    }else{
        if (isIE()) {
            History.pushState(null, title, q);
        } else {
            document.title = title;
            window.history.pushState('', null, q);
        }
    }
}
function isIE() {
    return navigator.appName == 'Microsoft Internet Explorer' || window.ActiveXObject || 'ActiveXObject' in window;
}
function priceChange(amount) {
    //paypal分期显示组件更新金额
    var obj = document.querySelector('[data-pp-message]');
    if (obj) {
        obj.setAttribute('data-pp-amount', amount);
    }
}
(function($){
	$.fn.bigImage = function(){
		var obj = $(this);
		obj.css({cursor: 'pointer'});
		obj.attr('title', '点击查看大图');
		obj.on('click', function(){
			var bigImageObj = $('#dealbox-bigimage');
			if (bigImageObj.length == 0) {
				var html = '<div id="dealbox-bigimage">\
								<div class="mask"></div>\
								<div class="centerShow">\
									<img src="/image/common/noimg.png">\
								</div>\
							</div>';
				$('body').append(html);
				bigImageObj = $('#dealbox-bigimage');
			}
			var src = obj.attr('src').replace('/200', '').replace('/400', '').replace('/600', '');
			bigImageObj.find('.centerShow img').attr('src', src);
			bigImageObj.find('.centerShow img').on('load', function(){
				bigImageObj.offsetCenter().dealboxShow();
			});
		});
	};
	$.fn.imageUpload = function(name, cate, callback) {
		var obj = $(this);
		obj.each(function(){
			var thisobj = $(this);
			thisobj.css({cursor: 'pointer'});
			var guid_name = guid();
			thisobj.data('file', guid_name);
			thisobj.parent().append('<input name="'+guid_name+'" type="file" accept=".bmp,.jpg,.png,.jpeg,image/bmp,image/jpg,image/png,image/jpeg" class="hide" readonly="readonly"/>');
			thisobj.on('click', function(){
				var file = $(this).data('file');
				$('[name="'+file+'"]').click();
			});
			$('[name="'+guid_name+'"]').on('change', function (e) {
	            var thissrc = thisobj.attr('src');
	            thisobj.data('src', thissrc);
	            thisobj.attr('src', '/image/common/loading.png').addClass('loading');
				var files = $(this).prop('files');
				var data = new FormData();
            	data.append('file', files[0]);
            	data.append('cate', cate);
  				$.ajax({
					url: '/api/upload',
					type: 'POST',
					data: data,
					cache: false,
					processData: false,
					contentType: false,
					success: function(res) {
	                    if (res.code == 200) {
	                    	thisobj.removeClass('loading').attr('src', res.data.url);
	                    	var $inputObj = thisobj.parent().find('[name="'+name+'"]');
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
    //同步数据
    if ($('.icon-gouwuche').length > 0) {
        REQUEST_PARAM.cart = 1;
    }
    if ($('.desc-title .info-name').length > 0) {
        REQUEST_PARAM.login = 1;
    }
    post('/api/stat', REQUEST_PARAM, function(res) {
        if (REQUEST_PARAM.cart > 0) {
            CART.count(res.data.cart_count);
        }
        if (REQUEST_PARAM.login > 0) {
            MEMBER.init(res.data.member);
        }
    });
	//回顶按钮
	if (document.body.scrollHeight - 300 > window.screen.height) {
		$('body').append('<div id="scroll-top"><span class="iconfont icon-xiangshang3"></span></div>');
		window.addEventListener('scroll', function (){
			var top = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
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
	$('img.lazyload').lazyload();
});