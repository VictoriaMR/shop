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
	code: function(code) {
		return VERIFY.check(code, /^[a-zA-Z0-9]{4,}/);
	},
	check: function(input, reg) {
		input = input.trim();
		if (input == '') return false;
		return reg.test(input);
	}
};
function confirm(msg, callbck) {
	if ($('#confirm-pop').length == 0) {
		const html = '<div id="confirm-pop">\
						<div class="mask"></div>\
						<div class="content">\
							<div class="message f18 tc"></div>\
							<div class="mt20">\
								<button class="btn cancel left w30">取消</button>\
								<button class="btn btn-primary right confirm w30">确定</button>\
								<div class="clear"></div>\
							</div>\
						</div>\
					</div>';
		$('body').append(html);
	}
	const obj = $('#confirm-pop');
	obj.find('.message').text(msg);
	obj.show();
	obj.on('click', '.btn.cancel, .mask', function(){
		obj.hide();
	});
	obj.on('click', '.btn.confirm', function(){
		callbck($(this), obj);
	});
}
function post(uri, param, success, error) {
	$.post(uri, param, function(res) {
		if (res.code === '200') {
			if (res.message) {
				successTips(res.message)
			}
			if (success) {
				success(res.data);
			}
		} else {
			errorTips(res.message);
			if (error) {
				error();
			}
		}
	});
}
function addRightTips(info, type, delay) {
	if (typeof info === 'undefined') {
		return false;
	}
    if (typeof delay === 'undefined') {
        delay = 5000;
    }
    info = info.replace(/\n/g,'<br>');
    if($('#rightTips').length === 0) {
        $('body').append('<div id="rightTips"></div>');
        $('#rightTips').on('click', '.info .glyphicon-remove', function(){
            $(this).parent().remove();
        });
    }
    const timestamp = new Date().getTime();
    $('#rightTips').prepend('<div class="info '+type+'" id="info_'+timestamp+'"><i class="glyphicon glyphicon-remove"></i>'+info+'</div>');
    $("#info_" + timestamp).delay(delay).fadeOut('slow', function () {
        $("#info_" + timestamp).remove()
    });
}
function successTips(msg) {
	addRightTips(msg, 'success');
}
function errorTips(msg) {
	addRightTips(msg, 'error');
}
function isScroll() {
    return document.body.scrollHeight > (window.innerHeight || document.documentElement.clientHeight);
}
function progressing(val) {
	if (window.location.pathname === '/login') {
		return false;
	}
    if (document.readyState == 'complete') {
        val = val + 50;
        val = val > 100 ? 100 : val;
    } else {
        if (val < 80) {
            val = val + 20;
        }
    }
    $('#progressing').stop(true,true).animate({'width':val+'%'}, 100, function(){
        if (val >= 100) {
            $('#progressing').fadeOut(150);
        } else {
            progressing(val);
        }
    });
    return true;
}
function S4() {
   return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
}
function guid() {
   return (S4()+S4()+'-'+S4()+'-'+S4()+'-'+S4()+'-'+S4()+S4()+S4());
}
(function($){
	$.fn.offsetCenter = function(width, height) {
		const obj = $(this).find('.centerShow');
		let w = width;
		if (typeof width === 'undefined'){
			w = $(window).innerWidth();
		}
		w = (w - obj.innerWidth())/2;
		let h = height
		if(typeof height === 'undefined'){
			h = $(window).innerHeight();
		}
		h = (h / 2) - (obj.actual('innerHeight') / 2);
		h = h < 0 ? 0 : h;
		obj.css('position','fixed');
		obj.css('top',h+'px');
		obj.css('left',w+'px');
		if (obj.data("resizeSign") !='ok') {
			obj.data('resizeSign','ok');
			$(window).resize(function () {
				obj.offsetCenter(width, height);
			});
			obj.find('.close').on('click', function() {
				obj.parent().dealboxHide();
			});
			obj.parent().find('.mask').on('click', function() {
				obj.parent().dealboxHide();
			});
		}
		return $(this);
	};
	$.fn.dealboxShow = function(title, width, height) {
		const obj = $(this);
		obj.offsetCenter();
		$('body').css({'overflow': 'hidden'});
		if (isScroll()) {
			$('body').css({'padding-right': '6.5px'});
		}
		if (title) {
			obj.find('.dealbox-title').text(title);
		}
		obj.show();
		return obj;
	};
	$.fn.dealboxHide = function(width, height) {
		const obj = $(this);
		$('body').css({'overflow': 'auto'});
		$('body').css({'padding-right': 0});
		obj.hide();
		return obj;
	};
	$.fn.switchBtn = function(status) {
		const obj = $(this);
		obj.data('status', status);
		if (status == 1) {
			obj.find('.switch_status').removeClass('off').addClass('on');
		} else {
			obj.find('.switch_status').removeClass('on').addClass('off');
		}
		return obj;
	};
	$.fn.formFilter = function() {
		const obj = $(this);
		let status = true;
		obj.find('[required="required"]').each(function(){
			var val = $(this).val();
			if (val == '') {
				errorTips($(this).prev().text()+'不能为空');
				status = false;
				return false;
			}
		});
		return status;
	};
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
	$.fn.imageUpload = function(cate, callback) {
		const obj = $(this);
		obj.each(function(){
			const thisobj = $(this);
			thisobj.css({cursor: 'pointer'});
			const guid_name = guid();
			thisobj.data('file', guid_name);
			thisobj.parent().append('<input name="'+guid_name+'" type="file" accept=".bmp,.jpg,.png,.jpeg,image/bmp,image/jpg,image/png,image/jpeg" class="hide" readonly="readonly"/>');
			thisobj.on('click', function(event){
				event.stopPropagation();
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
							if (thisobj.get(0).tagName == 'img') {
								thisobj.removeClass('loading').attr('src', res.data.url);
							} else {
								thisobj.removeClass('loading').find('img').attr('src', res.data.url);
							}
							if (callback) {
								callback(res.data, thisobj);
							}
						} else {
							errorTips(res.message);
							thisobj.removeClass('loading').attr('src', thisobj.data('src'));
						}
					},
					error: function(res) {
						errorTips('网络错误, 上传失败');
						thisobj.removeClass('loading').attr('src', thisobj.data('src'));
					}
				});
			});
		});
	};
}(jQuery));
$(function(){
	$('form .btn-group .btn').on('click', function(){
		const obj = $(this).parents('.row-item').find('input[type="hidden"]');
		if (obj.length > 0) {
			obj.val($(this).data('id'));
			obj.parents('form').eq(0).submit();
		}
	});
	$('.form_datetime').datetimepicker({
		language: 'zh-CN',
		format: 'yyyy-mm-dd',
		weekStart: 1,
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
		clearBtn: 1,
		minView: 'month'
	});
	$('#progressing').show();
	progressing(20);
});