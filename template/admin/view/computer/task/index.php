<div id="task-page" class="container-fluid" data-status="<?php echo $enabled;?>">
	<div class="row-item">
		<div class="left" style="padding-top: 10px;">
		系统状态：<?php if($enabled){echo '<span style="color:#5cb85c;">开启</span>';}else{echo '<span style="color:#e7502b;">关闭</span>';}?>
		当前系统时间： <span id="time"><?php echo now();?></span>
		</div>
		<?php if ($enabled){?>
		<div class="task-group right">
			<div style="display:inline-block;">操作所有任务: </div>
			<div class="btn-group" role="group" id="select-status" style="display:inline-block;">
				<?php if (empty($taskList)){?>
				<button title="初始化任务" class="btn btn-success btn-sm btn-task" <?php echo $enabled ? '' : 'disabled="disabled"';?> data-type="init">初始化</button>
				<?php } else {?>
				<button title="启动所有任务" class="btn btn-success btn-sm btn-task" <?php echo $enabled ? '' : 'disabled="disabled"';?> data-type="start-all">启动</button>
				<button title="停止所有任务" class="btn btn-danger btn-sm btn-task" data-type="stop-all">停止</button>
				<?php }?>
			</div>
		</div>
		<?php }?>
		<div class="clear"></div>
	</div>
	<?php if ($enabled){?>
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
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($taskList as $key=>$value){?>
				<tr data-key="<?php echo $key;?>">
					<td>
						<li class="cycle-<?php echo $value['boot'];?>"></li>
						<div class="in-1" title="<?php echo $value['name'];?>">
							<?php echo $value['name'];?><br>
							<?php echo $value['class_name'];?>
						</div>
					</td>
					<td>
						<?php echo $value['process_pid'] ?? '--';?><br>
						<?php echo $value['process_user'] ?? '--';?>
					</td>
					<td>
						<span title="开始时间"><?php echo $value['start_time'] ?? '--';?></span><br >
						<span title="运行时间"><?php echo $value['next_run'] ?? '--';?></span><br >
						<span title="使用内存"><?php echo $value['memory_usage'] ?? '--';?></span>
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
						<span><?php echo $value['info'] ?? '--';?></span><br >
					</td>
					<td>
						<div class="btn-group" role="group" id="select-status">
							<button class="btn btn-success btn-sm btn-task" data-type="start">启动</button>
							<button class="btn btn-danger btn-sm btn-task" data-type="stop">停止</button>
						</div>
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
	<?php }?>
</div>