$(function(){
	MEMBERLOG.init();
});
const MEMBERLOG = {
	init: function() {
		// 切换操作行为下拉框自动提交查询
		$('select[name="type"]').on('change', function(){
			$(this).closest('form').submit();
		});
	}
};
