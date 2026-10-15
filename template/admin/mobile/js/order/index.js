$(function() {
	ORDERLIST.init();
});

const ORDERLIST = {
	url: '',
	init: function() {
		this.url = $('#order-page').data('url') || '';
		const _this = this;

		// 顶部状态按钮快速筛选
		$('.status-filter button[data-status]').on('click', function() {
			const status = $(this).data('status');
			const form = $('#order-search-form');
			form.find('input[name="status"]').val(status);
			form.submit();
		});

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 打开状态更新弹窗
		$('#data-list').on('click', '.status-btn', function() {
			const $item = $(this).closest('.item');
			const orderId = $item.data('id');
			const status = $item.data('status');
			const $modal = $('#dealbox-status');
			$modal.find('[name="order_id"]').val(orderId);
			$modal.find('[name="order_id_Text"]').val('#' + orderId);
			$modal.find('[name="status"]').val(status);
			$modal.fadeIn(150);
		});

		// 保存订单状态
		$('#dealbox-status').on('click', '.save-status-btn', function() {
			const $btn = $(this);
			const $form = $('#status-form');
			$btn.button('loading');
			post(_this.url, $form.serializeArray(), function(res) {
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				showTips(res);
				if (res && res.code == 1) {
					$('#dealbox-status').fadeOut(150);
					setTimeout(function() {
						window.location.reload();
					}, 800);
				} else {
					$btn.button('reset');
				}
			});
		});

		// 查看订单详情
		$('#data-list').on('click', '.detail-btn', function() {
			const $btn = $(this);
			const orderId = $btn.closest('.item').data('id');
			if (!orderId) return;

			$btn.button('loading');
			post(_this.url, { opn: 'getInfo', order_id: orderId }, function(res) {
				$btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch (e) {}
				}
				if (res && res.code == 1 && res.data) {
					_this.renderDetail(res.data);
				} else {
					showTips(res || { code: 0, msg: '获取订单详情失败' });
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
		return '<div class="addr-box">'
			+ '<div><strong>' + name + '</strong> ' + (phone ? '<span class="text-muted">(' + phone + ')</span>' : '') + '</div>'
			+ '<div class="text-muted">' + line + '</div>'
			+ '</div>';
	},
	renderDetail: function(data) {
		const base = data.base || {};
		const cur = this.escapeHtml(base.currency || 'USD');
		$('#detail-order-title').text('#' + (base.order_id || ''));

		let html = '<div class="detail-section">'
			+ '<div class="detail-sec-title"><i class="fa fa-circle-info"></i> 基本信息</div>'
			+ '<div class="detail-kv-grid">'
			+ '<div class="detail-kv-item"><span class="detail-kv-label">状态：</span><strong class="detail-kv-val">' + this.escapeHtml(base.status_name || '') + '</strong></div>'
			+ '<div class="detail-kv-item"><span class="detail-kv-label">商品小计：</span><span class="detail-kv-val">' + cur + ' ' + parseFloat(base.product_total || 0).toFixed(2) + '</span></div>'
			+ '<div class="detail-kv-item"><span class="detail-kv-label">运费/保险：</span><span class="detail-kv-val">' + cur + ' ' + parseFloat(base.shipping_fee || 0).toFixed(2) + ' / ' + parseFloat(base.insurance_free || 0).toFixed(2) + '</span></div>'
			+ '<div class="detail-kv-item"><span class="detail-kv-label">优惠减免：</span><span class="detail-kv-val">' + cur + ' ' + parseFloat(base.coupon_free || 0).toFixed(2) + '</span></div>'
			+ '<div class="detail-kv-item full"><span class="detail-kv-label">订单总额：</span><strong class="detail-total-highlight">' + cur + ' ' + parseFloat(base.order_total || 0).toFixed(2) + '</strong></div>'
			+ '<div class="detail-kv-item full"><span class="detail-kv-label">下单时间：</span><span class="detail-kv-val">' + this.escapeHtml(base.add_time || '-') + '</span></div>'
			+ '</div></div>';

		html += '<div class="detail-section">'
			+ '<div class="detail-sec-title"><i class="fa fa-truck-fast"></i> 收货地址</div>'
			+ this.formatAddress(data.shipping_address)
			+ '</div>';

		html += '<div class="detail-section">'
			+ '<div class="detail-sec-title"><i class="fa fa-file-invoice-dollar"></i> 账单地址</div>'
			+ this.formatAddress(data.billing_address)
			+ '</div>';

		const products = data.products || [];
		html += '<div class="detail-section">'
			+ '<div class="detail-sec-title"><i class="fa fa-box-open"></i> 商品明细 (' + products.length + ')</div>';
		if (products.length === 0) {
			html += '<div class="text-muted">暂无商品明细</div>';
		} else {
			for (let i = 0; i < products.length; i++) {
				const p = products[i];
				const price = parseFloat(p.price || 0);
				const qty = parseInt(p.quantity || 0, 10);
				html += '<div class="prod-item-card">'
					+ '<div class="prod-top">'
					+ '<div class="prod-name">' + this.escapeHtml(p.name) + '</div>'
					+ '<span class="prod-sku">SKU #' + parseInt(p.sku_id || 0, 10) + '</span>'
					+ '</div>'
					+ (p.attr_text ? '<div class="prod-attr">' + this.escapeHtml(p.attr_text) + '</div>' : '')
					+ '<div class="prod-price-row">'
					+ '<span>' + cur + ' ' + price.toFixed(2) + ' × ' + qty + '</span>'
					+ '<strong>' + cur + ' ' + (price * qty).toFixed(2) + '</strong>'
					+ '</div>'
					+ '</div>';
			}
		}
		html += '</div>';

		const history = data.history || [];
		if (history.length > 0) {
			html += '<div class="detail-section">'
				+ '<div class="detail-sec-title"><i class="fa fa-clock-rotate-left"></i> 状态变更记录</div>';
			for (let j = 0; j < history.length; j++) {
				html += '<div class="history-item">'
					+ '<span class="history-time">' + this.escapeHtml(history[j].add_time || '-') + '</span>'
					+ '<span class="history-info">' + this.escapeHtml(history[j].info || '-') + '</span>'
					+ '</div>';
			}
			html += '</div>';
		}

		$('#detail-body').html(html);
		$('#dealbox-detail').fadeIn(150);
	}
};
