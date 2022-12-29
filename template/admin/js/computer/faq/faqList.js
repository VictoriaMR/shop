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
            obj.button('loading');
            post(URI+'faq/faqList', obj.parents('form').serializeArray(), function(res){
                showTips(res);
                if (res.code == 200) {
                    window.location.reload();
                } else {
                    obj.button('reset');
                }
            });
        });
        //编辑按钮
        $('#faq-page .btn.modify').on('click', function(){
            const obj = $(this);
            obj.button('loading');
            const id = obj.parents('tr').data('id');
            post(URI+'faq/faqList', {opn: 'getFaqInfo', faq_id: id}, function(res){
                if (res.code === 200) {
                    _this.initInfo('编辑分组', res.data);
                } else {
                    showTips(res);
                }
                obj.button('reset');
            });
        });
        //状态开关
        $('.switch_botton').on('click', function(){
            const _thisobj = $(this);
            const id = _thisobj.parents('tr').data('id');
            const status = _thisobj.data('status') == '0' ? 1 : 0;
            post(URI+'faq/faqList', {opn: 'modifyFaqStatus', faq_id: id, status: status}, function(res){
                showTips(res);
                if (res.code == 200) {
                    _thisobj.switchBtn(status);
                }
            });
        });
        //多语言配置
        $('.glyphicon-globe').on('click', function(event){
            event.stopPropagation();
            const _thisobj = $(this);
            const id = _thisobj.parents('tr').data('id');
            post(URI+'faq/faqList', {opn: 'getFaqLanguage', faq_id: id}, function(res){
                if (res.code == 200) {
                    var data = res.data;
                    const obj = $('#dealbox-language');
                    obj.find('input[name="faq_id"]').val(id);
                    var name = _thisobj.next().text();
                    obj.find('input[name="name"]').val(name);
                    obj.find('.dealbox-title').text(name);
                    obj.find('table input').val('');
                    let html = '';
                    for (const i in data) {
                        html += '<tr data-id="'+data[i].lan_id+'">\
                                    <td>\
                                        <span>'+data[i].language_name+'</span>\
                                    </td>\
                                    <td>'+(data[i].edit?'<span class="green">已配置</span>':'<span class="red">未配置</span>')+'</td>\
                                    <td>\
                                        <button class="btn btn-primary btn-xs edit" type="button"><i class="glyphicon glyphicon-edit"></i> 配置</button>\
                                    </td>\
                                </tr>';
                    }
                    obj.find('table tbody').html(html);
                    obj.dealboxShow();
                } else {
                    showTips(res);
                }
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
                    post(URI+'faq/faqList', {opn:'transfer', tr_code:tr_code, name:name, from_code: 'en'}, function(res){
                        len = len - 1;
                        if (res.code === 200) {
                            _thisobj.val(res.data);
                        } else {
                            showTips(res);
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
        $('#dealbox-language').on('click', '.btn.edit', function(){
            const _thisobj = $(this);
            _thisobj.button('loading');
            var id = _thisobj.parents('form').find('[name="faq_id"]').val();
            var lan_id = _thisobj.parents('tr').data('id');
            const tr_code = _thisobj.data('tr_code');
            post(URI+'faq/faqList', {opn: 'getFaqLanguage', faq_id: id, lan_id: lan_id}, function(res){
                if (res.code == 200) {
                    _this.initFaqLanguage(res.data);
                } else {
                    showTips(res);
                }
                _thisobj.button('reset');
            });
        });
        //保存语言
        $('#dealbox-faq-language .save-btn').on('click', function(){
            const _thisobj = $(this);
            _thisobj.button('loading');
            editor.sync();
            post(URI+'faq/faqList', $('#dealbox-faq-language form').serializeArray(), function(res){
                showTips(res);
                if (res.code == 200) {
                    var lanId = _thisobj.parent().find('[name="lan_id"]').val();
                    $('#dealbox-language tr[data-id="'+lanId+'"]').find('span.red').parent().html('<span class="green">已配置</span>');
                    $('#dealbox-faq-language').hide();
                }
                _thisobj.button('reset');
            });
            return false;
        });
        KindEditor.ready(function(K) {
            window.editor = K.create('#faq_editor',{
                height:'650px',
                uploadJson : URL+'faq/faqList?opn=upload',
                allowFileManager : false
            });
        });
    },
    initInfo: function(title, data) {
        if (!data) {
            data = {
                faq_id: 0,
                group_id: 0,
                title: '',
                status: 0,
                visit_total: 0,
            };
        }
        const obj = $('#dealbox-info');
        for (const i in data) {
            obj.find('[name="'+i+'"]').val(data[i]);
        }
        obj.dealboxShow(title);
        return true;
    },
    initFaqLanguage: function(data) {
        var obj = $('#dealbox-faq-language');
        for (var i in data) {
            obj.find('[name="'+i+'"]').val(data[i]);
        }
        editor.html(data.content);
        obj.dealboxShow('帮助文章编辑器('+data.language_name+')');
    },
};