$(function(){
	TASK.init();
});
const TASK = {
	init: function() {
		const _this = this;
		_this.startClock();

		$('#task-page').on('click', '.btn-task', function(){
			const btn = $(this);
			if (btn.is(':disabled') || btn.hasClass('disabled')) {
				return false;
			}
			const type = btn.data('type');
			const key = btn.closest('tr').data('key') || '';

			if (type === 'stop-all') {
				confirm('确定要停止所有后台定时任务吗？', function(confirmBtn, modal){
					modal.hide();
					_this.execute(btn, type, key);
				});
				return;
			}
			_this.execute(btn, type, key);
		});
	},
	execute: function(btn, type, key) {
		btn.button('loading');
		post('', {opn: 'modifyTask', type: type, key: key}, function(res) {
			showTips(res);
			if (res && res.code) {
				setTimeout(function(){
					window.location.reload();
				}, 800);
			} else {
				btn.button('reset');
			}
		});
	},
	startClock: function() {
		const timeEl = $('#time');
		if (!timeEl.length) return;
		let currentTs = Date.parse(timeEl.text().replace(/-/g, '/'));
		if (isNaN(currentTs)) return;
		setInterval(function(){
			currentTs += 1000;
			const d = new Date(currentTs);
			const pad = function(n){ return n < 10 ? '0' + n : n; };
			const str = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate())
				+ ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
			timeEl.text(str);
		}, 1000);
	}
};