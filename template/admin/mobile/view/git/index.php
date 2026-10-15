<div id="git-list" class="git-page" data-url="<?php echo adminUrl('git/index');?>">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-code-branch"></i>
			<span>Git版本库管理</span>
			<span class="total-badge">共 <?php echo count($list ?? []);?> 个库</span>
		</div>
	</div>

	<!-- 版本库卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-code-branch"></i>
				<p>暂无版本库数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$name = htmlspecialchars((string)($value['name'] ?? ''), ENT_QUOTES, 'UTF-8');
				$branch = htmlspecialchars((string)($value['branch'] ?? 'master'), ENT_QUOTES, 'UTF-8');
				$commitFull = (string)($value['commit'] ?? '');
				$commitShort = $commitFull !== '' ? substr($commitFull, 0, 8) : '--';
				$pending = (int)($value['pending_count'] ?? 0);
				$totalCount = (int)($value['total_count'] ?? 0);
				$gitId = (int)($value['git_id'] ?? 0);
				$status = (int)($value['status'] ?? -1);
			?>
			<div class="git-card item" data-name="<?php echo $name;?>" data-git-id="<?php echo $gitId;?>">
				<div class="card-main">
					<div class="git-info">
						<strong class="git-name"><?php echo $name;?></strong>
						<span class="branch-badge"><i class="fa fa-code-fork"></i> <?php echo $branch;?></span>
						<?php if ($pending > 0) { ?>
						<span class="status-tag tag-warning"><?php echo $pending;?> 条待发布</span>
						<?php } elseif ($status === 1) { ?>
						<span class="status-tag tag-success">已同步发布</span>
						<?php } else { ?>
						<span class="status-tag tag-default"><?php echo htmlspecialchars((string)($value['status_text'] ?? '--'), ENT_QUOTES, 'UTF-8');?></span>
						<?php } ?>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-success btn-xs update-git-btn" data-id="<?php echo $name;?>" title="同步版本库" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-arrows-rotate"></i></button>
						<?php if ($pending > 0 && $gitId > 0) { ?>
						<button type="button" class="btn btn-warning btn-xs release-latest-btn" data-id="<?php echo $gitId;?>" title="发布最新版本" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-paper-plane"></i></button>
						<?php } ?>
						<a href="<?php echo adminUrl('git/gitLog', ['id' => $value['name']]);?>" class="btn btn-primary btn-xs" title="版本日志"><i class="fa fa-rectangle-list"></i></a>
					</div>
				</div>

				<div class="card-body">
					<div class="meta-row">
						<div class="meta-item">
							<span class="meta-label">最新提交：</span>
							<code class="commit-code" title="<?php echo htmlspecialchars($commitFull, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($commitShort, ENT_QUOTES, 'UTF-8');?></code>
						</div>
						<div class="meta-item">
							<span class="meta-label">提交作者：</span>
							<span class="meta-val"><i class="fa fa-user"></i> <?php echo htmlspecialchars((string)($value['author'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
						</div>
					</div>
					<div class="meta-row">
						<div class="meta-item">
							<span class="meta-label">提交统计：</span>
							<span class="meta-val">共 <strong><?php echo $totalCount;?></strong> 条<?php if ($pending > 0) { ?>（<span class="text-warning">待发布 <?php echo $pending;?></span>）<?php } ?></span>
						</div>
					</div>
					<div class="commit-info-box">
						<span class="info-label">提交说明：</span>
						<span class="info-text"><?php echo htmlspecialchars((string)($value['info'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="time-footer">
						<span><i class="fa fa-clock"></i> 提交：<?php echo htmlspecialchars((string)($value['commit_time'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
						<span><i class="fa fa-rocket"></i> 发布：<?php echo htmlspecialchars((string)($value['release_time'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>
