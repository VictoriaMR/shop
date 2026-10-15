<?php
$coreKeys = [
	'redis_version'              => 'Redis 服务版本',
	'redis_mode'                 => '运行模式 (standalone / cluster)',
	'os'                         => '宿主操作系统',
	'process_id'                 => '进程 PID',
	'tcp_port'                   => '监听端口',
	'uptime_in_seconds'          => '连续运行秒数',
	'uptime_in_days'             => '连续运行天数',
	'connected_clients'          => '当前客户端连接数',
	'blocked_clients'            => '阻塞等待客户端数',
	'used_memory_human'          => '当前已用内存',
	'used_memory_peak_human'     => '历史峰值内存',
	'maxmemory_human'            => '最大内存限制',
	'maxmemory_policy'           => '内存淘汰策略',
	'total_connections_received' => '累计接收连接数',
	'total_commands_processed'   => '累计执行命令数',
	'instantaneous_ops_per_sec'  => '实时每秒操作数 (OPS)',
	'keyspace_hits'              => '键命中次数',
	'keyspace_misses'            => '键未命中次数',
	'role'                       => '主从角色 (master / slave)',
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
<div id="sysinfo-redis-page" class="redis-mobile-page">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-bolt"></i>
			<span>Redis 运行信息</span>
		</div>
		<?php if (!empty($info['redis_version'])) { ?>
		<span class="ver-badge">v<?php echo htmlspecialchars((string)$info['redis_version'], ENT_QUOTES, 'UTF-8');?></span>
		<?php } ?>
	</div>

	<?php if (empty($info) || !is_array($info)) { ?>
	<div class="empty-state">
		<i class="fa fa-plug-circle-xmark"></i>
		<p>Redis 连接不正常或服务未启动</p>
	</div>
	<?php } else { ?>
	<!-- 筛选与检索 -->
	<div class="filter-box">
		<div class="redis-tab-filter">
			<button type="button" data-sec="all" class="filter-tab active">全部</button>
			<button type="button" data-sec="core" class="filter-tab">核心指标</button>
			<button type="button" data-sec="keyspace" class="filter-tab">键空间</button>
			<button type="button" data-sec="detail" class="filter-tab">完整参数</button>
		</div>
		<input type="text" id="redis-keyword-input" class="form-control" placeholder="输入参数名或值快速过滤..." autocomplete="off">
	</div>

	<!-- 核心运行指标 -->
	<div class="section-card redis-section" data-section="core">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-gauge-high"></i> 核心运行指标</div>
		</div>
		<div class="section-body">
			<?php foreach ($coreKeys as $k => $desc) {
				if (!isset($info[$k])) continue;
			?>
			<div class="kv-card">
				<div class="kv-top">
					<span class="kv-key"><?php echo htmlspecialchars($k, ENT_QUOTES, 'UTF-8');?></span>
					<span class="kv-desc"><?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="kv-val"><code><?php echo htmlspecialchars((string)$info[$k], ENT_QUOTES, 'UTF-8');?></code></div>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- 数据库键空间统计 -->
	<?php if (!empty($keyspaceRows)) { ?>
	<div class="section-card redis-section" data-section="keyspace">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-hard-drive"></i> 数据库键空间统计 (Keyspace)</div>
			<span class="section-count"><?php echo count($keyspaceRows);?> 库</span>
		</div>
		<div class="section-body">
			<?php foreach ($keyspaceRows as $dbName => $dbStat) { ?>
			<div class="kv-card">
				<div class="kv-top">
					<span class="kv-key"><?php echo htmlspecialchars((string)$dbName, ENT_QUOTES, 'UTF-8');?></span>
					<span class="kv-desc">键数量 / 过期键统计</span>
				</div>
				<div class="kv-val"><code><?php echo htmlspecialchars((string)$dbStat, ENT_QUOTES, 'UTF-8');?></code></div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<!-- 完整配置与状态详情 -->
	<div class="section-card redis-section" data-section="detail">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-list-check"></i> 完整配置与状态详情</div>
			<span class="section-count">共 <?php echo count($info);?> 项</span>
		</div>
		<div class="section-body">
			<?php foreach ($info as $key => $val) {
				if (isset($coreKeys[$key]) || isset($keyspaceRows[$key])) continue;
			?>
			<div class="kv-card compact">
				<div class="kv-key"><code><?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?></code></div>
				<div class="kv-val"><?php echo htmlspecialchars(is_scalar($val) ? (string)$val : json_encode($val, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?></div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>
