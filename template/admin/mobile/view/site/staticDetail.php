<div id="static-detail-page" class="static-detail-mobile-page">
	<!-- 顶部返回与标题栏 -->
	<div class="page-header-bar">
		<a href="<?php echo adminUrl('site/staticCache');?>" class="btn btn-default btn-xs back-btn">
			<i class="fa fa-angle-left"></i> 返回静态缓存列表
		</a>
		<span class="total-badge"><?php echo strlen($content ?? '');?> Bytes</span>
	</div>

	<!-- 内容详情卡片 -->
	<div class="detail-card">
		<div class="detail-header">
			<div class="file-path-wrap">
				<i class="fa fa-file-code"></i>
				<code><?php echo htmlspecialchars($name ?? '--', ENT_QUOTES, 'UTF-8');?></code>
			</div>
			<?php if (($content ?? '') !== '') { ?>
			<button type="button" class="btn btn-default btn-icon" id="copy-static-btn" title="复制文件内容">
				<i class="fa fa-copy"></i>
			</button>
			<?php } ?>
		</div>
		<div class="detail-body">
			<?php if (($content ?? '') === '') { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>文件内容为空或文件不存在</p>
			</div>
			<?php } else { ?>
			<pre class="static-pre" id="static-code-content"><?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8');?></pre>
			<?php } ?>
		</div>
	</div>
</div>
