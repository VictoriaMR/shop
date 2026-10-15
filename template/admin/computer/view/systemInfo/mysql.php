<?php
$statDescMap = [
	'Uptime'                 => '服务连续运行时长 (秒)',
	'Threads'                => '当前活跃连接线程数',
	'Questions'              => '启动以来执行的查询总数',
	'Slow queries'           => '慢查询累计次数',
	'Opens'                  => '累计已打开的数据表数量',
	'Flush tables'           => 'Flush Tables 执行次数',
	'Open tables'            => '当前正打开的数据表数量',
	'Queries per second avg' => '平均每秒查询率 (QPS)',
];
?>
<div class="container-fluid">
	<table class="table table-hover table-middle">
		<thead>
			<tr>
				<th width="280">参数名称</th>
				<th>当前值</th>
				<th width="260">参数说明</th>
			</tr>
		</thead>
		<tbody>
			<tr class="info">
				<td colspan="3"><strong><i class="fa fa-database"></i> 版本与基础信息</strong></td>
			</tr>
			<tr>
				<td><strong>Server Version</strong></td>
				<td><code><?php echo htmlspecialchars($info['server'] ?? '--');?></code></td>
				<td class="text-muted">MySQL 服务端版本</td>
			</tr>
			<tr>
				<td><strong>Client Version</strong></td>
				<td><code><?php echo htmlspecialchars($info['client'] ?? '--');?></code></td>
				<td class="text-muted">PHP Mysqli 客户端库版本</td>
			</tr>

			<?php if (!empty($info['stat'])){ ?>
			<tr class="info">
				<td colspan="3"><strong><i class="fa fa-line-chart"></i> 运行状态统计 (Stat)</strong></td>
			</tr>
			<?php foreach ($info['stat'] as $val) {
				$sName = $val[0] ?? '';
				$sVal = $val[1] ?? '';
				$desc = $statDescMap[$sName] ?? '运行状态指标';
				if ($sName === 'Uptime' && is_numeric($sVal)) {
					$sec = (int)$sVal;
					$days = floor($sec / 86400);
					$hours = floor(($sec % 86400) / 3600);
					$mins = floor(($sec % 3600) / 60);
					$sVal .= ' (' . ($days > 0 ? $days . '天 ' : '') . $hours . '小时 ' . $mins . '分)';
				}
			?>
			<tr>
				<td><strong><?php echo htmlspecialchars($sName);?></strong></td>
				<td><code><?php echo htmlspecialchars($sVal);?></code></td>
				<td class="text-muted"><?php echo htmlspecialchars($desc);?></td>
			</tr>
			<?php } ?>
			<?php } ?>

			<?php if (!empty($info['other'])){ ?>
			<tr class="info">
				<td colspan="3"><strong><i class="fa fa-cogs"></i> 系统变量配置 (Variables)</strong></td>
			</tr>
			<?php foreach ($info['other'] as $key => $val) { ?>
			<tr>
				<td><code><?php echo htmlspecialchars((string)$key);?></code></td>
				<td colspan="2"><?php echo $val !== '' ? htmlspecialchars((string)$val) : '<span class="text-muted">--</span>';?></td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>