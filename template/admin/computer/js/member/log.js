$(function(){
	MEMBERLOG.init();
});
const MEMBERLOG = {
	init: function() {
		// 切换日志模块或操作行为下拉框自动提交查询
		$('select[name="log_type"], select[name="type"]').on('change', function(){
			$(this).closest('form').submit();
		});
	}
};
