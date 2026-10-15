<div class="container-fluid">
	<div>
		<a href="<?php echo adminUrl('site/staticCache');?>" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-menu-left"></i> 返回静态缓存列表</a>
		<span class="f16 ml12 mt4"><b>静态文件详情</b></span>
	</div>
	<div class="panel panel-default mt10">
		<div class="panel-heading">
			<span>文件路径：<code><?php echo htmlspecialchars($name ?? '');?></code></span>
			<span class="right text-muted f12">字符长度：<?php echo strlen($content ?? '');?> Bytes</span>
		</div>
		<div class="panel-body">
			<?php if (($content ?? '') === '') { ?>
			<div class="tc orange">文件内容为空或文件不存在</div>
			<?php } else { ?>
			<pre class="m0"><?php echo htmlspecialchars($content);?></pre>
			<?php } ?>
		</div>
	</div>
</div>