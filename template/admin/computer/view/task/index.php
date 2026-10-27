<?php
$totalCount = count($taskList ?? []);
$runningCount = 0;
$cronCount = 0;
$otherCount = 0;
$totalMemory = 0;
foreach ($taskList ?? [] as $item) {
	if (($item['status'] ?? '') === 'running') {
		$runningCount++;
		$totalMemory += (int)($item['memory_usage'] ?? 0);
	}
	$itemType = $item['type'] ?? 'other';
	if ($itemType === 'cron') {
		$cronCount++;
	} elseif ($itemType === 'other') {
		$otherCount++;
	}
}
$bootBadgeMap = [
	'on'     => ['label-success', '已开启'],
	'oning'  => ['label-warning', '启动中'],
	'offing' => ['label-info', '停止中'],
	'off'    => ['label-default', '已关闭'],
];
$typeBadgeMap = [
	'main'  => ['label-primary', '常驻主任务'],
	'cron'  => ['label-info', '定时任务'],
	'other' => ['label-default', '其他任务'],
];
?>
<div id="task-page" class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<div class="form-inline" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 6px;">
				<div class="form-group" style="margin-bottom: 6px;">
					<span class="text-muted">
						<i class="fa fa-clock"></i> 当前系统时间：<strong id="time" style="color: #0f172a; font-family: monospace;"><?php echo now();?></strong>
					</span>
					<span class="text-muted" style="margin-left: 16px;">
						共 <strong><?php echo $totalCount;?></strong> 个任务（主任务 <strong>1</strong> / 定时 <strong><?php echo $cronCount;?></strong> / 其他 <strong><?php echo $otherCount;?></strong>），运行中 <strong class="green"><?php echo $runningCount;?></strong> 个，总内存占用 <strong><?php echo get1024Peck($totalMemory);?></strong>
					</span>
				</div>
				<div class="form-group" style="margin-bottom: 6px;">
					<div class="btn-group" role="group">
						<button type="button" title="启动所有任务" class="btn btn-success btn-sm btn-task" data-type="start-all">
							<i class="fa fa-play"></i> 全部启动
						</button>
						<button type="button" title="停止所有任务" class="btn btn-danger btn-sm btn-task" data-type="stop-all">
							<i class="fa fa-stop"></i> 全部停止
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="260">任务名称 / 类路径</th>
				<th width="180">进程 / 调度规则</th>
				<th width="220">运行时间 / 内存</th>
				<th width="140" class="tc">运行 / 开关状态</th>
				<th width="120">执行统计</th>
				<th>任务备注</th>
				<th width="185">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($taskList)){ ?>
			<tr>
				<td colspan="7">
					<div class="tc orange"><i class="fa fa-info-circle"></i> 暂无定时任务数据</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($taskList as $key => $value){
				$taskType = $value['type'] ?? ($key === 'app/task/MainTask' ? 'main' : 'other');
				$isMain = ($taskType === 'main');
				$isCron = ($taskType === 'cron');
				$status = $value['status'] ?? 'stop';
				$boot = $value['boot'] ?? 'off';
				$isRunning = ($status === 'running');
				$bootInfo = $bootBadgeMap[$boot] ?? ['label-default', htmlspecialchars($boot)];
				$typeInfo = $typeBadgeMap[$taskType] ?? $typeBadgeMap['other'];
				$nextRun = (int)($value['next_run'] ?? 0);
				$startCount = (int)($value['start_count'] ?? 0);
				$runCount = (int)($value['run_count'] ?? 0);
				$crons = is_array($value['cron'] ?? null) ? $value['cron'] : [];
			?>
			<tr data-key="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8');?>" class="<?php echo $isMain ? 'info' : '';?>">
				<td>
					<div style="display: flex; align-items: center;">
						<span class="task-dot <?php echo $isRunning ? 'dot-running' : 'dot-stop';?>" title="<?php echo $isRunning ? '进程运行中' : '进程已停止';?>"></span>
						<div>
							<div>
								<strong style="color: #0f172a; font-size: 13px;"><?php echo htmlspecialchars($value['name'] ?? '-');?></strong>
								<span class="label <?php echo $typeInfo[0];?>" style="margin-left: 4px; font-weight: normal;"><?php echo $typeInfo[1];?></span>
							</div>
							<div class="mt2">
								<code style="color: #475569; background: #f1f5f9; padding: 1px 5px; border-radius: 3px; font-size: 11px;"><?php echo htmlspecialchars($key);?></code>
							</div>
						</div>
					</div>
				</td>
				<td>
					<div class="f12"><span class="text-muted">PID:</span> <strong><?php echo !empty($value['process_pid']) ? (int)$value['process_pid'] : '--';?></strong> <span class="text-muted ml4">User:</span> <?php echo htmlspecialchars($value['process_user'] ?? '--');?></div>
					<div class="mt2">
						<?php if ($isMain){ ?>
						<code style="color: #0369a1; background: #f0f9ff; padding: 1px 5px; border-radius: 3px; font-size: 11px;">常驻监听调度</code>
						<?php } elseif ($isCron && !empty($crons)){ foreach ($crons as $cronExpr){ ?>
						<code style="display: inline-block; margin-right: 4px; color: #0369a1; background: #f0f9ff; padding: 1px 5px; border-radius: 3px; font-size: 11px;" title="Cron 表达式"><?php echo htmlspecialchars($cronExpr);?></code>
						<?php } } else { ?>
						<span class="text-muted f12">按需触发</span>
						<?php } ?>
					</div>
				</td>
				<td class="f12">
					<div><span class="text-muted">启动:</span> <?php echo !empty($value['start_at']) ? now($value['start_at']) : '--';?></div>
					<div><span class="text-muted">最近:</span> <?php echo !empty($value['run_at']) ? now($value['run_at']) : '--';?></div>
					<div>
						<span class="text-muted">下次:</span>
						<?php if ($isMain){ ?>
							<span class="green">常驻运行</span>
						<?php } elseif ($nextRun > 0){ ?>
							<span style="color: #0369a1; font-weight: 600;"><?php echo now($nextRun);?></span>
						<?php } elseif ($isCron){ ?>
							<span class="text-muted">未排期</span>
						<?php } else { ?>
							<span class="text-muted">待触发</span>
						<?php } ?>
					</div>
					<div><span class="text-muted">内存:</span> <strong><?php echo !empty($value['memory_usage']) ? get1024Peck($value['memory_usage']) : '--';?></strong></div>
				</td>
				<td class="tc">
					<div>
						<?php if ($isRunning){ ?>
						<span class="label label-success" style="font-weight: normal; padding: 3px 8px;"><i class="fa fa-circle-play"></i> 运行中</span>
						<?php } else { ?>
						<span class="label label-default" style="font-weight: normal; padding: 3px 8px;"><i class="fa fa-circle-stop"></i> 已停止</span>
						<?php } ?>
					</div>
					<div class="mt4">
						<span class="label <?php echo $bootInfo[0];?>" style="font-weight: normal; padding: 2px 6px;">开关: <?php echo $bootInfo[1];?></span>
					</div>
				</td>
				<td class="f12">
					<div><span class="text-muted">启动次数:</span> <strong><?php echo $startCount;?></strong></div>
					<div class="mt2"><span class="text-muted">循环次数:</span> <strong><?php echo $runCount;?></strong></div>
				</td>
				<td>
					<span class="text-muted f12" style="white-space: pre-wrap;"><?php echo !empty($value['remark']) ? htmlspecialchars($value['remark']) : '--';?></span>
				</td>
				<td style="white-space: nowrap;">
					<div class="btn-group btn-group-xs" role="group">
						<button type="button" class="btn btn-success btn-xs btn-task" data-type="start" <?php echo $boot === 'off' ? '' : 'disabled';?>>
							<i class="fa fa-play"></i> 启动
						</button>
						<button type="button" class="btn btn-danger btn-xs btn-task" data-type="stop" <?php echo $boot === 'on' ? '' : 'disabled';?>>
							<i class="fa fa-stop"></i> 停止
						</button>
						<?php if ($isMain){ ?>
						<button type="button" class="btn btn-warning btn-xs btn-task" data-type="reset" title="重置并重启主任务进程">
							<i class="fa fa-rotate-right"></i> 重置
						</button>
						<?php } else { ?>
						<button type="button" class="btn btn-primary btn-xs btn-task" data-type="run" title="立即触发执行一次" <?php echo $isRunning ? 'disabled' : '';?>>
							<i class="fa fa-bolt"></i> 执行
						</button>
						<?php } ?>
					</div>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>