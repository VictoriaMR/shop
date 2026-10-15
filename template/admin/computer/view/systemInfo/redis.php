<?php
$coreKeys = [
	'redis_version'            => 'Redis 服务版本',
	'redis_mode'               => '运行模式 (standalone / cluster)',
	'os'                       => '宿主操作系统',
	'process_id'               => '进程 PID',
	'tcp_port'                 => '监听端口',
	'uptime_in_seconds'        => '连续运行秒数',
	'uptime_in_days'           => '连续运行天数',
	'connected_clients'        => '当前客户端连接数',
	'blocked_clients'          => '阻塞等待客户端数',
	'used_memory_human'        => '当前已用内存',
	'used_memory_peak_human'   => '历史峰值内存',
	'maxmemory_human'          => '最大内存限制',
	'maxmemory_policy'         => '内存淘汰策略',
	'total_connections_received' => '累计接收连接数',
	'total_commands_processed' => '累计执行命令数',
	'instantaneous_ops_per_sec'=> '实时每秒操作数 (OPS)',
	'keyspace_hits'            => '键命中次数',
	'keyspace_misses'          => '键未命中次数',
	'role'                     => '主从角色 (master / slave)',
];

$keyspaceRows = [];
if (!empty($info) && is_array($info)) {
	foreach ($info as $k => $v) {
		if (preg_match('/^db\d+$/', (string)$k)) {
			$keyspaceRows[$k] = $v;
		}
	}
}
?>
<div class="container-fluid">
	<?php if (empty($info) || !is_array($info)){ ?>
	<table class="table table-hover table-middle">
		<tbody>
			<tr>
				<td class="tc orange"><i class="glyphicon glyphicon-info-sign"></i> Redis 连接不正常或服务未启动</td>
			</tr>
		</tbody>
	</table>
	<?php } else { ?>
	<table class="table table-hover table-middle">
		<thead>
			<tr>
				<th width="280">参数名称</th>
				<th>当前值</th>
				<th width="260">说明</th>
			</tr>
		</thead>
		<tbody>
			<tr class="info">
				<td colspan="3"><strong><i class="fa fa-tachometer"></i> 核心运行指标</strong></td>
			</tr>
			<?php foreach ($coreKeys as $k => $desc) {
				if (!isset($info[$k])) continue;
			?>
			<tr>
				<td><strong><?php echo htmlspecialchars($k);?></strong></td>
				<td><code><?php echo htmlspecialchars((string)$info[$k]);?></code></td>
				<td class="text-muted"><?php echo htmlspecialchars($desc);?></td>
			</tr>
			<?php } ?>

			<?php if (!empty($keyspaceRows)){ ?>
			<tr class="info">
				<td colspan="3"><strong><i class="fa fa-hdd-o"></i> 数据库键空间统计 (Keyspace)</strong></td>
			</tr>
			<?php foreach ($keyspaceRows as $dbName => $dbStat) { ?>
			<tr>
				<td><strong><?php echo htmlspecialchars($dbName);?></strong></td>
				<td><code><?php echo htmlspecialchars((string)$dbStat);?></code></td>
				<td class="text-muted">键数量 / 过期键统计</td>
			</tr>
			<?php } ?>
			<?php } ?>

			<tr class="info">
				<td colspan="3"><strong><i class="fa fa-list-alt"></i> 完整配置与状态详情 (共 <?php echo count($info);?> 项)</strong></td>
			</tr>
			<?php foreach ($info as $key => $val) {
				if (isset($coreKeys[$key]) || isset($keyspaceRows[$key])) continue;
			?>
			<tr>
				<td><code><?php echo htmlspecialchars((string)$key);?></code></td>
				<td colspan="2"><?php echo htmlspecialchars(is_scalar($val) ? (string)$val : json_encode($val, JSON_UNESCAPED_UNICODE));?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php } ?>
</div>