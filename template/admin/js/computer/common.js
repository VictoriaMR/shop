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
function isScroll() {
    return document.body.scrollHeight > (window.innerHeight || document.documentElement.clientHeight);
}
function progressing(val) {
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
$(function(){
	$.fn.center = function() {
		var obj = $(this);
	    var w = ($(window).innerWidth() - obj.actual('width'))/2;
	    var h = ($(window).innerHeight() - obj.actual('height'))/2 - 50;
	    obj.css('top', h+'px');
	    obj.css('left', w+'px');
		return $(this);
	};
	$.fn.modalShow = function(title) {
		const obj = $(this);
		if (title) {
			obj.find('.title').text(title);
		}
		if (obj.data('mask')) {
			$('body').css({'overflow': 'hidden'});
			if (isScroll()) {
				$('body').css({'padding-right': '6.5px'});
			}
		}
		obj.center();
		obj.show();
		return obj;
	};
	$.fn.modalHide = function(width, height) {
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
				bigImageObj.center().dealboxShow();
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
            	post(URI+'api/upload', data, function(res){
            		if (res.code == 200) {
						if (thisobj.get(0).tagName == 'IMG') {
							thisobj.removeClass('loading').attr('src', res.data.url);
						} else {
							thisobj.removeClass('loading').find('img').attr('src', res.data.url);
						}
						if (callback) {
							callback(res.data, thisobj);
						}
					} else {
						showTips(res);
						thisobj.removeClass('loading').attr('src', thisobj.data('src'));
					}
            	});
			});
		});
	};
	$.fn.autoHeight = function(){
		$(this).each(function(){
			$(this).height(this.scrollHeight - 12);
		});
	};
}(jQuery));
$(function(){
	// 弹窗关闭
	$('.s-modal .glyphicon-remove').on('click', function(){
		$(this).parents('.s-modal').modalHide();
	});
	// 日期初始化
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
	// 初始加载进度条
	$('#progressing').show();
	progressing(20);
});