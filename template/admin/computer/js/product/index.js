$(function(){
	if (typeof cate_list !== 'undefined') {
		PRODUCT.cate_init(cate);
	}
	PRODUCT.init();
});

var PRODUCT = {
	init: function() {
		// 恢复视图模式偏好 (grid | table)
		var savedMode = localStorage.getItem('admin_spu_view_mode') || 'grid';
		this.switchView(savedMode);

		var _this = this;
		$('.view-mode-btn').on('click', function() {
			var mode = $(this).data('mode');
			_this.switchView(mode);
			localStorage.setItem('admin_spu_view_mode', mode);
		});

		// 快速切换上下架状态 (1=销售中, 2=已下架 / 0=未上架)
		$('#spu-index-page').on('click', '.j-quick-status', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var btn = $(this);
			var spuId = btn.data('id');
			var curStatus = parseInt(btn.attr('data-status'), 10);
			var nextStatus = curStatus === 1 ? 2 : 1;
			var actionText = nextStatus === 1 ? '上架销售' : '下架';

			post('/product/index', {
				opn: 'editInfo',
				spu_id: spuId,
				status: nextStatus
			}, function(res) {
				showTips(res);
				if (res.code == 1) {
					window.location.reload();
				}
			});
		});

		// 列表视图开关切换 (免运费 / 热门)
		$('#spu-index-page').on('click', '.j-switch-field', function() {
			var sw = $(this);
			var spuId = sw.closest('tr').data('id');
			var field = sw.data('field');
			var nextVal = parseInt(sw.attr('data-status'), 10) === 1 ? 0 : 1;
			var payload = {
				opn: 'editInfo',
				spu_id: spuId
			};
			payload[field] = nextVal;
			post('/product/index', payload, function(res) {
				showTips(res);
				if (res.code == 1) {
					sw.switchBtn(nextVal);
				}
			});
		});
	},

	switchView: function(mode) {
		$('.view-mode-btn').removeClass('active');
		$('.view-mode-btn[data-mode="' + mode + '"]').addClass('active');
		if (mode === 'table') {
			$('#spu-grid-view').addClass('hidden');
			$('#spu-table-view').removeClass('hidden');
		} else {
			$('#spu-table-view').addClass('hidden');
			$('#spu-grid-view').removeClass('hidden');
		}
		if (typeof LAZYLOAD !== 'undefined') {
			LAZYLOAD.init();
		}
	},

	cate_init: function(selectedCate) {
		var list = Array.isArray(cate_list) ? cate_list : (cate_list[-1] || []);
		if (!list || !list.length) {
			return false;
		}
		var html = '<option value="0">全部产品分类</option>';
		for (var i = 0; i < list.length; i++) {
			var paddingStr = '';
			for (var p = 0; p < list[i].level; p++) {
				paddingStr += '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			var isSelected = (selectedCate && parseInt(selectedCate, 10) === parseInt(list[i].cate_id, 10)) ? 'selected' : '';
			html += '<option value="' + list[i].cate_id + '" ' + isSelected + '>' + paddingStr + list[i].name + '</option>';
		}
		$('select[name="cate"]').html(html);
	}
};