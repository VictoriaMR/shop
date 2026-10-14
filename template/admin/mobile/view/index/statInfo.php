<div class="stat-container">
	<div class="stat-header">
		<h2><i class="fa fa-dashboard" style="color: #3b82f6;"></i> 系统概览 & 性能监控</h2>
		<p>实时监控服务器运行状态、系统参数配置及硬件资源使用情况</p>
	</div>

	<!-- 实时核心指标网格 -->
	<div class="metrics-grid">
		<div class="metric-card">
			<div class="title">
				<span>CPU 使用率</span>
				<i class="fa fa-microchip" style="color: #3b82f6;"></i>
			</div>
			<div class="value" id="loadpercentage"><?php echo $cpuInfo['loadpercentage'] ?? '0%';?></div>
			<div class="metric-progress">
				<div class="metric-progress-bar" id="loadpercentage_bar" style="width: <?php echo floatval($cpuInfo['loadpercentage'] ?? 0);?>%; background: #3b82f6;"></div>
			</div>
		</div>

		<div class="metric-card">
			<div class="title">
				<span>内存使用率</span>
				<i class="fa fa-pie-chart" style="color: #10b981;"></i>
			</div>
			<div class="value" id="memory_used_rate"><?php echo $cpuInfo['memory_used_rate'] ?? '0%';?></div>
			<div class="metric-progress">
				<div class="metric-progress-bar" id="memory_used_rate_bar" style="width: <?php echo floatval($cpuInfo['memory_used_rate'] ?? 0);?>%; background: #10b981;"></div>
			</div>
			<div class="sub-info">已用: <span id="memory_used"><?php echo $cpuInfo['memory_used'] ?? '0M';?></span> / <span id="memory_total"><?php echo $cpuInfo['memory_total'] ?? '0M';?></span></div>
		</div>

		<div class="metric-card">
			<div class="title">
				<span>内存剩余</span>
				<i class="fa fa-server" style="color: #8b5cf6;"></i>
			</div>
			<div class="value" id="memory_free"><?php echo $cpuInfo['memory_free'] ?? '0M';?></div>
			<div class="sub-info">运行状态良好</div>
		</div>

		<div class="metric-card">
			<div class="title">
				<span>磁盘使用量</span>
				<i class="fa fa-database" style="color: #f59e0b;"></i>
			</div>
			<div class="value" id="disk_used_space"><?php echo $cpuInfo['disk_used_space'] ?? '0M';?></div>
			<div class="sub-info">总计: <span id="disk_total_space"><?php echo $cpuInfo['disk_total_space'] ?? '0M';?></span> (余 <span id="disk_free_space"><?php echo $cpuInfo['disk_free_space'] ?? '0M';?></span>)</div>
		</div>
	</div>

	<!-- 详细信息卡片 -->
	<div class="info-grid">
		<div class="info-card">
			<div class="info-card-header">
				<h3><i class="fa fa-globe"></i> 网站基本信息</h3>
			</div>
			<div class="info-card-body">
				<div class="data-list">
					<div class="data-item">
						<span class="data-label">操作系统</span>
						<span class="data-value badge-tech"><?php echo php_uname('s') . ' ' . php_uname('r');?></span>
					</div>
					<div class="data-item">
						<span class="data-label">服务器版本</span>
						<span class="data-value"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? '', ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="data-item">
						<span class="data-label">服务器域名</span>
						<span class="data-value"><?php echo htmlspecialchars($_SERVER['SERVER_NAME'] ?? '', ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="data-item">
						<span class="data-label">地址 / 端口</span>
						<span class="data-value badge-tech badge-purple"><?php echo htmlspecialchars(($_SERVER['SERVER_ADDR'] ?? '') . ':' . ($_SERVER['SERVER_PORT'] ?? ''), ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="data-item">
						<span class="data-label">PHP 版本</span>
						<span class="data-value badge-tech"><?php echo PHP_VERSION;?></span>
					</div>
					<div class="data-item">
						<span class="data-label">PHP 运行方式</span>
						<span class="data-value"><?php echo php_sapi_name();?></span>
					</div>
					<div class="data-item">
						<span class="data-label">MySQL 版本</span>
						<span class="data-value badge-tech badge-green"><?php echo htmlspecialchars((string)$mysqlVersion, ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="data-item">
						<span class="data-label">最大执行时间</span>
						<span class="data-value"><?php echo get_cfg_var('max_execution_time') . 's';?></span>
					</div>
					<div class="data-item">
						<span class="data-label">内存限制</span>
						<span class="data-value"><?php echo get_cfg_var('memory_limit');?></span>
					</div>
				</div>
			</div>
		</div>

		<div class="info-card">
			<div class="info-card-header">
				<h3><i class="fa fa-cogs"></i> 服务器硬件 & 资源配置</h3>
			</div>
			<div class="info-card-body">
				<div class="data-list">
					<?php if (!empty($cpuInfo['Name'])) {?>
					<div class="data-item">
						<span class="data-label">CPU 型号</span>
						<span class="data-value"><?php echo htmlspecialchars($cpuInfo['Name'], ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<?php } ?>
					<?php if (!empty($cpuInfo['NumberOfCores'])) {?>
					<div class="data-item">
						<span class="data-label">CPU 核心数</span>
						<span class="data-value badge-tech"><?php echo (int)$cpuInfo['NumberOfCores'];?> 核</span>
					</div>
					<?php } ?>
					<div class="data-item">
						<span class="data-label">CPU 使用率</span>
						<span class="data-value" style="color: #3b82f6; font-weight: 700;" id="loadpercentage_detail"><?php echo $cpuInfo['loadpercentage'] ?? '0%';?></span>
					</div>
					<div class="data-item">
						<span class="data-label">物理内存总量</span>
						<span class="data-value" id="memory_total_detail"><?php echo $cpuInfo['memory_total'] ?? '0M';?></span>
					</div>
					<div class="data-item">
						<span class="data-label">已使用内存</span>
						<span class="data-value" id="memory_used_detail"><?php echo $cpuInfo['memory_used'] ?? '0M';?></span>
					</div>
					<div class="data-item">
						<span class="data-label">空闲内存</span>
						<span class="data-value badge-tech badge-green" id="memory_free_detail"><?php echo $cpuInfo['memory_free'] ?? '0M';?></span>
					</div>
					<div class="data-item">
						<span class="data-label">磁盘总容量</span>
						<span class="data-value" id="disk_total_space_detail"><?php echo $cpuInfo['disk_total_space'] ?? '0M';?></span>
					</div>
					<div class="data-item">
						<span class="data-label">已用磁盘空间</span>
						<span class="data-value" id="disk_used_space_detail"><?php echo $cpuInfo['disk_used_space'] ?? '0M';?></span>
					</div>
					<div class="data-item">
						<span class="data-label">空闲磁盘空间</span>
						<span class="data-value badge-tech badge-green" id="disk_free_space_detail"><?php echo $cpuInfo['disk_free_space'] ?? '0M';?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
