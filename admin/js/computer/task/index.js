const TASK = {
	init: function(enable) {
		const _this = this;
		_this.loading = true;
		_this.enable = enable;
		_this.initData();
		clearInterval(_this.interval);
		_this.interval = setInterval(function(){
			if (_this.loading) {
				_this.loading = false;
				_this.initData();
			}
		}, 5000);
	},
	initData: function() {
		const _this = this;
		$.post(URI+'task', {opn: 'taskList'}, function(res){
			_this.loading = true;
			if (res.code === '200') {
				_this.updateTime(res.data.time);
				_this.updatePage(res.data.list);
			} else {
				errorTips(res.message);
			}
		});
	},
	updateTime: function(time) {
		$('#time').text(time);
	},
	updatePage: function(list) {
		$('table tbody').html('');
		if (list.length === 0) {
			return false;
		}
		let html = '';
		for (let i=0;i<list.length;i++) {
			html += '<tr data-key="'+list[i].name+'">\
				<td>\
					<li class="cycle-'+(list[i].boot ? list[i].boot :'off')+'"></li>\
					<div class="in-1" title="'+list[i].name+'">'+list[i].name+'<br>'+list[i].info+'</div>\
				</td>\
				<td>\
					'+(list[i]['process.gid'] ? list[i]['process.gid']+'<br>' : '')+'\
					'+(list[i]['process.pid'] ? list[i]['process.pid']+'<br>' : '')+'\
					'+(list[i]['process.uid'] ? list[i]['process.uid']+'<br>' : '')+'\
					'+(list[i]['process.user'] ? list[i]['process.user'] : '')+'\
				</td>\
				<td>\
					<span title="开始时间">'+(list[i].start_time ? list[i].start_time : '')+'</span><br >\
					<span title="运行时间">'+(list[i].nextRun ? list[i].nextRun : '')+'</span><br >\
					<span title="使用内存">'+(list[i].memoryUsage ? list[i].memoryUsage : '')+'</span>\
				</td>\
				<td>\
					<li class="cycle-'+(list[i].boot ? list[i].boot :'off')+'"></li>\
					<div class="in-1 mt8">\
						<span class="in-2">'+(list[i].boot ? list[i].boot :'off')+'</span>\
					</div>\
				</td>\
				<td>\
					<span title="运行次数">'+(list[i].count ? list[i].count : '0')+'</span><br >\
					<span title="运行次数">'+(list[i].loopCount ? list[i].loopCount : '0')+'</span>\
				</td>\
				<td>\
					<span>'+(list[i].remark ? list[i].remark : '')+'</span><br >\
					<span title="下次运行时间">'+(list[i].nextRun ? '下次运行时间:'+list[i].nextRun : '')+'</span><br >\
				</td>\
				<td>\
					<div class="btn-group" role="group" id="select-status">\
						<button class="btn btn-success btn-sm btn-task" data-type="startup" '+(list[i].boot === 'on' || !this.enable ? 'disabled' : '')+'>启动</button>\
						<button class="btn btn-danger btn-sm btn-task" data-type="shutdown" '+(list[i].boot === 'on' ? '' : 'disabled')+'>停止</button>\
					</div>\
				</td>\
			</tr>';
		}
		$('table tbody').html(html);
	},
	click: function() {
		const _this = this;
		$('#task-page').on('click', '.btn-task', function(){
			clearInterval(_this.interval);
			const _thisobj = $(this);
			const type = $(this).data('type');
			const key = $(this).parents('tr').data('key');
			_thisobj.button('loading');
			$.post(URI+'task', {opn: 'modifyTask', type: type, key: key}, function(res) {
				_thisobj.button('reset');
				if (res.code === '200') {
					successTips(res.message);
				} else {
					errorTips(res.message);
				}
				_this.init(_this.enable);
			});
		});
	}
};