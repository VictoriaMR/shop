<?php
$validList = [];
if (!empty($list) && is_array($list)) {
	foreach ($list as $item) {
		$t = trim((string)$item);
		if ($t !== '') {
			$validList[] = $t;
		}
	}
}
?>
<div id="log-detail-page" class="log-detail-mobile-page">
	<!-- 顶部返回与标题栏 -->
	<div class="page-header-bar">
		<a href="<?php echo adminUrl('site/siteLog');?>" class="btn btn-default btn-xs back-btn">
			<i class="fa fa-angle-left"></i> 返回日志列表
		</a>
		<span class="total-badge">共 <?php echo count($validList);?> 条分块</span>
	</div>

	<!-- 文件信息与关键词过滤 -->
	<div class="file-meta-card">
		<div class="file-path-row">
			<i class="fa fa-file-code"></i>
			<code><?php echo htmlspecialchars($name ?? '--', ENT_QUOTES, 'UTF-8');?></code>
		</div>
		<?php if (!empty($validList)) { ?>
		<div class="filter-input-wrap">
			<input type="text" id="log-keyword-input" class="form-control" placeholder="输入关键词过滤日志内容..." autocomplete="off">
		</div>
		<?php } ?>
	</div>

	<!-- 日志分块列表 -->
	<div class="chunk-list" id="log-chunk-list">
		<?php if (empty($validList)) { ?>
		<div class="empty-state">
			<i class="fa fa-folder-open"></i>
			<p>日志文件内容为空或文件不存在</p>
		</div>
		<?php } else { foreach ($validList as $idx => $text) { ?>
		<div class="chunk-card">
			<div class="chunk-header">
				<span class="chunk-idx">#<?php echo $idx + 1;?></span>
				<button type="button" class="btn btn-default btn-icon copy-chunk-btn" title="复制日志内容">
					<i class="fa fa-copy"></i>
				</button>
			</div>
			<pre class="chunk-pre"><?php echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');?></pre>
		</div>
		<?php } } ?>
	</div>
</div>
