$(function() {
	PURCHASE_SHOP.init();
});

const PURCHASE_SHOP = {
	init: function() {
		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 查看店铺详情
		$('#data-list').on('click', '.view-shop-btn', function() {
			const $card = $(this).closest('.item');
			const $modal = $('#shop-detail-modal');
			$modal.find('.j-detail-id').text('#' + ($card.data('id') || '--'));
			$modal.find('.j-detail-channel').text($card.data('channel') || '--');
			$modal.find('.j-detail-unique').text($card.data('unique') || '--');
			$modal.find('.j-detail-name').text($card.data('name') || '--');
			$modal.find('.j-detail-count').text(($card.data('count') || 0) + ' 件');
			const metricsHtml = $card.find('.metrics-box').html() || '--';
			$modal.find('.j-detail-metrics').html(metricsHtml);
			$modal.find('.j-detail-time').text($card.data('time') || '--');
			$modal.fadeIn(150);
		});
	}
};
