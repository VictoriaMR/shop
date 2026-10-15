$(function(){
    $('select[name="site"]').on('change', function(){
        var siteVal = $(this).val();
        PRODUCT.cate_init(siteVal, 0);
    });
    if (typeof site !== 'undefined') {
        PRODUCT.cate_init(site, cate);
    }
});
var PRODUCT = {
    cate_init: function(site, cate) {
        var list = cate_list[site];
        if (!list) {
            return false;
        }
        var html = '<option value="0">全部产品分类</option>';
        for (var i = 0; i < list.length; i++) {
            var paddingStr = '';
            var disable = false;
            if (list[i + 1] && list[i + 1].level > list[i].level) {
                disable = true;
            }
            for (var p = 0; p < list[i].level; p++) {
                paddingStr += '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            html += '<option value="' + list[i].cate_id + '" ' + (disable ? 'disabled="disabled"' : '') + ' ' + (cate && cate == list[i].cate_id ? 'selected' : '') + '>' + paddingStr + list[i].name + '</option>';
        }
        $('select[name="cate"]').html(html);
    }
};