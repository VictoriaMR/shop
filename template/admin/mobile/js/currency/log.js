$(function(){
	CURRENCY_LOG.init();
});

const CURRENCY_LOG = {
	init: function() {
		// 切换货币下拉框自动提交检索
		$('#currency-log-page select[name="code"]').on('change', function(){
			$(this).closest('form').submit();
		});
	}
};
