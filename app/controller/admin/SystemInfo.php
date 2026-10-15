<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class SystemInfo extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index'   => '服务器信息',
			'mysql'   => 'MySQL信息',
			'redis'   => 'Redis信息',
			'phpinfo' => 'PHP信息',
		];
		$this->_default = '服务器信息';
		$this->_init();
	}

	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$redisVersion = '';
		try {
			$redisInfo = redis()->info();
			$redisVersion = $redisInfo['redis_version'] ?? '';
		} catch (\Throwable $e) {
			$redisVersion = '';
		}

		$mysqlVersion = '';
		try {
			$rst = db()->query('SELECT version() AS version');
			if ($rst) {
				$row = $rst->fetch_assoc();
				$mysqlVersion = $row['version'] ?? '';
			}
		} catch (\Throwable $e) {
			$mysqlVersion = '';
		}

		$opensslCnf = '';
		if (extension_loaded('openssl')) {
			$ext = new \ReflectionExtension('openssl');
			ob_start();
			$ext->info();
			$extInfo = ob_get_clean();
			foreach (explode("\n", (string)$extInfo) as $line) {
				if (strpos($line, 'Openssl default config') !== false) {
					$opensslCnf = trim(strip_tags(str_replace(['Openssl default config', '=>'], '', $line)));
					break;
				}
			}
		}

		$this->assign('mysql_version', $mysqlVersion);
		$this->assign('redis_version', $redisVersion);
		$this->assign('openssl_cnf', $opensslCnf);
		$this->view();
	}

	public function mysql()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$statRaw = (string)db()->stat();
		$statParts = array_filter(explode('  ', $statRaw));
		$stat = [];
		foreach ($statParts as $part) {
			$pair = explode(': ', $part, 2);
			$stat[] = [
				trim($pair[0] ?? ''),
				trim($pair[1] ?? ''),
			];
		}

		$vars = [];
		$sql = "SHOW VARIABLES WHERE Variable_name LIKE '%character%' "
			. "OR Variable_name LIKE '%version%' "
			. "OR Variable_name LIKE '%client%' "
			. "OR Variable_name LIKE '%server%' "
			. "OR Variable_name LIKE '%collation%'";
		$rst = db()->query($sql);
		if ($rst) {
			foreach ($rst as $val) {
				$vars[$val['Variable_name']] = $val['Value'];
			}
			ksort($vars);
		}

		$info = [
			'stat'   => $stat,
			'client' => mysqli_get_client_info(),
			'server' => db()->server_info,
			'other'  => $vars,
		];
		$this->assign('info', $info);
		$this->view();
	}

	public function redis()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$info = [];
		try {
			$raw = redis()->info();
			if (is_array($raw)) {
				$info = $raw;
			}
		} catch (\Throwable $e) {
			$info = [];
		}
		$this->assign('info', $info);
		$this->view();
	}

	public function phpinfo()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		ob_start();
		phpinfo();
		$rawHtml = (string)ob_get_clean();

		// 仅提取 body 内部结构并转换为标准 Bootstrap 表格，避免 phpinfo 自带的全局 style 污染后台导航样式
		$bodyHtml = $rawHtml;
		if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $rawHtml, $matches)) {
			$bodyHtml = $matches[1];
		}
		$bodyHtml = preg_replace('/<table[^>]*>/i', '<table class="table table-hover table-middle">', $bodyHtml);

		$this->assign('phpinfoHtml', $bodyHtml);
		$this->view();
	}
}