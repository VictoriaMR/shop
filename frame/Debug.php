<?php

namespace frame;

class Debug
{
	public function init(): void
	{
		if (isCli()) return;
		$timeStart = defined('APP_TIME_START') ? APP_TIME_START : microtime(true);
		$memStart = defined('APP_MEMORY_START') ? APP_MEMORY_START : memory_get_usage();
		$runtime = number_format(microtime(true) - $timeStart, 6, '.', '');
		$reqs = (float)$runtime > 0 ? number_format(1 / (float)$runtime, 2, '.', '') : '∞';
		$mem = number_format((memory_get_usage() - $memStart) / 1024, 2, '.', '');
		$uri = ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1') . ' ' . ($_SERVER['REQUEST_METHOD'] ?? 'GET') . ' ' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/');

		$info = get_included_files();
		$fileMem = 0.0;
		foreach ($info as $key => $file) {
			$temp = is_file($file) ? number_format(filesize($file) / 1024, 2, '.', '') : '0.00';
			$fileMem += (float)$temp;
			$info[$key] .= '(' . $temp . 'KB)';
		}

		$execSql = \App::get('exec_sql') ?? [];
		$base = [
			'请求信息' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()) . ' ' . $uri,
			'运行时间' => $runtime . 's[吞吐率：' . $reqs . ' req/s]',
			'查询信息' => count($execSql) . '条',
			'内存消耗' => $mem . 'KB',
			'文件加载' => count($info) . '个',
			'文件总值' => number_format($fileMem, 2, '.', '') . 'KB',
		];

		$trace = [
			'基本' => $base,
			'文件' => $info,
			'SQL' => $execSql,
		];

		frame('View')->load('frame/pagetrace', [
			'trace' => $trace,
			'runtime' => $runtime,
		], false);
	}

	/**
	 * 记录运行日志
	 * @param string $msg  日志内容, 空字符串表示仅记录运行时信息
	 * @param string $type 日志类型后缀, 如 'error' 则文件名为 28_error.log
	 * @return $this
	 */
	public function runlog($msg = '', $type = '')
	{
		if (is_array($msg)) {
			$msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
		}
		$destination = ROOT_PATH . 'runtime/' . date('Ym') . '/' . date('d') . ($type === '' ? '' : '_' . $type) . '.log';
		$dir = dirname($destination);
		if (!is_dir($dir)) {
			createDir($dir);
		}

		// 请求来源
		if (isCli()) {
			$currentUri = ' cmd: ' . implode(' ', $_SERVER['argv'] ?? []);
		} else {
			$currentUri = ' uri: ' . ($_SERVER['HTTP_HOST'] ?? '') . urldecode($_SERVER['REQUEST_URI'] ?? '/');
		}

		// 运行指标
		$timeStart = defined('APP_TIME_START') ? APP_TIME_START : microtime(true);
		$memStart = defined('APP_MEMORY_START') ? APP_MEMORY_START : memory_get_usage();
		$runtime = number_format(microtime(true) - $timeStart, 6, '.', '');
		$reqs = (float)$runtime > 0 ? number_format(1 / (float)$runtime, 2, '.', '') : '∞';
		$mem = number_format((memory_get_usage() - $memStart) / 1024, 2, '.', '');
		$fileCount = count(get_included_files());
		$sqlCount = count(\App::get('exec_sql') ?? []);

		$server = $_SERVER['SERVER_ADDR'] ?? '0.0.0.0';
		$remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

		$log = '[runtime] ' . date('Y-m-d H:i:s') . ' [server] ' . $server . ' [remote] ' . $remote . $currentUri . PHP_EOL
			 . "[time: {$runtime}s] [qps: {$reqs}req/s] [mem: {$mem}kb] [sql: {$sqlCount}] [files: {$fileCount}]" . PHP_EOL
			 . ($msg !== '' ? $msg . PHP_EOL : '')
			 . PHP_EOL;

		error_log($log, 3, $destination);
		return $this;
	}

	public function addLog($msg = '', $type = '')
	{
		return $this->runlog($msg, $type);
	}
}
