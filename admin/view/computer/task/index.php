<div class="container-fluid">
	<div class="row-item">
		<div class="left" style="padding-top: 10px;">
		系统状态：<?php if(config('task.enabled')){echo '<span style="color:#5cb85c;">开启</span>';}else{echo '<span style="color:#e7502b;">关闭</span>';}?> 
		当前系统时间： <span id="time"><?php echo now();?></span>
		</div>
		<div class="task-group right">
			<div style="display:inline-block;">操作所有任务: </div>
			<div class="btn-group" role="group" id="select-status" style="display:inline-block;">
				<button title="启动所有任务" class="btn btn-primary btn-sm btn-task" data-type="startup_all" <?php if(isset($val['data']['boot']) && in_array($val['data']['boot'],['on','restart'])){echo 'disabled';}?>>启动</button>
				<button title="重动所有任务" class="btn btn-success btn-sm btn-task" data-type="restart_all" <?php if(isset($val['data']['boot']) && in_array($val['data']['boot'],['off','restart'])){echo 'disabled';}?>>重启</button>
				<button title="停止所有任务" class="btn btn-danger btn-sm btn-task" data-type="shutdown_all" <?php if(isset($val['data']['boot']) && in_array($val['data']['boot'],['off','restart'])){echo 'disabled';}?>>停止</button>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="row-item mt8">
		<table class="table table-hover table-middle">
			<thead>
				<tr>
					<th width="200">任务类<BR>任务说明</th>
					<th width="100">IP<BR>PID</th>
					<th width="170">启动/下次运行时间<BR>心跳/上次停止时间</th>
					<th width="80">运行状态<BR>开关状态 </th>
					<th width="80">运行次数<BR>循环次数</th>
					<th width="310">任务信息</th>
					<th width="190">操作</th>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
$(function(){
	TASK.init();
});
</script>