// 页面ready
const $ready = (function() {
    var funcs = new Array();
    var ready = false;
    function handler(e) {
        ready = true;
        for(var i=0; i<funcs.length; i++) {
            funcs[i].call(e);
        }
    }
    if(document.addEventListener) {
        window.addEventListener('load', handler, false);
    }else if(document.attachEvent) {
        window.attachEvent('onload', handler);
    } else {
        fn.call(e);
    }
    return function dofunc(fn) {
        ready ? fn.call(e) : funcs.push(fn);
    }
})();
function bindHandle(type, name, fn) {
    var list = document.querySelectorAll(name);
    console.log(name, list)
    for (var i=0; i<list.length; i++) {
        if(list[i].addEventListener) {
            list[i].addEventListener(type, fn, false);
        }else if(list[i].attachEvent) {
            list[i].attachEvent(type, fn);
        }
    }
}
// 点击事件绑定
const $click = (function(){
    return function $click(name, fn) {
        bindHandle('click', name, fn);
    }
})();
// 滚动事件
const $scroll = (function(){
    var funcs = [];
    window.addEventListener('scroll', function(e){
        for (let i=0; i<funcs.length; i++) {
            funcs[i].call(e);
        }
    });
    return function $scroll(fn) {
        funcs.push(fn);
    }
})();
// 输入事件
const $change = (function(){
    return function $change(name, fn) {
        var list = document.querySelectorAll(name);
        for (var i=0; i<list.length; i++) {
            if(list[i].addEventListener) {
                list[i].addEventListener('input', fn, false);
            }else if(list[i].attachEvent) {
                list[i].attachEvent('input', fn);
            }
        }
    }
})();
// 失去焦点事件
const $blur = (function(){
    return function $blur(name, fn) {
        var list = document.querySelectorAll(name);
        for (var i=0; i<list.length; i++) {
            if(list[i].addEventListener) {
                list[i].addEventListener('blur', fn, false);
            }else if(list[i].attachEvent) {
                list[i].attachEvent('blur', fn);
            }
        }
    }
})();
// 失去焦点事件
const $focus = (function(){
    return function $focus(name, fn) {
        var list = document.querySelectorAll(name);
        for (var i=0; i<list.length; i++) {
            if(list[i].addEventListener) {
                list[i].addEventListener('focus', fn, false);
            }else if(list[i].attachEvent) {
                list[i].attachEvent('focus', fn);
            }
        }
    }
})();
// 查找父类
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
// 请求
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