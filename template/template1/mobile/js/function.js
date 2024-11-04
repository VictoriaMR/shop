// 多语言翻译接口
function distT(text, replace) {
	if (_language_translate && _language_translate[text]) {
		if (replace) {
			return _language_translate[text].replace(replace);
		}
		return _language_translate[text];
	}
	return text;
}
// input组件错误
function inputError(obj, text) {
	var pObj = obj.parents('.input-group');
	pObj.find('.error-tips').remove();
	pObj.removeClass('success').addClass('error');
	pObj.append('<p class="error-tips">'+text+'</p>');
	addShake(pObj.find('.input-title'));
}
// 标签抖动
function addShake(obj) {
	obj.addClass('shake');
	navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
	if (navigator.vibrate) {
		navigator.vibrate([200]);
	}
	setTimeout(function(){
		obj.removeClass('shake');
	}, 600);
}
// 弹窗展示
function showModal(name) {
	var index = $('.modal.open').length * 2 + 101;
	var obj = $(name);
	obj.addClass('open').css('z-index', index+1);
	obj.after('<div class="mask '+obj.data('type')+'" style="z-index:'+index+'"></div>');
}
// 弹窗关闭
function hideModal1(e) {
	var modal = $(this).parents('.modal');
	modal.removeClass('open');
	modal.next('.mask').fadeOut(200, function(){
		$(this).remove();
	});
}
// 弹窗关闭
function hideModal2(e) {
	$(this).prev('.modal').removeClass('open');
	$(this).fadeOut(200, function(){
		$(this).remove();
	});
}
function loading(obj) {
	obj.attr('disabled', 'disabled');
	obj.data('loading_text', obj.text());
	obj.html('<div class="loader"></div>');
}
function loaded(obj) {
	obj.prop('disabled', false);
	obj.html(obj.data('loading_text'));
}