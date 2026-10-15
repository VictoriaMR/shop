$(function() {
	STATICDETAIL.init();
});

const STATICDETAIL = {
	init: function() {
		const _this = this;

		$('#copy-static-btn').on('click', function() {
			const text = $('#static-code-content').text() || '';
			if (!text) return;
			_this.copyText(text, function() {
				successTips('文件内容已复制到剪贴板');
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
