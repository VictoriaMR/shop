<div class="container-fluid" id="site-log-page">
	<div class="row">
		<div class="col-xs-12">
			<div class="btn-group left log-type-filter" role="group">
				<button type="button" data-type="all" class="btn btn-primary btn-sm">全部</button>
				<button type="button" data-type="request" class="btn btn-default btn-sm">请求日志</button>
				<button type="button" data-type="error" class="btn btn-default btn-sm">错误日志</button>
				<button type="button" data-type="task" class="btn btn-default btn-sm">任务日志</button>
			</div>
			<span class="text-muted ml12 pt6 left">共 <strong><?php echo count($list ?? []);?></strong> 个日志文件</span>
			<?php if (!empty($list)) { ?>
			<div class="right">
				<button class="btn btn-danger btn-sm delete" data-id="all" type="button"><i class="glyphicon glyphicon-trash"></i> 清除全部日志</button>
			</div>
			<?php } ?>
			<div class="clear"></div>
		</div>
	</div>

	<table class="table table-hover table-middle mt12" id="data-list">
		<thead>
			<tr>
				<th>日志文件路径</th>
				<th width="120" class="tc">日志类型</th>
				<th width="130">文件大小</th>
				<th width="170">最后写入时间</th>
				<th width="150">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)) { ?>
			<tr>
				<td colspan="5">
					<div class="tc orange"><i class="glyphicon glyphicon-info-sign"></i> 暂无运行日志</div>
				</td>
			</tr>
			<?php } else { foreach ($list as $value) {
				$logType = $value['log_type'] ?? 'request';
			?>
			<tr class="log-row" data-type="<?php echo htmlspecialchars($logType);?>">
				<td><code><?php echo htmlspecialchars($value['name']);?></code></td>
				<td class="tc">
					<?php if ($logType === 'error') { ?>
					<span class="label label-danger">错误日志</span>
					<?php } elseif ($logType === 'task') { ?>
					<span class="label label-warning">任务日志</span>
					<?php } else { ?>
					<span class="label label-info">请求日志</span>
					<?php } ?>
				</td>
				<td><?php echo sprintf('%.2f', ($value['size'] ?? 0) / 1024) . ' KB';?></td>
				<td class="text-muted f12"><?php echo htmlspecialchars($value['c_time'] ?? '--');?></td>
				<td>
					<a href="<?php echo adminUrl('site/logDetail', ['name' => $value['name']]);?>" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-eye-open"></i> 查看</a>
					<button class="btn btn-danger btn-xs delete ml6" type="button" data-id="<?php echo htmlspecialchars($value['name']);?>"><i class="glyphicon glyphicon-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>
</div>