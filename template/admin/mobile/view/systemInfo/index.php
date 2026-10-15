<?php
$phpOk = version_compare(PHP_VERSION, '7.1', '>=');
$redisOk = !empty($redis_version) && version_compare($redis_version, '2.8', '>=');
$mysqliOk = extension_loaded('mysqli');
$opensslOk = extension_loaded('openssl');
$opensslCnfOk = $opensslOk && !empty($openssl_cnf) && is_file($openssl_cnf);

$envRows = [
	[
		'name'   => '操作系统',
		'value'  => PHP_OS . ' (' . php_uname('s') . ' ' . php_uname('r') . ')',
		'remark' => php_uname('m'),
		'ok'     => true,
		'status' => '正常',
	],
	[
		'name'   => '运行环境 / SAPI',
		'value'  => ($_SERVER['SERVER_SOFTWARE'] ?? 'CLI') . ' / ' . PHP_SAPI,
		'remark' => '时区: ' . date_default_timezone_get(),
		'ok'     => true,
		'status' => '正常',
	],
	[
		'name'   => 'PHP 版本',
		'value'  => PHP_VERSION,
		'remark' => '要求 >= 7.1',
		'ok'     => $phpOk,
		'status' => $phpOk ? '符合要求' : '版本过低',
	],
	[
		'name'   => 'MySQL (Mysqli)',
		'value'  => $mysql_version ?: '--',
		'remark' => '数据库驱动扩展',
		'ok'     => $mysqliOk,
		'status' => $mysqliOk ? '已安装' : '未安装',
	],
	[
		'name'   => 'Redis 服务',
		'value'  => $redis_version ?: '--',
		'remark' => '要求 >= 2.8',
		'ok'     => $redisOk,
		'status' => $redisOk ? '已连接' : '未连接',
	],
	[
		'name'   => 'Mysqli.reconnect',
		'value'  => ini_get('mysqli.reconnect') ? 'On' : 'Off',
		'remark' => '断线自动重连配置',
		'ok'     => (bool)ini_get('mysqli.reconnect'),
		'status' => ini_get('mysqli.reconnect') ? '已开启' : '未开启',
		'warn'   => true,
	],
	[
		'name'   => '内存 / 上传 / 超时限制',
		'value'  => '内存: ' . ini_get('memory_limit') . ' / 上传: ' . ini_get('upload_max_filesize') . ' / POST: ' . ini_get('post_max_size'),
		'remark' => '最大执行时间: ' . ini_get('max_execution_time') . 's',
		'ok'     => true,
		'status' => '已配置',
	],
	[
		'name'   => 'openssl.cnf',
		'value'  => $openssl_cnf ?: 'not found openssl.cnf path',
		'remark' => $opensslCnfOk ? '默认证书配置文件' : '可通过系统配置节点 openssl_cnf 单独配置',
		'ok'     => $opensslCnfOk,
		'status' => $opensslCnfOk ? '已配置' : '未配置',
		'warn'   => true,
	],
];

$extensions = [
	['label' => 'Mysqlnd',    'ext' => 'mysqlnd',    'desc' => 'MySQL 原生驱动',       'required' => true],
	['label' => 'Json',       'ext' => 'json',       'desc' => 'JSON 数据解析',        'required' => true],
	['label' => 'Openssl',    'ext' => 'openssl',    'desc' => 'SSL 加密与签名传输',    'required' => true],
	['label' => 'Pcre',       'ext' => 'pcre',       'desc' => '正则表达式匹配引擎',    'required' => true],
	['label' => 'Curl',       'ext' => 'curl',       'desc' => 'HTTP 远程请求客户端',   'required' => true],
	['label' => 'Session',    'ext' => 'session',    'desc' => '会话状态管理',          'required' => true],
	['label' => 'Filter',     'ext' => 'filter',     'desc' => '数据过滤与安全校验',    'required' => true],
	['label' => 'Iconv',      'ext' => 'iconv',      'desc' => '字符集编码转换',        'required' => true],
	['label' => 'Reflection', 'ext' => 'reflection', 'desc' => '类与方法反射分析',      'required' => true],
	['label' => 'Mbstring',   'ext' => 'mbstring',   'desc' => '多字节字符串处理',      'required' => true],
	['label' => 'php_zip',    'ext' => 'zip',        'desc' => 'ZIP 压缩包读写',       'required' => false],
	['label' => 'php_xml',    'ext' => 'xml',        'desc' => 'XML 文档解析',         'required' => true],
	['label' => 'php_gd2',    'ext' => 'gd',         'desc' => 'GD 图像处理与缩略图',   'required' => true],
	['label' => 'Fileinfo',   'ext' => 'fileinfo',   'desc' => '文件 MIME 类型识别',    'required' => true],
	['label' => 'Exif',       'ext' => 'exif',       'desc' => '图片元信息读取',        'required' => false],
	['label' => 'Memcached',  'ext' => 'memcached',  'desc' => 'Memcached 分布式缓存', 'required' => false],
	['label' => 'Memcache',   'ext' => 'memcache',   'desc' => 'Memcache 缓存扩展',    'required' => false],
	['label' => 'Mailparse',  'ext' => 'mailparse',  'desc' => '邮件内容解析扩展',      'required' => false],
	['label' => 'Imap',       'ext' => 'imap',       'desc' => 'IMAP 邮件协议收发',     'required' => false],
	['label' => 'Imagick',    'ext' => 'imagick',    'desc' => 'ImageMagick 高级图像', 'required' => false],
];
?>
<div id="sysinfo-index-page" class="sysinfo-mobile-page">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-server"></i>
			<span>服务器运行环境</span>
		</div>
		<span class="ver-badge">PHP <?php echo htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8');?></span>
	</div>

	<!-- 快速筛选与搜索栏 -->
	<div class="filter-box">
		<div class="sys-tab-filter">
			<button type="button" data-filter="all" class="filter-tab active">全部</button>
			<button type="button" data-filter="env" class="filter-tab">核心配置</button>
			<button type="button" data-filter="ext" class="filter-tab">PHP扩展</button>
			<button type="button" data-filter="warn" class="filter-tab">未安装/提醒</button>
		</div>
		<div class="search-row">
			<input type="text" id="sys-keyword-input" class="form-control" placeholder="输入检测项或扩展名快速检索..." autocomplete="off">
		</div>
	</div>

	<!-- 核心运行环境与配置 -->
	<div class="section-card sys-section" data-section="env">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-microchip"></i> 核心运行环境与配置</div>
			<span class="section-count"><?php echo count($envRows);?> 项</span>
		</div>
		<div class="section-body">
			<?php foreach ($envRows as $row) {
				$statusClass = $row['ok'] ? 'ok' : (!empty($row['warn']) ? 'warn' : 'err');
			?>
			<div class="sys-item-card" data-ok="<?php echo $row['ok'] ? '1' : '0';?>">
				<div class="item-top">
					<span class="item-name"><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');?></span>
					<span class="status-pill <?php echo $statusClass;?>"><?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="item-val">
					<code><?php echo htmlspecialchars($row['value'], ENT_QUOTES, 'UTF-8');?></code>
				</div>
				<div class="item-remark"><?php echo htmlspecialchars($row['remark'], ENT_QUOTES, 'UTF-8');?></div>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- PHP 扩展模块状态 -->
	<div class="section-card sys-section" data-section="ext">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-puzzle-piece"></i> PHP 扩展模块状态</div>
			<span class="section-count"><?php echo count($extensions);?> 项</span>
		</div>
		<div class="section-body">
			<?php foreach ($extensions as $item) {
				$loaded = extension_loaded($item['ext']);
				$extVer = $loaded ? (phpversion($item['ext']) ?: '已加载') : '--';
				$statusClass = $loaded ? 'ok' : ($item['required'] ? 'err' : 'neutral');
			?>
			<div class="sys-item-card" data-ok="<?php echo $loaded ? '1' : '0';?>">
				<div class="item-top">
					<div class="item-title-group">
						<span class="item-name"><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');?></span>
						<code class="ext-code"><?php echo htmlspecialchars($item['ext'], ENT_QUOTES, 'UTF-8');?></code>
						<?php if (!$item['required']) { ?>
						<span class="opt-tag">可选</span>
						<?php } ?>
					</div>
					<span class="status-pill <?php echo $statusClass;?>"><?php echo $loaded ? '已安装' : '未安装';?></span>
				</div>
				<div class="item-meta-line">
					<span class="item-remark"><?php echo htmlspecialchars($item['desc'], ENT_QUOTES, 'UTF-8');?></span>
					<?php if ($loaded) { ?>
					<code class="ver-code"><?php echo htmlspecialchars((string)$extVer, ENT_QUOTES, 'UTF-8');?></code>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
