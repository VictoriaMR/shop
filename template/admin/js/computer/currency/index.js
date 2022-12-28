$(function(){
    CURRENCY.init();
});
const CURRENCY = {
    init: function() {
        const _this = this;
        //新增修改
        $('.btn.modify').on('click', function(){
            var btnobj = $(this);
            var id = btnobj.parents('.item').data('id');
            btnobj.button('loading');
            post(URI+'desc', {opn: 'getDescInfo', id: id}, function(data){
                if (res.code == 200) {
                    _this.initData(res.data);
                } else {
                    errorTips(res.msg);
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
        if (data) {
            obj.find('input[name="id"]').val(data.descn_id);
            obj.find('input[name="name"]').val(data.name);
        } else {
            obj.find('input[name="id"]').val(0);
            obj.find('input[name="name"]').val('');
        }
        obj.dealboxShow();
        return true;
    },
};