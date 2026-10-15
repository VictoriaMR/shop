$(function() {
	LOGDETAIL.init();
});

const LOGDETAIL = {
	init: function() {
		const _this = this;

		// 关键词过滤日志分块
		$('#log-keyword-input').on('input', function() {
			const kw = $.trim($(this).val()).toLowerCase();
			$('#log-chunk-list .chunk-card').each(function() {
				const text = $(this).find('.chunk-pre').text().toLowerCase();
				if (kw === '' || text.indexOf(kw) !== -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});

		// 复制单条日志
		$('#log-chunk-list').on('click', '.copy-chunk-btn', function() {
			const $btn = $(this);
			const text = $btn.closest('.chunk-card').find('.chunk-pre').text() || '';
			if (!text) return;
			_this.copyText(text, function() {
				successTips('日志内容已复制');
			});
		});
	},

	copyText: function(text, callback) {
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(callback);
		} else {
			const $temp = $('<textarea>');
			$('body').append($temp);
			$temp.val(text).select();
			document.execCommand('copy');
			$temp.remove();
			if (typeof callback === 'function') callback();
		}
	}
};
