$(function(){
	SITELOG.init();
});

const SITELOG = {
	init: function() {
		// 日志类型前端快速筛选
		$('.log-type-filter button').on('click', function(){
			const btn = $(this);
			const type = btn.data('type');
			btn.removeClass('btn-default').addClass('btn-primary').siblings().removeClass('btn-primary').addClass('btn-default');
			if (type === 'all') {
				$('#data-list tr.log-row').show();
			} else {
				$('#data-list tr.log-row').each(function(){
					if ($(this).data('type') === type) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		});

		// 删除日志
		$('.btn.delete').on('click', function(){
			const obj = $(this);
			const name = obj.data('id');
			const tip = name === 'all' ? '确认要清除全部运行日志文件吗?' : '确认要删除日志文件 [' + name + '] 吗?';
			confirm(tip, function(btn, modal){
				obj.button('loading');
				post('', {opn: 'deleteLog', name: name}, function(res){
					showTips(res);
					if (res && res.code == 1) {
						window.location.reload();
					} else {
						obj.button('reset');
						modal.hide();
					}
				});
			});
		});
	}
};