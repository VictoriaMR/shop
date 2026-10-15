<div class="container-fluid">
	<div>
		<a href="<?php echo adminUrl('site/siteLog');?>" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-menu-left"></i> 返回日志列表</a>
		<span class="f16 ml12 mt4"><b>日志文件详情</b></span>
	</div>
	<div class="panel panel-default mt10">
		<div class="panel-heading">
			<span>日志文件：<code><?php echo htmlspecialchars($name ?? '');?></code></span>
			<span class="right text-muted f12">共 <b><?php echo count($list ?? []);?></b> 条日志分块</span>
		</div>
		<table class="table table-hover table-middle">
			<thead>
				<tr>
					<th width="70" class="tc">序号</th>
					<th>日志内容明细</th>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($list)) { ?>
				<tr>
					<td colspan="2" class="tc orange">日志文件内容为空或文件不存在</td>
				</tr>
				<?php } else { foreach ($list as $idx => $item) {
					$text = trim($item);
					if ($text === '') continue;
				?>
				<tr>
					<td class="tc"><b>#<?php echo $idx + 1;?></b></td>
					<td><pre class="m0"><?php echo htmlspecialchars($text);?></pre></td>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	</div>
</div>