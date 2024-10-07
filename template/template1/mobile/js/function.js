/* 页面ready */
const $pageReady=function(){
    var funcs = [];
    var ready = false;
    function handler(e) {
        if(!ready) {
            for(var i=0; i<funcs.length; i++) {
                funcs[i].call(e);
            }
            ready = true;
            funcs = null;
        }
    }
    if(document.addEventListener) {
        document.addEventListener('DOMContentLoaded', handler, false);
        document.addEventListener('readystatechange', handler, false);
        window.addEventListener('load', handler, false);
    }else if(document.attachEvent) {
        document.attachEvent('onreadystatechange', handler);
        window.attachEvent('onload', handler);
    }
    return function pageReady(fn) {
        if (ready) {
            fn.call(e);
        } else {
            funcs.push(fn);
        }
    }
}
/* 绑定事件调用 */
function $bindAction(type, name, fn){
    var list = document.querySelectorAll(name);
    for (var i=0; i<list.length; i++) {
        if(list[i].addEventListener) {
            list[i].addEventListener(type, fn, false);
        }else if(list[i].attachEvent) {
            list[i].attachEvent(type, fn);
        }
    }
}
/* 点击事件绑定 */
function $click(name, fn) {
    $bindAction('click', name, fn);
}
/* 滚动事件 */
function $scroll(){
    $bindAction('scroll', name, fn);
}
/* 输入事件 */
function $change(name, fn) {
    $bindAction('input', name, fn);
}
/* 失去焦点事件 */
function $blur(name, fn) {
    $bindAction('blur', name, fn);
}
/* 失去焦点事件 */
function $focus(name, fn) {
    $bindAction('focus', name, fn);
}
/* 查找父类 */
function parents(obj, className) {
    let node = obj;
    let status = true;
    while (status) {
        node = node.parentNode;
        if (!node) {
            node = obj;
            status = false;
        } else {
            if (node.classList.length > 0 && node.classList.contains(className)) {
                status = false;
            }
        }
    }
    return node;
}
/* 请求 */
function post(url, param, callback) {
    let formData = new FormData();
    for (let i in param) {
        formData.append(i, param[i]);
    }
    let init = {};
    init.method = 'POST';
    init.body = formData;
    fetch(url, init).then(response => response.json()).then(data => {
        callback(data);
    }).catch((error) => {
        callback({code: 500, msg: error.message});
    });
}
/* 多语言翻译接口 */
function distT(text, replace) {
    if (_language_translate && _language_translate[text]) {
        if (replace) {
            return _language_translate[text].replace(replace);
        }
        return _language_translate[text];
    }
    return text;
}
/* input组件错误 */
function inputError(obj, text) {
    var pObj = obj.parentNode.parentNode;
    var obj = pObj.querySelector('.error-tips');
    if (obj) {
        obj.remove();
    }
    pObj.classList.remove('success');
    pObj.classList.add('error');
    var p = document.createElement('p');
    p.className = 'error-tips';
    p.innerHTML = text
    pObj.appendChild(p);
    addShake(pObj.querySelector('.title'));
}