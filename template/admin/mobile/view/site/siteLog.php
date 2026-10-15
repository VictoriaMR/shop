<div id="site-log-page" class="site-log-mobile-page" data-url="<?php echo adminUrl('site/siteLog');?>">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-file-lines"></i>
			<span>站点日志</span>
			<span class="total-badge">共 <?php echo count($list ?? []);?> 个</span>
		</div>
		<?php if (!empty($list)) { ?>
		<button class="btn btn-danger btn-xs delete" data-id="all" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
			<i class="fa fa-trash-can"></i> 清除全部
		</button>
		<?php } ?>
	</div>

	<!-- 日志类型快速筛选 -->
	<div class="filter-box">
		<div class="log-type-filter">
			<button type="button" data-type="all" class="filter-tab active">全部</button>
			<button type="button" data-type="request" class="filter-tab">请求日志</button>
			<button type="button" data-type="error" class="filter-tab">错误日志</button>
			<button type="button" data-type="task" class="filter-tab">任务日志</button>
		</div>
	</div>

	<!-- 日志卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
		<div class="empty-state">
			<i class="fa fa-folder-open"></i>
			<p>暂无运行日志</p>
		</div>
		<?php } else { foreach ($list as $value) {
			$logType = $value['log_type'] ?? 'request';
		?>
		<div class="log-card log-row" data-type="<?php echo htmlspecialchars($logType, ENT_QUOTES, 'UTF-8');?>">
			<div class="card-header">
				<div class="log-title-wrap">
					<?php if ($logType === 'error') { ?>
					<span class="type-badge error">错误</span>
					<?php } elseif ($logType === 'task') { ?>
					<span class="type-badge task">任务</span>
					<?php } else { ?>
					<span class="type-badge request">请求</span>
					<?php } ?>
					<code class="file-name"><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');?></code>
				</div>
				<div class="card-actions">
					<a href="<?php echo adminUrl('site/logDetail', ['name' => $value['name']]);?>" class="btn btn-primary btn-icon" title="查看日志"><i class="fa fa-eye"></i></a>
					<button class="btn btn-danger btn-icon delete" type="button" data-id="<?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');?>" title="删除日志" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
				</div>
			</div>
			<div class="card-meta">
				<span><i class="fa fa-hard-drive"></i> <?php echo sprintf('%.2f', ($value['size'] ?? 0) / 1024) . ' KB';?></span>
				<span><i class="fa fa-clock"></i> <?php echo htmlspecialchars($value['c_time'] ?? '--', ENT_QUOTES, 'UTF-8');?></span>
			</div>
		</div>
		<?php } } ?>
	</div>
</div>
