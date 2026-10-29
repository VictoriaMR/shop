<?php
$phpOk = PHP_VERSION_ID >= 80500;
$redisOk = !empty($redis_version) && version_compare($redis_version, '2.8', '>=');
$mysqliOk = extension_loaded('mysqli');
$opensslOk = extension_loaded('openssl');
$opensslCnfOk = $opensslOk && !empty($openssl_cnf) && is_file($openssl_cnf);
$opcacheOk = extension_loaded('Zend OPcache') && (bool)ini_get('opcache.enable');

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
		'remark' => '要求 >= 8.5',
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
		'name'   => 'Zend OPcache',
		'value'  => $opcacheOk ? 'On' : 'Off',
		'remark' => '字节码加速缓存',
		'ok'     => $opcacheOk,
		'status' => $opcacheOk ? '已开启' : '未开启',
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
<div class="container-fluid">
	<table class="table table-hover table-middle">
		<thead>
			<tr>
				<th width="220">检测项 / 扩展名称</th>
				<th width="340">当前版本 / 配置值</th>
				<th>说明</th>
				<th width="120" class="tc">状态</th>
			</tr>
		</thead>
		<tbody>
			<tr class="info">
				<td colspan="4"><strong><i class="fa fa-server"></i> 核心运行环境与配置</strong></td>
			</tr>
			<?php foreach ($envRows as $row) {
				$badgeClass = $row['ok'] ? 'label-success' : (!empty($row['warn']) ? 'label-warning' : 'label-danger');
			?>
			<tr>
				<td><strong><?php echo htmlspecialchars($row['name']);?></strong></td>
				<td><code><?php echo htmlspecialchars($row['value']);?></code></td>
				<td class="text-muted"><?php echo htmlspecialchars($row['remark']);?></td>
				<td class="tc">
					<span class="label <?php echo $badgeClass;?>"><?php echo htmlspecialchars($row['status']);?></span>
				</td>
			</tr>
			<?php } ?>

			<tr class="info">
				<td colspan="4"><strong><i class="fa fa-puzzle-piece"></i> PHP 扩展模块状态</strong></td>
			</tr>
			<?php foreach ($extensions as $item) {
				$loaded = extension_loaded($item['ext']);
				$extVer = $loaded ? (phpversion($item['ext']) ?: '已加载') : '--';
				$badgeClass = $loaded ? 'label-success' : ($item['required'] ? 'label-danger' : 'label-default');
			?>
			<tr>
				<td>
					<strong><?php echo htmlspecialchars($item['label']);?></strong>
					<span class="text-muted f12 ml6">(<?php echo htmlspecialchars($item['ext']);?>)</span>
				</td>
				<td>
					<?php if ($loaded){ ?>
					<code><?php echo htmlspecialchars((string)$extVer);?></code>
					<?php } else { ?>
					<span class="text-muted">--</span>
					<?php } ?>
				</td>
				<td class="text-muted">
					<?php echo htmlspecialchars($item['desc']);?>
					<?php if (!$item['required']){ ?>
					<span class="label label-default ml6">可选</span>
					<?php } ?>
				</td>
				<td class="tc">
					<span class="label <?php echo $badgeClass;?>"><?php echo $loaded ? '已安装' : '未安装';?></span>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
