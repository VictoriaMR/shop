<div id="static-cache-page" class="static-cache-mobile-page" data-url="<?php echo adminUrl('site/staticCache');?>">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-bolt"></i>
			<span>静态文件管理</span>
			<span class="total-badge">共 <?php echo count($list ?? []);?> 个</span>
		</div>
		<?php if (!empty($list)) { ?>
		<button class="btn btn-danger btn-xs delete" data-id="all" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
			<i class="fa fa-trash-can"></i> 清除全部
		</button>
		<?php } ?>
	</div>

	<!-- 类型快速筛选 -->
	<div class="filter-box">
		<div class="ext-type-filter">
			<button type="button" data-ext="all" class="filter-tab active">全部</button>
			<button type="button" data-ext="css" class="filter-tab">CSS 样式</button>
			<button type="button" data-ext="js" class="filter-tab">JS 脚本</button>
		</div>
	</div>

	<!-- 缓存卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
		<div class="empty-state">
			<i class="fa fa-folder-open"></i>
			<p>暂无静态缓存文件</p>
		</div>
		<?php } else { foreach ($list as $value) {
			$ext = strtolower($value['ext'] ?? pathinfo($value['name'], PATHINFO_EXTENSION));
		?>
		<div class="static-card" data-ext="<?php echo htmlspecialchars($ext, ENT_QUOTES, 'UTF-8');?>">
			<div class="card-header">
				<div class="static-title-wrap">
					<span class="ext-badge <?php echo $ext === 'css' ? 'css' : ($ext === 'js' ? 'js' : 'file');?>"><?php echo htmlspecialchars(strtoupper($ext ?: 'FILE'), ENT_QUOTES, 'UTF-8');?></span>
					<code class="file-name"><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');?></code>
				</div>
				<div class="card-actions">
					<a href="<?php echo adminUrl('site/staticDetail', ['name' => $value['name']]);?>" class="btn btn-primary btn-icon" title="查看缓存详情"><i class="fa fa-eye"></i></a>
					<button class="btn btn-danger btn-icon delete" type="button" data-id="<?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');?>" title="删除缓存文件" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
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
