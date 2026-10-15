<?php

namespace frame;

class Redis
{
	private $_link = null;
	private $_db = 0;
	protected $_connect = false;
	const DEFAULT_EXT_TIME = 3600;
	const DEFAULT_CONNECT_TIME = 5;

	private function connect(): bool
	{
		$this->_connect = false;
		try {
			$redis = new \Redis();
			$host = config('redis', 'host', '127.0.0.1');
			$port = (int)config('redis', 'port', 6379);
			$password = config('redis', 'password', '');

			if ($redis->connect($host, $port, self::DEFAULT_CONNECT_TIME)) {
				if (!empty($password)) {
					$redis->auth($password);
				}
				// CLI 常驻任务下避免 blPop 被默认 read_timeout 中断
				if (isCli() && defined('\Redis::OPT_READ_TIMEOUT')) {
					$redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
				}
				if ($this->_db > 0) {
					$redis->select($this->_db);
				}
				$this->_link = $redis;
				$this->_connect = true;
			}
		} catch (\Throwable $e) {
			$this->_link = null;
			$this->_connect = false;
			frame('Debug')->runlog($e->getMessage(), 'redis');
		}
		return $this->_connect;
	}

	public function setDb($db = 0, $force = false)
	{
		$db = (int)$db;
		if ($this->_link === null || !$this->_connect || $force) {
			$this->_db = $db;
			$this->connect();
			return $this;
		}
		if ($db !== $this->_db) {
			try {
				$this->_link->select($db);
				$this->_db = $db;
			} catch (\Throwable $e) {
				$this->_db = $db;
				$this->connect();
			}
		}
		return $this;
	}

	public function __call($func, $arg)
	{
		if (!$this->_connect && !$this->connect()) {
			return false;
		}
		if ($func === 'hSet') {
			if (isset($arg[2]) && is_array($arg[2])) {
				$arg[2] = json_encode($arg[2], JSON_UNESCAPED_UNICODE);
			}
		} elseif ($func !== 'blPop' && $func !== 'brPop' && $func !== 'eval') {
			if (isset($arg[1]) && is_array($arg[1])) {
				$arg[1] = json_encode($arg[1], JSON_UNESCAPED_UNICODE);
			}
		}

		try {
			$info = $this->_link->$func(...$arg);
		} catch (\Throwable $e) {
			// 常驻进程断线自动重连并重试一次
			if (!$this->connect()) {
				frame('Debug')->runlog($func . ' failed: ' . $e->getMessage(), 'redis');
				return false;
			}
			try {
				$info = $this->_link->$func(...$arg);
			} catch (\Throwable $retryEx) {
				frame('Debug')->runlog($func . ' retry failed: ' . $retryEx->getMessage(), 'redis');
				return false;
			}
		}

		if ($func === 'blPop' || $func === 'brPop' || $func === 'eval') {
			return $info;
		}
		if ($info && is_string($info)) {
			return isArray($info);
		}
		if ($info && is_array($info) && $func === 'hGetAll') {
			foreach ($info as $k => $v) {
				$info[$k] = isArray($v);
			}
		}
		return $info;
	}
}