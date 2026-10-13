$(function(){
    CURRENCY.init();
});

const CURRENCY = {
    init: function() {
        const _this = this;

        // 打开新增弹窗
        $('#add-data-btn').on('click', function(){
            _this.dealboxData({});
        });

        // 在线更新汇率
        $('#update-rate-btn').on('click', function(){
            const btn = $(this);
            btn.button('loading');
            post('', {opn: 'updateCurrencyRate'}, function(res){
                btn.button('reset');
                showTips(res);
                if (res && (res.code == 1 || res.code == 200)) {
                    setTimeout(function(){
                        window.location.reload();
                    }, 800);
                }
            });
        });

        // 编辑货币
        $('#data-list .modify').on('click', function(){
            const tr = $(this).closest('tr');
            const id = tr.data('id');
            let rowData = tr.data('info') || {};
            if (typeof rowData === 'string') {
                try { rowData = JSON.parse(rowData); } catch(e){}
            }

            // 优先直接使用行内已有的数据回显，实现即时弹出无感知延迟
            if (rowData && rowData.code) {
                _this.dealboxData(rowData);
            }

            // 异步从服务端获取最新详细信息进行确认
            post('', {opn: 'getCurrencyInfo', id: id}, function(res){
                if (typeof res === 'string') {
                    try { res = JSON.parse(res); } catch(e){}
                }
                if (res && (res.code == 1 || res.code == 200)) {
                    _this.dealboxData(res.data);
                } else if (!rowData || !rowData.code) {
                    showTips(res || {code: 0, msg: '获取货币信息失败'});
                }
            });
        });

        // 保存货币数据
        $('#dealbox .save').on('click', function(){
            const form = $('#dealbox form');
            if (!form.formFilter()) {
                return false;
            }

            const codeInput = form.find('input[name="code"]');
            const code = $.trim(codeInput.val()).toUpperCase();
            codeInput.val(code);

            const name = $.trim(form.find('input[name="name"]').val());
            const symbol = $.trim(form.find('input[name="symbol"]').val());
            const rate = $.trim(form.find('input[name="rate"]').val());
            const id = form.find('input[name="id"]').val();

            if (!id && !/^[A-Z]{3}$/.test(code)) {
                errorTips('货币代码必须为3位大写英文字母(如: USD)');
                codeInput.focus();
                return false;
            }

            if (name === '') {
                errorTips('货币名称不能为空');
                return false;
            }

            if (symbol === '') {
                errorTips('货币符号不能为空');
                return false;
            }

            if (rate === '' || isNaN(rate) || parseFloat(rate) <= 0) {
                errorTips('汇率必须为大于0的有效数值');
                return false;
            }

            const btn = $(this);
            btn.button('loading');
            post('', form.serializeArray(), function(res){
                btn.button('reset');
                if (typeof res === 'string') {
                    try { res = JSON.parse(res); } catch(e){}
                }
                showTips(res);
                if (res && (res.code == 1 || res.code == 200)) {
                    $('#dealbox').dealboxHide();
                    setTimeout(function(){
                        window.location.reload();
                    }, 600);
                }
            });
        });

        // 删除货币
        $('#data-list .delete').on('click', function(){
            const tr = $(this).closest('tr');
            const id = tr.data('id');
            if (!id || id === 'CNY') {
                errorTips('基准货币不允许删除');
                return false;
            }

            confirm('确定要删除货币【' + id + '】吗？删除后不可恢复', function(btn, modal){
                post('', {opn: 'deleteCurrencyInfo', id: id}, function(res){
                    modal.hide();
                    if (typeof res === 'string') {
                        try { res = JSON.parse(res); } catch(e){}
                    }
                    showTips(res);
                    if (res && (res.code == 1 || res.code == 200)) {
                        tr.fadeOut(300, function(){
                            tr.remove();
                        });
                    }
                });
            });
        });

        // 弹窗关闭
        $('#dealbox .close, #dealbox .mask').on('click', function(){
            $('#dealbox').dealboxHide();
        });
    },

    dealboxData: function(data) {
        const obj = $('#dealbox');
        obj.find('input:not(.no_replace)').val('');

        if (data && data.code) {
            obj.find('.dealbox-title').text('编辑货币 (' + data.code + ')');
            obj.find('input[name="id"]').val(data.code);
            obj.find('input[name="code"]').val(data.code).prop('readonly', true);
            obj.find('input[name="name"]').val(data.name || '');
            obj.find('input[name="symbol"]').val(data.symbol || '');
            obj.find('input[name="rate"]').val(data.rate || '');
        } else {
            obj.find('.dealbox-title').text('新增货币');
            obj.find('input[name="id"]').val('');
            obj.find('input[name="code"]').val('').prop('readonly', false);
            obj.find('input[name="name"]').val('');
            obj.find('input[name="symbol"]').val('');
            obj.find('input[name="rate"]').val('');
        }

        obj.dealboxShow();
    }
};