/* task_index */
$(function(){
	TASK.init();
});
const TASK = {
	init: function() {
		const _this = this;
		_this.enable = $('#task-page').data('status');
		$('#task-page .btn-task').unbind('click').on('click', function(){
			const _thisobj = $(this);
			const type = $(this).data('type');
			const key = $(this).parents('tr').data('key');
			_thisobj.button('loading');
			post('', {opn: 'modifyTask', type: type, key: key}, function(res) {
				showTips(res);
				if (res.code) {
					window.location.reload();
				} else {
					_thisobj.button('reset');
				}
			});
		});
	},
};
