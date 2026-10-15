<?php
$totalCount = count($taskList ?? []);
$runningCount = 0;
$cronCount = 0;
$otherCount = 0;
$totalMemory = 0;
foreach ($taskList ?? [] as $item) {
	if (($item['status'] ?? '') === 'running') {
		$runningCount++;
	}
	$itemType = $item['type'] ?? 'other';
	if ($itemType === 'cron') {
		$cronCount++;
	} elseif ($itemType === 'other') {
		$otherCount++;
	}
	$totalMemory += (int)($item['memory_usage'] ?? 0);
}
$bootBadgeMap = [
	'on'     => ['tag-success', '已开启'],
	'oning'  => ['tag-warning', '启动中'],
	'offing' => ['tag-info', '停止中'],
	'off'    => ['tag-default', '已关闭'],
];
$typeBadgeMap = [
	'main'  => ['tag-primary', '常驻主任务'],
	'cron'  => ['tag-info', '定时任务'],
	'other' => ['tag-default', '其他任务'],
];
?>
<div id="task-page" class="task-page" data-url="<?php echo adminUrl('task/index');?>">
	<!-- 顶部标题栏 + 批量操作 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-gears"></i>
			<span>定时任务</span>
			<span class="total-badge">共 <?php echo $totalCount;?> 个</span>
		</div>
		<div class="header-actions">
			<button type="button" title="启动所有任务" class="btn btn-success btn-xs btn-task" data-type="start-all" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
				<i class="fa fa-play"></i> 全部启动
			</button>
			<button type="button" title="停止所有任务" class="btn btn-danger btn-xs btn-task" data-type="stop-all" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
				<i class="fa fa-stop"></i> 全部停止
			</button>
		</div>
	</div>

	<!-- 统计概览卡片 -->
	<div class="summary-card">
		<div class="clock-row">
			<span class="clock-label"><i class="fa fa-clock"></i> 当前系统时间：</span>
			<strong id="time" class="clock-time"><?php echo now();?></strong>
		</div>
		<div class="stat-grid">
			<div class="stat-item">
				<span class="stat-num text-success"><?php echo $runningCount;?></span>
				<span class="stat-label">运行中</span>
			</div>
			<div class="stat-item">
				<span class="stat-num"><?php echo $cronCount;?></span>
				<span class="stat-label">定时任务</span>
			</div>
			<div class="stat-item">
				<span class="stat-num"><?php echo $otherCount;?></span>
				<span class="stat-label">其他任务</span>
			</div>
			<div class="stat-item">
				<span class="stat-num"><?php echo get1024Peck($totalMemory);?></span>
				<span class="stat-label">总内存</span>
			</div>
		</div>
	</div>

	<!-- 任务卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($taskList)) { ?>
			<div class="empty-state">
				<i class="fa fa-gears"></i>
				<p>暂无定时任务数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($taskList as $key => $value) {
				$taskType = $value['type'] ?? ($key === 'app/task/MainTask' ? 'main' : 'other');
				$isMain = ($taskType === 'main');
				$isCron = ($taskType === 'cron');
				$status = $value['status'] ?? 'stop';
				$boot = $value['boot'] ?? 'off';
				$isRunning = ($status === 'running');
				$bootInfo = $bootBadgeMap[$boot] ?? ['tag-default', htmlspecialchars((string)$boot, ENT_QUOTES, 'UTF-8')];
				$typeInfo = $typeBadgeMap[$taskType] ?? $typeBadgeMap['other'];
				$nextRun = (int)($value['next_run'] ?? 0);
				$startCount = (int)($value['start_count'] ?? 0);
				$runCount = (int)($value['run_count'] ?? 0);
				$crons = is_array($value['cron'] ?? null) ? $value['cron'] : [];
			?>
			<div class="task-card item <?php echo $isMain ? 'is-main' : '';?>" data-key="<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>">
				<div class="card-main">
					<div class="task-info">
						<span class="task-dot <?php echo $isRunning ? 'dot-running' : 'dot-stop';?>" title="<?php echo $isRunning ? '进程运行中' : '进程已停止';?>"></span>
						<strong class="task-name"><?php echo htmlspecialchars((string)($value['name'] ?? '-'), ENT_QUOTES, 'UTF-8');?></strong>
						<span class="status-tag <?php echo $typeInfo[0];?>"><?php echo $typeInfo[1];?></span>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-success btn-xs btn-task" data-type="start" title="启动" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $boot === 'off' ? '' : 'disabled';?>>
							<i class="fa fa-play"></i>
						</button>
						<button type="button" class="btn btn-danger btn-xs btn-task" data-type="stop" title="停止" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $boot === 'on' ? '' : 'disabled';?>>
							<i class="fa fa-stop"></i>
						</button>
						<?php if ($isMain) { ?>
						<button type="button" class="btn btn-warning btn-xs btn-task" data-type="reset" title="重置并重启主任务进程" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
							<i class="fa fa-rotate-right"></i>
						</button>
						<?php } else { ?>
						<button type="button" class="btn btn-primary btn-xs btn-task" data-type="run" title="立即触发执行一次" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isRunning ? 'disabled' : '';?>>
							<i class="fa fa-bolt"></i>
						</button>
						<?php } ?>
					</div>
				</div>

				<div class="task-sub-bar">
					<code class="task-key"><?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?></code>
					<div class="badge-group">
						<?php if ($isRunning) { ?>
						<span class="status-tag tag-success"><i class="fa fa-circle-play"></i> 运行中</span>
						<?php } else { ?>
						<span class="status-tag tag-default"><i class="fa fa-circle-stop"></i> 已停止</span>
						<?php } ?>
						<span class="status-tag <?php echo $bootInfo[0];?>">开关: <?php echo $bootInfo[1];?></span>
					</div>
				</div>

				<div class="task-meta-grid">
					<div class="meta-cell">
						<span class="meta-label">PID / 用户：</span>
						<span class="meta-val"><strong><?php echo !empty($value['process_pid']) ? (int)$value['process_pid'] : '--';?></strong> / <?php echo htmlspecialchars((string)($value['process_user'] ?? '--'), ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="meta-cell">
						<span class="meta-label">内存占用：</span>
						<span class="meta-val"><strong><?php echo !empty($value['memory_usage']) ? get1024Peck($value['memory_usage']) : '--';?></strong></span>
					</div>
					<div class="meta-cell">
						<span class="meta-label">调度规则：</span>
						<span class="meta-val">
							<?php if ($isMain) { ?>
								<code class="cron-code">常驻监听调度</code>
							<?php } elseif ($isCron && !empty($crons)) { foreach ($crons as $cronExpr) { ?>
								<code class="cron-code"><?php echo htmlspecialchars((string)$cronExpr, ENT_QUOTES, 'UTF-8');?></code>
							<?php } } else { ?>
								<span class="text-muted">按需触发</span>
							<?php } ?>
						</span>
					</div>
					<div class="meta-cell">
						<span class="meta-label">启动 / 循环：</span>
						<span class="meta-val"><strong><?php echo $startCount;?></strong> / <strong><?php echo $runCount;?></strong></span>
					</div>
					<div class="meta-cell full">
						<span class="meta-label">下次执行：</span>
						<span class="meta-val">
							<?php if ($isMain) { ?>
								<span class="text-success">常驻运行</span>
							<?php } elseif ($nextRun > 0) { ?>
								<strong class="text-primary"><?php echo now($nextRun);?></strong>
							<?php } elseif ($isCron) { ?>
								<span class="text-muted">未排期</span>
							<?php } else { ?>
								<span class="text-muted">待触发</span>
							<?php } ?>
						</span>
					</div>
					<div class="meta-cell full time-pair">
						<span>启动：<?php echo !empty($value['start_at']) ? now($value['start_at']) : '--';?></span>
						<span>最近：<?php echo !empty($value['run_at']) ? now($value['run_at']) : '--';?></span>
					</div>
				</div>

				<?php if (!empty($value['remark'])) { ?>
				<div class="task-remark">
					<i class="fa fa-circle-info"></i>
					<span><?php echo htmlspecialchars((string)$value['remark'], ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>
