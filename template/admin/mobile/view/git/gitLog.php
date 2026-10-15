<?php
$curId = $id ?? '';
$curStatus = isset($status) ? (int)$status : -1;
$curKeyword = $keyword ?? '';
$defaultLib = $curId !== '' ? $curId : ($git_library[0] ?? 'shop');
?>
<div id="git-log-page" class="git-log-page" data-url="<?php echo adminUrl('git/gitLog');?>">
	<!-- 顶部标题栏 + 同步按钮 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-clock-rotate-left"></i>
			<span>Git版本日志</span>
			<span class="total-badge">共 <?php echo (int)$total;?> 条</span>
		</div>
		<button id="update-git" type="button" class="btn btn-success btn-sm" data-id="<?php echo htmlspecialchars((string)$defaultLib, ENT_QUOTES, 'UTF-8');?>" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 同步中">
			<i class="fa fa-arrows-rotate"></i> 同步 (<?php echo htmlspecialchars((string)$defaultLib, ENT_QUOTES, 'UTF-8');?>)
		</button>
	</div>

	<!-- 筛选栏（无重置按钮） -->
	<div class="filter-box">
		<form action="<?php echo adminUrl('git/gitLog');?>" method="get" class="search-form">
			<input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$curId, ENT_QUOTES, 'UTF-8');?>">
			<input type="hidden" name="status" value="<?php echo $curStatus;?>">

			<div class="filter-group lib-filter">
				<button type="button" data-id="" class="btn btn-xs <?php echo $curId === '' ? 'btn-primary' : 'btn-default';?>">全部库</button>
				<?php foreach ($git_library ?? [] as $value) { ?>
				<button type="button" data-id="<?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');?>" class="btn btn-xs <?php echo $curId === $value ? 'btn-primary' : 'btn-default';?>"><?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');?></button>
				<?php } ?>
			</div>

			<div class="filter-group status-filter">
				<button type="button" data-status="-1" class="btn btn-xs <?php echo ($curStatus === 1 || $curStatus === 0) ? 'btn-default' : 'btn-primary';?>">全部状态</button>
				<button type="button" data-status="1" class="btn btn-xs <?php echo $curStatus === 1 ? 'btn-primary' : 'btn-default';?>">已发布</button>
				<button type="button" data-status="0" class="btn btn-xs <?php echo $curStatus === 0 ? 'btn-primary' : 'btn-default';?>">待发布</button>
			</div>

			<div class="filter-row">
				<div class="filter-item flex-1">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars((string)$curKeyword, ENT_QUOTES, 'UTF-8');?>" placeholder="版本号 / 作者 / 提交信息" autocomplete="off">
				</div>
				<button class="btn btn-info search-submit-btn" type="submit"><i class="fa fa-magnifying-glass"></i> 查询</button>
			</div>
		</form>
	</div>

	<!-- 日志卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>暂无版本日志记录</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$gitId = (int)$value['git_id'];
				$commitFull = (string)($value['commit'] ?? '');
				$commitShort = $commitFull !== '' ? substr($commitFull, 0, 10) : '--';
				$isReleased = !empty($value['status']);
			?>
			<div class="log-card item" data-id="<?php echo $gitId;?>">
				<div class="card-main">
					<div class="log-info">
						<span class="id-badge">#<?php echo $gitId;?></span>
						<span class="lib-tag"><?php echo htmlspecialchars((string)($value['library'] ?? ''), ENT_QUOTES, 'UTF-8');?></span>
						<code class="commit-code" title="完整版本号: <?php echo htmlspecialchars($commitFull, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($commitShort, ENT_QUOTES, 'UTF-8');?></code>
						<?php if ($isReleased) { ?>
						<span class="status-tag tag-success">已发布</span>
						<?php } else { ?>
						<span class="status-tag tag-warning">待发布</span>
						<?php } ?>
					</div>
					<?php if (!$isReleased) { ?>
					<div class="card-actions">
						<button type="button" class="btn btn-primary btn-xs release-btn" title="发布该版本" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-paper-plane"></i></button>
					</div>
					<?php } ?>
				</div>

				<div class="commit-msg">
					<?php echo htmlspecialchars((string)($value['info'] ?: '--'), ENT_QUOTES, 'UTF-8');?>
				</div>

				<div class="commit-meta">
					<span><i class="fa fa-user"></i> <?php echo htmlspecialchars((string)($value['author'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
					<span>提交: <?php echo htmlspecialchars((string)($value['commit_time'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
					<?php if ($isReleased && !empty($value['release_time'])) { ?>
					<span>发布: <?php echo htmlspecialchars((string)$value['release_time'], ENT_QUOTES, 'UTF-8');?></span>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页组件 -->
	<?php echo page($size, $total);?>
</div>
