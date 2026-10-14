function post(url, param, callback) {
	if (typeof FormData !== 'undefined' && param instanceof FormData) {
		$.ajax({
			url: url,
			type: 'POST',
			data: param,
			processData: false,
			contentType: false,
			success: callback
		});
		return;
	}
	$.post(url, param, callback);
}
function get(url, param, callback) {
	$.get(url, param, callback);
}
function addRightTips(info, type, delay) {
	if (!info) {
		return false;
	}
	delay = delay || 3500;
	info = String(info).replace(/\n/g, '<br>');
	var obj = document.getElementById('rightTips');
	if (!obj) {
		obj = document.createElement('div');
		obj.id = 'rightTips';
		document.body.appendChild(obj);
	}
	obj.innerHTML = '<div class="info ' + type + '"><span>' + info + '</span><i class="fa fa-xmark close-tip"></i></div>';
	var closeBtn = obj.querySelector('.close-tip');
	if (closeBtn) {
		closeBtn.onclick = function() {
			obj.remove();
		};
	}
	clearTimeout(obj._timer);
	obj._timer = setTimeout(function() {
		if (obj && obj.parentNode) {
			obj.remove();
		}
	}, delay);
}
function successTips(msg) {
	addRightTips(msg, 'success');
}
function errorTips(msg) {
	addRightTips(msg, 'error');
}
function showTips(res) {
	if (res && res.code) {
		successTips(res.msg);
	} else if (res) {
		errorTips(res.msg);
	}
}
function confirm(msg, callback) {
	if ($('#confirm-pop').length === 0) {
		const html = '<div id="confirm-pop" style="display:none;">' +
			'<div class="mask"></div>' +
			'<div class="content">' +
				'<div class="message f16 tc" style="color:#0f172a;font-weight:500;padding:0.08rem 0;"></div>' +
				'<div class="mt20" style="display:flex;gap:0.12rem;justify-content:center;">' +
					'<button type="button" class="btn btn-default cancel" style="flex:1;">取消</button>' +
					'<button type="button" class="btn btn-primary confirm" style="flex:1;">确定</button>' +
				'</div>' +
			'</div>' +
		'</div>';
		$('body').append(html);
	}
	const obj = $('#confirm-pop');
	obj.find('.message').text(msg);
	obj.show();
	obj.off('click', '.btn.cancel, .mask').on('click', '.btn.cancel, .mask', function() {
		obj.hide();
	});
	obj.off('click', '.btn.confirm').on('click', '.btn.confirm', function() {
		callback($(this), obj);
	});
}
function progressing(val) {
	if (document.readyState === 'complete') {
		val = Math.min(100, val + 50);
	} else if (val < 80) {
		val += 20;
	}
	$('#progressing').show().stop(true, true).animate({'width': val + '%'}, 100, function() {
		if (val >= 100) {
			$('#progressing').fadeOut(150);
		} else {
			progressing(val);
		}
	});
	return true;
}
function S4() {
	return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
}
function guid() {
	return (S4() + S4() + '-' + S4() + '-' + S4() + '-' + S4() + '-' + S4() + S4() + S4());
}

const VERIFY = {
	mobile: function(mobile) {
		return this.check(mobile, /^1[3-9]\d{9}$/);
	},
	email: function(email) {
		return this.check(email, /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/);
	},
	password: function(password) {
		return this.check(password, /^[0-9A-Za-z]{6,}/);
	},
	code: function(code) {
		return this.check(code, /^[a-zA-Z0-9]{4,}/);
	},
	check: function(input, reg) {
		return reg.test(input ? String(input).trim() : '');
	}
};

(function($) {
	// 轻量级兼容 $.fn.button('loading' / 'reset')，无需引入 bootstrap.js
	$.fn.button = function(state) {
		return this.each(function() {
			const $el = $(this);
			if (state === 'loading') {
				if (!$el.data('orig-html')) {
					$el.data('orig-html', $el.html());
				}
				const loadingText = $el.attr('data-loading-text') || '<i class="fa fa-spinner fa-spin"></i> 处理中...';
				$el.prop('disabled', true).addClass('disabled').html(loadingText);
			} else if (state === 'reset') {
				const origHtml = $el.data('orig-html');
				if (origHtml !== undefined) {
					$el.html(origHtml);
				}
				$el.prop('disabled', false).removeClass('disabled');
			}
		});
	};

	$.fn.dealboxShow = function(title) {
		const obj = $(this);
		if (title) {
			obj.find('.dealbox-title, .title').text(title);
		}
		$('body').css({'overflow': 'hidden'});
		obj.removeClass('hidden hide').show();
		return obj;
	};

	$.fn.dealboxHide = function() {
		const obj = $(this);
		$('body').css({'overflow': 'auto'});
		obj.addClass('hidden').hide();
		return obj;
	};

	$.fn.modalShow = function(title) {
		return $(this).dealboxShow(title);
	};

	$.fn.modalHide = function() {
		return $(this).dealboxHide();
	};

	$.fn.switchBtn = function(status) {
		const obj = $(this);
		obj.data('status', status).attr('data-status', status);
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
		obj.find('[required="required"]').each(function() {
			if ($(this).val() === '') {
				errorTips($(this).prev().text() + '不能为空');
				status = false;
				return false;
			}
		});
		return status;
	};

	$.fn.imageUpload = function(cate, callback) {
		return this.each(function() {
			const $trigger = $(this);
			if ($trigger.data('upload-bound')) return;
			$trigger.data('upload-bound', true).css({ cursor: 'pointer' });
			const guidName = guid();
			const $fileInput = $('<input type="file" accept=".bmp,.jpg,.png,.jpeg,image/bmp,image/jpg,image/png,image/jpeg" style="display:none;" />').attr('name', guidName);
			$trigger.after($fileInput);

			$trigger.on('click', function(e) {
				e.stopPropagation();
				$fileInput[0].click();
			});

			$fileInput.on('change', function() {
				const files = $(this).prop('files');
				if (!files || !files[0]) return;
				const formData = new FormData();
				formData.append('file', files[0]);
				formData.append('cate', cate);

				const origHtml = $trigger.html();
				$trigger.addClass('uploading').html('<i class="fa fa-spinner fa-spin"></i>');

				post('/api/upload', formData, function(res) {
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch (err) {}
					}
					$trigger.removeClass('uploading');
					if (res && res.code == 1 && res.data) {
						if ($trigger.is('img')) {
							$trigger.attr('src', res.data.url);
						} else {
							$trigger.html('<img src="' + res.data.url + '" alt="icon">');
						}
						if (typeof callback === 'function') {
							callback(res.data, $trigger);
						}
					} else {
						$trigger.html(origHtml);
						showTips(res || { code: 0, msg: '上传失败' });
					}
					$fileInput.val('');
				});
			});
		});
	};
})(jQuery);

$(function() {
	progressing(20);
	$('body').on('click', '#dealbox .close, #dealbox .mask, .dealbox .close, .dealbox .mask', function() {
		$(this).closest('#dealbox, .dealbox').dealboxHide();
	});
});
