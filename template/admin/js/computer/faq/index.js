$(function(){
    FAQGROUP.init();
});
var FAQGROUP = {
    init: function() {
        var _this = this;
        $('#faq-page .btn.add-btn').on('click', function(){
            _this.initInfo('新增分组');
        });
        //保存按钮
        $('#dealbox-info .btn.save-btn').on('click', function(){
            var obj = $(this);
            post(URI+'faq', obj.parents('form').serializeArray(), function(){
                window.location.reload();
            });
        });
        //编辑按钮
        $('#faq-page .btn.modify').on('click', function(){
            const obj = $(this);
            obj.button('loading');
            const id = obj.parents('tr').data('id');
            $.post(URI+'faq', {opn: 'getGroupInfo', group_id: id}, function(res){
                obj.button('reset');
                if (res.code === '200') {
                    _this.initInfo('编辑分组', res.data);
                } else {
                    errorTips(res.message);
                }
            });
        });
        //状态开关
        $('.switch_botton').on('click', function(){
            const _thisobj = $(this);
            const id = _thisobj.parents('tr').data('id');
            const status = _thisobj.data('status') == '0' ? 1 : 0;
            post(URI+'faq', {opn: 'modifyGroupStatus', group_id: id, status: status}, function(){
                _thisobj.switchBtn(status);
            });
        });
        //多语言配置
        $('.glyphicon-globe').on('click', function(event){
            event.stopPropagation();
            const _thisobj = $(this);
            const id = _thisobj.parents('tr').data('id');
            post(URI+'faq', {opn: 'getGroupLanguage', group_id: id}, function(data){
                const obj = $('#dealbox-language');
                obj.find('input[name="group_id"]').val(id);
                var name = _thisobj.next().text();
                obj.find('input[name="name"]').val(name);
                obj.find('.dealbox-title').text(name);
                obj.find('table input').val('');
                let html = '<tr>\
                                <th style="width:88px">语言名称</th>\
                                <th>\
                                    <span>文本</span>\
                                    <span title="智能翻译" class="glyphicon glyphicon-transfer"></span>\
                                </th>\
                            </tr>';
                for (const i in data) {
                    html += '<tr>\
                                <th>\
                                    <span>'+data[i].language_name+'</span>\
                                </th>\
                                <td class="p0">';
                    html += '<textarea rows="3" type="text" name="language['+data[i].lan_id+']" data-tr_code="'+data[i].tr_code+'" class="form-control transfer" autocomplete="off">'+data[i].name+'</textarea>';
                    html += '</td>\
                            </tr>';
                }
                obj.find('table tbody').html(html);
                obj.dealboxShow();
            });
        });
        //智能翻译
        $('#dealbox-language').on('click', '.glyphicon-transfer', function(){
            let name = $('#dealbox-language input[name="name"]').val();
            if (!name) {
                var obj = $('#dealbox-language td').eq(0);
                if (obj.find('input').length > 0) {
                    name = obj.find('input').val();
                } else {
                    name = obj.find('textarea').val();
                }
            }
            const thisobj = $(this);
            var obj = thisobj.parents('table').find('.transfer');
            let len = obj.length;
            thisobj.button('loading');
            obj.each(function(){
                const value = $(this).val();
                if (value === '') {
                    const _thisobj = $(this);
                    const tr_code = _thisobj.data('tr_code');
                    $.post(URI+'faq', {opn:'transfer', tr_code:tr_code, name:name, from_code: 'en'}, function(res){
                        len = len - 1;
                        if (res.code === '200') {
                            _thisobj.val(res.data);
                        } else {
                            errorTips(res.msg);
                        }
                        if (len === 0) {
                            thisobj.button('reset');
                        }
                    });
                } else {
                    len = len - 1;
                    if (len === 0) {
                        thisobj.button('reset');
                    }
                }
            });
        });
        //保存语言
        $('#dealbox-language .save-btn').on('click', function(){
            const obj = $(this);
            obj.button('loading');
            post(URI+'faq', $('#dealbox-language form').serializeArray(), function(){
                obj.button('reset');
                window.location.reload();
            });
            return false;
        });
    },
    initInfo: function(title, data) {
        if (!data) {
            data = {
                group_id: 0,
                name: '',
                status: 0,
            };
        }
        const obj = $('#dealbox-info');
        for (const i in data) {
            obj.find('[name="'+i+'"]').val(data[i]);
        }
        obj.dealboxShow(title);
        return true;
    }
};