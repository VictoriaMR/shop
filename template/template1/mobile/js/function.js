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
    var pObj = obj.parentNode.parentNode;
    var obj = pObj.querySelector('.error-tips');
    if (obj) {
        obj.remove();
    }
    pObj.classList.remove('success');
    pObj.classList.add('error');
    let p = document.createElement('p');
    p.className = 'error-tips';
    p.innerHTML = text
    pObj.appendChild(p);
    addShake(pObj.querySelector('.title'));
}
// 标签抖动
function addShake(obj) {
    obj.classList.add('shake');
    navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
    if (navigator.vibrate) {
        navigator.vibrate([200]);
    }
    setTimeout(function(){
        obj.classList.remove('shake');
    }, 600);
}
// 弹窗展示
function showModal(name) {
    $(name).addClass('open');
    modal_list.push(name);
    if ($('#modal-mask').length == 0) {
        $('body').append('<div id="modal-mask" class="mask"></div>');
    }
}
// 弹窗关闭
function hideModal() {
    var len = modal_list.length;
    if (len > 0) {
        $(modal_list[len-1]).removeClass('open');
        modal_list.splice(len-1, 1);
        if (len <= 1) {
            $('#modal-mask').fadeOut(200, function(){
                $(this).remove();
            });
        }
    }
}