$(function() {
	PRODUCT.init();
});

var PRODUCT = {
	init: function() {
		var _this = this;
		$('select[name="site"]').on('change', function() {
			var selectedSite = $(this).val();
			_this.cate_init(selectedSite, -1);
		});
		if (typeof site !== 'undefined' && site >= 0 && typeof cate_list !== 'undefined' && cate_list[site]) {
			_this.cate_init(site, typeof cate !== 'undefined' ? cate : -1);
		}
	},
	cate_init: function(siteVal, cateVal) {
		if (typeof cate_list === 'undefined' || !cate_list) {
			return false;
		}
		var list = cate_list[siteVal] || cate_list[-1];
		if (!list || !list.length) {
			return false;
		}
		var html = '<option value="-1">全部分类</option>';
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
