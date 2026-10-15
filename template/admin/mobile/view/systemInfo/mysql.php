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
<div id="sysinfo-mysql-page" class="mysql-mobile-page">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-database"></i>
			<span>MySQL 运行信息</span>
		</div>
		<span class="ver-badge"><?php echo htmlspecialchars($info['server'] ?? '--', ENT_QUOTES, 'UTF-8');?></span>
	</div>

	<!-- 快速检索框 -->
	<div class="filter-box">
		<input type="text" id="mysql-keyword-input" class="form-control" placeholder="输入参数名称或值检索..." autocomplete="off">
	</div>

	<!-- 版本与基础信息 -->
	<div class="section-card mysql-section">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-circle-info"></i> 版本与基础信息</div>
		</div>
		<div class="section-body">
			<div class="kv-card">
				<div class="kv-top">
					<span class="kv-key">Server Version</span>
					<span class="kv-desc">MySQL 服务端版本</span>
				</div>
				<div class="kv-val"><code><?php echo htmlspecialchars($info['server'] ?? '--', ENT_QUOTES, 'UTF-8');?></code></div>
			</div>
			<div class="kv-card">
				<div class="kv-top">
					<span class="kv-key">Client Version</span>
					<span class="kv-desc">PHP Mysqli 客户端库版本</span>
				</div>
				<div class="kv-val"><code><?php echo htmlspecialchars($info['client'] ?? '--', ENT_QUOTES, 'UTF-8');?></code></div>
			</div>
		</div>
	</div>

	<!-- 运行状态统计 (Stat) -->
	<?php if (!empty($info['stat'])) { ?>
	<div class="section-card mysql-section">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-chart-line"></i> 运行状态统计 (Stat)</div>
			<span class="section-count"><?php echo count($info['stat']);?> 项</span>
		</div>
		<div class="section-body">
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
			<div class="kv-card">
				<div class="kv-top">
					<span class="kv-key"><?php echo htmlspecialchars($sName, ENT_QUOTES, 'UTF-8');?></span>
					<span class="kv-desc"><?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="kv-val"><code><?php echo htmlspecialchars($sVal, ENT_QUOTES, 'UTF-8');?></code></div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<!-- 系统变量配置 (Variables) -->
	<?php if (!empty($info['other'])) { ?>
	<div class="section-card mysql-section">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-gears"></i> 系统变量配置 (Variables)</div>
			<span class="section-count"><?php echo count($info['other']);?> 项</span>
		</div>
		<div class="section-body">
			<?php foreach ($info['other'] as $key => $val) { ?>
			<div class="kv-card compact">
				<div class="kv-key"><code><?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?></code></div>
				<div class="kv-val"><?php echo $val !== '' ? htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8') : '<span class="text-muted">--</span>';?></div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>
