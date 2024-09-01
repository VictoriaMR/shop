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
