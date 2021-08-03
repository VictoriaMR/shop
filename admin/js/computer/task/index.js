const TASK = {
	init: function() {
		const _this = this;
		_this.initData();
		_this.loading = true;
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
					'+list[i]['process.gid']+'<br>\
					'+list[i]['process.pid']+'<br>\
					'+list[i]['process.uid']+'<br>\
					'+list[i]['process.user']+'\
				</td>\
				<td>\
					<span title="开始时间">'+(list[i].start_time ? list[i].start_time : '')+'</span><br >\
					<span title="运行时间">'+(list[i].nextRun ? list[i].nextRun : '')+'</span><br >\
					<span title="使用内存">'+(list[i].memoryUsage ? list[i].memoryUsage : '')+'</span>\
				</td>\
				<td>\
					<li class="cycle-'+(list[i].boot ? list[i].boot :'off')+'"></li>\
					<div class="in-1">\
						<span class="in-2">'+(list[i].boot ? list[i].boot :'off')+'</span>\
					</div>\
				</td>\
				<td>\
					<span title="运行次数">'+(list[i].count ? list[i].count : '0')+'</span><br >\
					<span title="运行次数">'+(list[i].loopCount ? list[i].loopCount : '0')+'</span>\
				</td>\
				<td>\
					<span>'+(list[i].remark ? list[i].remark : '')+'</span>\
				</td>\
				<td>\
					<div class="btn-group" role="group" id="select-status">\
						<button class="btn btn-primary btn-sm btn-task" data-type="startup" '+(list[i].boot === 'on' ? 'disabled' : '')+'>启动</button>\
						<button class="btn btn-success btn-sm btn-task" data-type="restart" '+(list[i].boot !== 'on' ? 'disabled' : '')+'>重启</button>\
						<button class="btn btn-danger btn-sm btn-task" data-type="shutdown" '+(list[i].boot === 'on' ? '' : 'disabled')+'>停止</button>\
					</div>\
				</td>\
            </tr>';
		}
		$('table tbody').html(html);
	}
};