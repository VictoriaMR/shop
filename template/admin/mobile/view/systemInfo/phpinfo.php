<div id="phpinfo-page" class="phpinfo-mobile-page">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-code"></i>
			<span>PHP 环境信息 (phpinfo)</span>
		</div>
		<span class="ver-badge">PHP <?php echo htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8');?></span>
	</div>

	<!-- 检索过滤栏 -->
	<div class="filter-box">
		<input type="text" id="phpinfo-keyword-input" class="form-control" placeholder="输入配置指令或扩展模块名称检索..." autocomplete="off">
	</div>

	<!-- phpinfo 内容卡片区 -->
	<div id="phpinfo-content" class="phpinfo-card-wrap">
		<?php echo $phpinfoHtml ?? '';?>
	</div>
</div>
