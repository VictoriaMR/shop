<div id="task-page" class="container-fluid" data-status="<?php echo $enabled;?>">
	<div class="row-item">
		<div class="left" style="padding-top: 10px;">
		系统状态：<?php if($enabled){echo '<span style="color:#5cb85c;">开启</span>';}else{echo '<span style="color:#e7502b;">关闭</span>';}?> 
		当前系统时间： <span id="time"><?php echo now();?></span>
		</div>
		<div class="task-group right">
			<div style="display:inline-block;">操作所有任务: </div>
			<div class="btn-group" role="group" id="select-status" style="display:inline-block;">
				<button title="启动所有任务" class="btn btn-success btn-sm btn-task" <?php echo $enabled ? '' : 'disabled="disabled"';?> data-type="startup_all">启动</button>
				<button title="停止所有任务" class="btn btn-danger btn-sm btn-task" data-type="shutdown_all">停止</button>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="row-item mt8">
		<table class="table table-hover table-middle">
			<thead>
				<tr>
					<th width="300">任务类<br >任务说明</th>
					<th width="150">IP<br >PID</th>
					<th width="200">信息</th>
					<th width="80">运行状态<br >开关状态 </th>
					<th width="80">运行次数<br >循环次数</th>
					<th width="300">任务信息</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>