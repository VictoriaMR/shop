$(function() {
	PRODUCT.init();
});

var PRODUCT = {
	init: function() {
		if (typeof cate_list !== 'undefined' && cate_list) {
			this.cate_init(typeof cate !== 'undefined' ? cate : 0);
		}
	},
	cate_init: function(cateVal) {
		if (typeof cate_list === 'undefined' || !cate_list) {
			return false;
		}
		var list = Array.isArray(cate_list) ? cate_list : (cate_list[-1] || []);
		if (!list || !list.length) {
			return false;
		}
		var html = '<option value="0">全部分类</option>';
		for (var i = 0; i < list.length; i++) {
			var item = list[i];
			var lev = parseInt(item.level || 0, 10);
			var paddingStr = '';
			for (var p = 0; p < lev; p++) {
				paddingStr += '　';
			}
			var disable = false;
			if (list[i + 1] && parseInt(list[i + 1].parent_id, 10) === parseInt(item.cate_id, 10)) {
				disable = true;
			}
			var selected = (cateVal && parseInt(cateVal, 10) === parseInt(item.cate_id, 10)) ? 'selected' : '';
			html += '<option value="' + item.cate_id + '" ' + (disable ? 'disabled="disabled"' : '') + ' ' + selected + '>' + paddingStr + item.name + '</option>';
		}
		$('select[name="cate"]').html(html);
	}
};
