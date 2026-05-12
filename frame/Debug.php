<?php

namespace frame;

class Debug
{
	public function init()
	{
		if (isCli()) return;
		// 获取基本信息
		$runtime = number_format(microtime(true) - APP_TIME_START, 10, '.', '');
		$reqs = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
		$mem = number_format((memory_get_usage() - APP_MEMORY_START) / 1024, 2);
		$uri = implode(' ', [
			$_SERVER['SERVER_PROTOCOL'],
			$_SERVER['REQUEST_METHOD'],
			$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
		]);
		$info = get_included_files();
		$fileMem = 0;
		foreach ($info as $key => $file) {
			$temp = number_format(filesize($file) / 1024, 2);
			$fileMem += $temp;
			$info[$key] .= '(' . $temp . 'KB)';
		}
		$base = [
			'请求信息' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $uri,
			'运行时间' => number_format((float) $runtime, 6) . 's[吞吐率：' . $reqs . ' req/s]',
			'查询信息' => count(\App::get('exec_sql') ?? []) . '条',
			'内存消耗' => $mem . 'KB',
			'文件加载' => count($info) . '个',
			'文件总值' => $fileMem . 'KB',
		];
		$config = [
			'file' => '',
			'tabs' => ['base' => '基本', 'file' => '文件', 'sql' => 'SQL'],
		];
		$trace = [];
		foreach ($config['tabs'] as $name => $title) {
			$name = strtolower($name);
			switch ($name) {
				case 'base':
					$trace[$title] = $base;
					break;
				case 'file':
					$trace[$title] = $info;
					break;
				case 'sql':
					$trace[$title] = \App::get('exec_sql') ?? '';
					break;
			}
		}
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
	public function runlog($msg='', $type='')
	{
		$destination = ROOT_PATH.'runtime/'.date('Ym').'/'.date('d').(empty($type) ? '' : '_'.$type).'.log';

		if (!is_file($destination)) {
			createDir(dirname($destination));
		}

		// 请求来源
		if (isCli()) {
			$current_uri = ' cmd: ' . implode(' ', $_SERVER['argv']);
		} else {
			$current_uri = ' uri: ' . $_SERVER['HTTP_HOST'] . urldecode($_SERVER['REQUEST_URI']);
		}

		// 运行指标
		$runtime = number_format(microtime(true) - APP_TIME_START, 6, '.', '');
		$reqs = $runtime > 0 ? number_format(1 / $runtime, 2, '.', '') : '∞';
		$mem = number_format((memory_get_usage() - APP_MEMORY_START) / 1024, 2, '.', '');
		$fileCount = count(get_included_files());
		$sqlCount = count(\App::get('exec_sql') ?? []);

		// 组装日志行
		$info = "[Time：{$runtime}s] [QPS：{$reqs}req/s] [MEM：{$mem}kb] [SQL：{$sqlCount}] [Files：{$fileCount}]" . PHP_EOL;
		$server = $_SERVER['SERVER_ADDR'] ?? '0.0.0.0';
		$remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

		$log = '[runtime] ' . date('Y-m-d H:i:s') . ' [server addr] ' . $server . ' [remote addr] ' . $remote . $current_uri . PHP_EOL
			 . $info
			 . ($msg !== '' ? $msg . PHP_EOL : '')
			 . PHP_EOL;

		error_log($log, 3, $destination);
		return $this;
	}
}