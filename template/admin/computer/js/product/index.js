$(function(){
    $('select[name="site"]').on('change', function(){
        var site = $(this).val();
        PRODUCT.cate_init(site);
    });
    if (site) {
        PRODUCT.cate_init(site, cate);
    }
});
var PRODUCT = {
    cate_init: function(site, cate) {
        var list = cate_list[site];
        if(!list) {
            return false;
        }
        var html = '<option value="-1">请选择分类</option>';
        for (var i = 0; i < list.length; i++) {
            if (list[i].level > 0) {
                var paddingStr = '';
                var disable = false;
                if (list[i+1] && list[i+1].parent_id == list[i].cate_id) {
                    disable = true;
                }
                for (var p=1; p<list[i].level; p++) {
                    paddingStr += '&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                html += '<option value="'+list[i].cate_id+'" '+(disable?'disabled="disabled"':'')+' '+(cate&&cate==list[i]['cate_id']?'selected':'')+'>'+paddingStr+list[i].name+'</option>';
            }
        }
        $('select[name="cate"]').html(html);
    }
};