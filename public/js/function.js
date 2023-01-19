function post(url, data, callbck) {
    if (data.constructor == Array) {
        data.push({name:'is_ajax',value:1});
    } else {
        data.is_ajax = 1;
    }
    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        dataType: 'json',
        success: function(res){
            if (typeof callbck == 'function') {
                callbck(res);
            }
        },
        error: function(xhr,status,error) {
            if (typeof callbck == 'function') {
                callbck({code:500, msg:error});
            }
        }
    });
}
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