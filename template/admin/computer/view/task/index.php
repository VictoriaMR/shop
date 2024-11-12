<div id="task-page" class="container-fluid">
	<div class="row-item">
		<div class="left" style="padding-top: 10px;">
		当前系统时间： <span id="time"><?php echo now();?></span>
		</div>
		<div class="task-group right">
			<div style="display:inline-block;">操作所有任务: </div>
			<div class="btn-group" role="group" id="select-status" style="display:inline-block;">
				<button title="启动所有任务" class="btn btn-success btn-sm btn-task" data-type="start-all">启动</button>
				<button title="停止所有任务" class="btn btn-danger btn-sm btn-task" data-type="stop-all">停止</button>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="row-item mt8">
		<table class="table table-hover table-middle">
			<thead>
				<tr>
					<th width="320">任务类<br >任务说明</th>
					<th width="200">IP<br >PID</th>
					<th width="200">配置</th>
					<th width="150">运行状态<br >开关状态 </th>
					<th width="150">运行次数<br >循环次数</th>
					<th width="300">任务信息</th>
					<th width="150">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($taskList as $key=>$value){?>
				<tr data-key="<?php echo $key;?>">
					<td>
						<li class="cycle-<?php echo $value['boot'];?>"></li>
						<div class="in-1" title="<?php echo $value['name'];?>">
							<?php echo $value['name'];?><br>
							<?php echo $key;?>
						</div>
					</td>
					<td>
						<span title="进程ID"><?php echo $value['process_pid'] ?? '--';?></span><br>
						<span title="执行进程角色"><?php echo $value['process_user'] ?? '--';?></span><br>
						<span title="进程启动时间"><?php echo implode(PHP_EOL, $value['cron']);?></span>
					</td>
					<td>
						<span title="开始时间"><?php echo empty($value['start_at']) ? '--': now($value['start_at']);?></span><br >
						<span title="运行时间"><?php echo empty($value['run_at']) ? '--': now($value['run_at']);?></span><br >
						<span title="下次运行时间"><?php echo isset($value['next_run']) ? ($value['next_run'] <= 0 ? 'alwaysRun' : now($value['next_run'])) : '--';?></span><br >
						<span title="使用内存"><?php echo empty($value['memory_usage']) ? '--' : get1024Peck($value['memory_usage']);?></span>
					</td>
					<td>
						<li class="cycle-<?php echo $value['boot'];?>"></li>
						<div class="in-1">
							<div class="in-2"><?php echo $value['status'];?></div>
							<div class="in-2 mt2"><?php echo $value['boot'];?></div>
						</div>
					</td>
					<td>
						<span title="运行次数"><?php echo $value['count'] ?? '--';?></span><br >
						<span title="循环次数"><?php echo $value['loop_count'] ?? '--';?></span>
					</td>
					<td>
						<span style="white-space: pre-wrap;"><?php echo $value['remark'] ?? '--';?></span>
					</td>
					<td>
						<div class="btn-group" role="group" id="select-status">
							<button class="btn btn-success btn-sm btn-task" data-type="start"<?php echo $value['boot']=='off'?'':'disabled';?>>启动</button>
							<button class="btn btn-danger btn-sm btn-task" data-type="stop"<?php echo $value['boot']=='on'?'':'disabled';?>>停止</button>
						</div>
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<p>共 <?php echo count($taskList);?> 个任务, 当前运行中的任务合计使用内存：<?php echo get1024Peck(array_sum(array_column($taskList, 'memory_usage')));?></p>
	</div>
</div>