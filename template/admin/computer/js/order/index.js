$(function(){
	ORDERLIST.init();
});
const ORDERLIST = {
	init: function() {
		const _this = this;

		// 顶部状态按钮快速筛选
		$('.status-filter button[data-status]').on('click', function(){
			const status = $(this).data('status');
			const form = $('#order-search-form');
			form.find('input[name="status"]').val(status);
			form.submit();
		});

		// 关闭弹窗
		$('.dealbox .close, .dealbox .mask').on('click', function(){
			$(this).closest('.dealbox').dealboxHide();
		});

		// 打开状态更新弹窗
		$('#data-list').on('click', '.status-btn', function(){
			const tr = $(this).closest('tr');
			const orderId = tr.data('id');
			const status = tr.data('status');
			const modal = $('#dealbox-status');
			modal.find('[name="order_id"]').val(orderId);
			modal.find('[name="order_id_Text"]').val('#' + orderId);
			modal.find('[name="status"]').val(status);
			modal.dealboxShow();
		});

		// 保存订单状态
		$('#dealbox-status .save-status-btn').on('click', function(){
			const btn = $(this);
			const form = $('#dealbox-status form');
			btn.button('loading');
			post('', form.serializeArray(), function(res){
				showTips(res);
				if (res && res.code) {
					$('#dealbox-status').dealboxHide();
					setTimeout(function(){
						window.location.reload();
					}, 800);
				} else {
					btn.button('reset');
				}
			});
		});

		// 查看订单详情
		$('#data-list').on('click', '.detail-btn', function(){
			const btn = $(this);
			const orderId = btn.closest('tr').data('id');
			btn.button('loading');
			post('', {opn: 'getInfo', order_id: orderId}, function(res){
				btn.button('reset');
				if (res && res.code && res.data) {
					_this.renderDetail(res.data);
				} else {
					showTips(res || {code: 0, msg: '获取订单详情失败'});
				}
			});
		});
	},
	escapeHtml: function(str) {
		if (str === null || typeof str === 'undefined') return '';
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	},
	formatAddress: function(addr) {
		if (!addr || (!addr.first_name && !addr.address1)) {
			return '<span class="text-muted">暂无地址信息</span>';
		}
		const name = this.escapeHtml($.trim((addr.first_name || '') + ' ' + (addr.last_name || '')));
		const phone = this.escapeHtml(addr.phone || '');
		const line = this.escapeHtml($.trim([addr.address1, addr.address2, addr.city, addr.state, addr.country_code2, addr.postcode].filter(Boolean).join(', ')));
		return '<div><strong>' + name + '</strong> ' + (phone ? '<span class="text-muted">(' + phone + ')</span>' : '') + '</div>'
			+ '<div class="text-muted f12 mt2">' + line + '</div>';
	},
	renderDetail: function(data) {
		const base = data.base || {};
		const cur = this.escapeHtml(base.currency || 'USD');
		$('#detail-order-title').text('#' + (base.order_id || ''));

		let html = '<div class="panel panel-default" style="margin-bottom: 12px;">'
			+ '<div class="panel-heading"><strong>基本信息</strong></div>'
			+ '<div class="panel-body" style="padding: 10px 15px;">'
			+ '<div class="row">'
			+ '<div class="col-xs-4">状态：<strong>' + this.escapeHtml(base.status_name || '') + '</strong></div>'
			+ '<div class="col-xs-4">商品小计：' + cur + ' ' + parseFloat(base.product_total || 0).toFixed(2) + '</div>'
			+ '<div class="col-xs-4">运费 / 保险：' + cur + ' ' + parseFloat(base.shipping_fee || 0).toFixed(2) + ' / ' + parseFloat(base.insurance_free || 0).toFixed(2) + '</div>'
			+ '</div>'
			+ '<div class="row mt6">'
			+ '<div class="col-xs-4">优惠减免：' + cur + ' ' + parseFloat(base.coupon_free || 0).toFixed(2) + '</div>'
			+ '<div class="col-xs-4">订单总额：<strong style="color:#e7502b;">' + cur + ' ' + parseFloat(base.order_total || 0).toFixed(2) + '</strong></div>'
			+ '<div class="col-xs-4">下单时间：' + this.escapeHtml(base.add_time || '-') + '</div>'
			+ '</div>'
			+ '</div></div>';

		html += '<div class="row" style="margin-bottom: 12px;">'
			+ '<div class="col-xs-6"><div class="panel panel-default" style="margin-bottom:0;"><div class="panel-heading"><strong>收货地址</strong></div><div class="panel-body" style="padding:10px 15px;">' + this.formatAddress(data.shipping_address) + '</div></div></div>'
			+ '<div class="col-xs-6"><div class="panel panel-default" style="margin-bottom:0;"><div class="panel-heading"><strong>账单地址</strong></div><div class="panel-body" style="padding:10px 15px;">' + this.formatAddress(data.billing_address) + '</div></div></div>'
			+ '</div>';

		const products = data.products || [];
		html += '<div class="panel panel-default" style="margin-bottom: 12px;">'
			+ '<div class="panel-heading"><strong>商品明细 (' + products.length + ')</strong></div>'
			+ '<table class="table table-condensed table-middle" style="margin-bottom:0;">'
			+ '<thead><tr><th width="70">SKU</th><th>商品名称 / 规格</th><th width="90">单价</th><th width="70">数量</th><th width="100">小计</th></tr></thead><tbody>';
		if (products.length === 0) {
			html += '<tr><td colspan="5" class="tc text-muted">暂无商品明细</td></tr>';
		} else {
			for (let i = 0; i < products.length; i++) {
				const p = products[i];
				const price = parseFloat(p.price || 0);
				const qty = parseInt(p.quantity || 0);
				html += '<tr>'
					+ '<td>#' + parseInt(p.sku_id || 0) + '</td>'
					+ '<td><div>' + this.escapeHtml(p.name) + '</div>' + (p.attr_text ? '<div class="text-muted f12">' + this.escapeHtml(p.attr_text) + '</div>' : '') + '</td>'
					+ '<td>' + cur + ' ' + price.toFixed(2) + '</td>'
					+ '<td>x' + qty + '</td>'
					+ '<td><strong>' + cur + ' ' + (price * qty).toFixed(2) + '</strong></td>'
					+ '</tr>';
			}
		}
		html += '</tbody></table></div>';

		const history = data.history || [];
		if (history.length > 0) {
			html += '<div class="panel panel-default" style="margin-bottom: 0;">'
				+ '<div class="panel-heading"><strong>状态变更记录</strong></div>'
				+ '<table class="table table-condensed" style="margin-bottom:0;">'
				+ '<thead><tr><th width="160">时间</th><th>变更说明</th></tr></thead><tbody>';
			for (let j = 0; j < history.length; j++) {
				html += '<tr><td class="text-muted">' + this.escapeHtml(history[j].add_time || '-') + '</td><td>' + this.escapeHtml(history[j].info || '-') + '</td></tr>';
			}
			html += '</tbody></table></div>';
		}

		$('#detail-body').html(html);
		$('#dealbox-detail').dealboxShow();
	}
};
