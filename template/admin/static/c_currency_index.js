/* currency_index */
$(function(){
    CURRENCY.init();
});
const CURRENCY = {
    init: function() {
        const _this = this;
        $('.btn.add').on('click', function(){
            _this.initData();
        });
        $('.btn.update').on('click', function(){
            var obj = $(this);
            obj.button('loading');
            post(URI+'currency', {opn: 'updateCurrencyRate'}, function(res){
                showTips(res);
                if (res.code == 200) {
                    window.location.reload();
                } else {
                    btnobj.button('reset');
                }
            });
        });
        //新增修改
        $('.btn.modify').on('click', function(){
            var btnobj = $(this);
            var id = btnobj.parents('.item').data('id');
            btnobj.button('loading');
            post(URI+'currency', {opn: 'getCurrencyInfo', id: id}, function(res){
                if (res.code == 200) {
                    _this.initData(res.data);
                } else {
                    showTips(res);
                }
                btnobj.button('reset');
            });
        });
        //保存数据
        $('#dealbox .save-btn').on('click', function(){
            var name = $('#dealbox form input[name="name"]').val();
            if (name == '') {
                errorTips('名称不能为空');
                return false;
            }
            var obj = $(this);
            obj.button('loading');
            post(URI+'desc', $('#dealbox form').serializeArray(), function(res){
                showTips(res);
                if (res.code == 200) {
                    window.location.reload();
                } else {
                    obj.button('reset');
                }
            });
        });
        //删除
        $('.btn.delete').on('click', function(){
            var btnobj = $(this);
            var id = btnobj.parents('.item').data('id');
            confirm('确定要删除吗?', function(obj){
                obj.button('loading');
                post(URI+'desc', {opn: 'deleteDescInfo', id: id}, function(res){
                    showTips(res);
                    if (res.code == 200) {
                        window.location.reload();
                    } else {
                        obj.button('reset');
                    }
                });
            });
        });
    },
    initData: function(data) {
        var obj = $('#dealbox');
        if (!data) {
            data = {
                id: '',
                code: '',
                name: '',
                rate: '',
                symbol: '',
            };
        }
        if (data.code) {
            data.id = data.code;
        }
        for (var i in data) {
            obj.find('input[name="'+i+'"]').val(data[i]);
        }
        obj.find('input[name="code"]').prop('readonly', data.code ? true : false);
        obj.dealboxShow();
        return true;
    },
};