/* common */
//认证函数
const VERIFY = {
    mobile: function (mobile) {
        return this.check(mobile, /^1[3456789]\d{9}$/);
    },
    email: function (email) {
        return this.check(email, /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/);
    },
    password: function (password) {
        return this.check(password, /^[0-9A-Za-z]{6,}/);
    },
    code: function(code) {
        return this.check(code, /^[a-zA-Z0-9]{4,}/);
    },
    check: function(input, reg) {
        return reg.test(input.trim());
    }
};
// 工具
const Tool = {
    loading: function(obj, type) {
        this.obj = obj;
        if (typeof type == 'undefined') {
            type = 1;
        }
        obj.setAttribute('data-text', obj.innerHTML);
        obj.innerHTML = '<div class="dots type-'+type+'"><div></div><div></div><div></div></div>';
    },
    hide: function() {
        console.log(this.obj, 'hide')
       this.obj.innerHTML = this.obj.getAttribute('data-text'); 
    },
    tips: function(text, time) {
        if(typeof time=='undefined'){
            time=2;
        }
        var obj = document.getElementById('pop-tips');
        if (!obj) {
            obj = document.createElement('div');
            obj.id = 'pop-tips';
            document.querySelector('body').appendChild(obj);
        }
        obj.innerHTML = text;
        clearTimeout(this.setTimeoutId);
        this.setTimeoutId = setTimeout(function(){
            obj.remove();
        }, time*1000);
    }
};
/* common */
// 页面ready
const $pageReady = (function() {
    var funcs = [];
    var ready = false;
    function handler(e) {
        if(ready || (e.type === 'onreadystatechange' && document.readyState !== 'complete')) {
            return;
        }
        for(var i=0; i<funcs.length; i++) {
            funcs[i].call(e);
        }
        ready = true;
        funcs = null;
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
})();
// 点击事件绑定
const $click = (function(){
    return function $click(name, fn) {
        var list = document.querySelectorAll(name);
        for (var i=0; i<list.length; i++) {
            if(list[i].addEventListener) {
                list[i].addEventListener('click', fn, false);
            }else if(list[i].attachEvent) {
                list[i].attachEvent('click', fn);
            }
        }
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
