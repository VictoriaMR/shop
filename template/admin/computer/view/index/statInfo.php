<div class="stat-container">
	<style>
		.stat-container {
			padding: 24px;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
			color: #334155;
			background-color: #f8fafc;
			min-height: 100vh;
			box-sizing: border-box;
		}
		.stat-header {
			margin-bottom: 24px;
		}
		.stat-header h2 {
			font-size: 20px;
			font-weight: 700;
			color: #0f172a;
			margin: 0 0 6px 0;
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.stat-header p {
			font-size: 13px;
			color: #64748b;
			margin: 0;
		}
		/* Dynamic Metrics Top Grid */
		.metrics-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
			gap: 16px;
			margin-bottom: 24px;
		}
		.metric-card {
			background: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 12px;
			padding: 18px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
			transition: all 0.2s ease-in-out;
		}
		.metric-card:hover {
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
			transform: translateY(-2px);
		}
		.metric-card .title {
			font-size: 13px;
			color: #64748b;
			font-weight: 500;
			margin-bottom: 10px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.metric-card .value {
			font-size: 24px;
			font-weight: 700;
			color: #0f172a;
			letter-spacing: -0.5px;
		}
		.metric-card .sub-info {
			font-size: 12px;
			color: #94a3b8;
			margin-top: 8px;
		}
		.metric-progress {
			height: 6px;
			background: #f1f5f9;
			border-radius: 3px;
			overflow: hidden;
			margin-top: 10px;
		}
		.metric-progress-bar {
			height: 100%;
			border-radius: 3px;
			transition: width 0.5s ease;
		}
		/* Two-Column Info Cards Grid */
		.info-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
			gap: 20px;
		}
		.info-card {
			background: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 12px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
			overflow: hidden;
		}
		.info-card-header {
			padding: 16px 20px;
			border-bottom: 1px solid #f1f5f9;
			background: #ffffff;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		.info-card-header h3 {
			font-size: 15px;
			font-weight: 600;
			color: #0f172a;
			margin: 0;
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.info-card-header h3 i {
			color: #3b82f6;
		}
		.info-card-body {
			padding: 16px 20px;
		}
		/* Data Rows */
		.data-list {
			display: flex;
			flex-direction: column;
			gap: 8px;
		}
		.data-item {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 10px 14px;
			background: #f8fafc;
			border-radius: 8px;
			transition: background 0.15s ease;
		}
		.data-item:hover {
			background: #f1f5f9;
		}
		.data-label {
			font-size: 13px;
			color: #64748b;
			font-weight: 500;
		}
		.data-value {
			font-size: 13px;
			color: #0f172a;
			font-weight: 600;
			word-break: break-all;
			text-align: right;
		}
		.badge-tech {
			display: inline-block;
			padding: 3px 8px;
			border-radius: 6px;
			background: #eff6ff;
			color: #2563eb;
			font-size: 12px;
			font-weight: 500;
			font-family: SFMono-Regular, Consolas, "Liberation Mono", Menlo, monospace;
		}
		.badge-green {
			background: #ecfdf5;
			color: #059669;
		}
		.badge-purple {
			background: #f5f3ff;
			color: #7c3aed;
		}
	</style>

	<div class="stat-header">
		<h2><i class="fa fa-dashboard" style="color: #3b82f6;"></i> 系统概览 & 性能监控</h2>
		<p>实时监控服务器运行状态、系统参数配置及硬件资源使用情况</p>
	</div>

	<!-- Dynamic Realtime Metrics Cards -->
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
			<div class="sub-info">已用: <span id="memory_used"><?php echo $cpuInfo['memory_used'] ?? '0M';?></span> / 总计: <span id="memory_total"><?php echo $cpuInfo['memory_total'] ?? '0M';?></span></div>
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
			<div class="sub-info">总容量: <span id="disk_total_space"><?php echo $cpuInfo['disk_total_space'] ?? '0M';?></span> (剩余: <span id="disk_free_space"><?php echo $cpuInfo['disk_free_space'] ?? '0M';?></span>)</div>
		</div>
	</div>

	<!-- Info Cards Grid -->
	<div class="info-grid">
		<!-- Website & Runtime Info -->
		<div class="info-card">
			<div class="info-card-header">
				<h3><i class="fa fa-globe"></i> 网站基本信息</h3>
			</div>
			<div class="info-card-body">
				<div class="data-list">
					<div class="data-item">
						<span class="data-label">操作系统</span>
						<span class="data-value badge-tech"><?php echo php_uname('s').' '.php_uname('r');?></span>
					</div>
					<div class="data-item">
						<span class="data-label">服务器版本</span>
						<span class="data-value"><?php echo $_SERVER['SERVER_SOFTWARE'];?></span>
					</div>
					<div class="data-item">
						<span class="data-label">服务器域名</span>
						<span class="data-value"><?php echo $_SERVER['SERVER_NAME'];?></span>
					</div>
					<div class="data-item">
						<span class="data-label">服务器地址 / 端口</span>
						<span class="data-value badge-tech badge-purple"><?php echo $_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'];?></span>
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
						<span class="data-value badge-tech badge-green"><?php echo $mysqlVersion;?></span>
					</div>
					<div class="data-item">
						<span class="data-label">最大执行时间</span>
						<span class="data-value"><?php echo get_cfg_var('max_execution_time').'s';?></span>
					</div>
					<div class="data-item">
						<span class="data-label">内存限制</span>
						<span class="data-value"><?php echo get_cfg_var('memory_limit');?></span>
					</div>
				</div>
			</div>
		</div>

		<!-- Server & Hardware Info -->
		<div class="info-card">
			<div class="info-card-header">
				<h3><i class="fa fa-cogs"></i> 服务器硬件 & 资源配置</h3>
			</div>
			<div class="info-card-body">
				<div class="data-list">
					<?php if (!empty($cpuInfo['Name'])) {?>
					<div class="data-item">
						<span class="data-label">CPU 型号</span>
						<span class="data-value"><?php echo $cpuInfo['Name'];?></span>
					</div>
					<?php } ?>
					<?php if (!empty($cpuInfo['NumberOfCores'])) {?>
					<div class="data-item">
						<span class="data-label">CPU 核心数</span>
						<span class="data-value badge-tech"><?php echo $cpuInfo['NumberOfCores'];?> 核</span>
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