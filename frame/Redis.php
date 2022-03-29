<?php

namespace frame;

class Redis
{
	private $_link;
	private $_db;
	protected $_connect = false;
	const DEFAULT_EXT_TIME = 3600;
	const DEFAULT_CONNECT_TIME = 5;

	private function connect() 
	{
		if (config('env', 'REDIS_HOST')) {
			$this->_link = new \Redis();
			try {
				$this->_link->connect(config('env', 'REDIS_HOST'), config('env', 'REDIS_PORT', '6379'), self::DEFAULT_CONNECT_TIME);
				$this->_connect = true;
			} catch (\Exception $e) {
				make('frame/Debug')->runlog($e->getMessage(), 'redis');
			}
			if ($this->_connect && !empty(config('env', 'REDIS_PASSWORD'))) {
				$this->_link->auth(config('env', 'REDIS_PASSWORD'));
			}
		}
		return $this->_connect;
	}

	public function setDb($db=0, $force=false)
	{
		if (config('env', 'APP_CACHE') || $force) {
			if (is_null($this->_link)) {
				$this->connect();
			}
			if ($db != $this->_db) {
				$this->_link->select($db);
				$this->_db = $db;
			}
		}
		return $this;
	}
	
	public function __call($func, $arg)
	{
		if (!$this->_connect) return false;
		if ($func != 'hMset' && isset($arg[1]) && is_array($arg[1])) {
			$arg[1] = json_encode($arg[1], JSON_UNESCAPED_UNICODE);
		} elseif ($func == 'hSet' && isset($arg[2]) && is_array($arg[2])) {
			$arg[2] = json_encode($arg[2], JSON_UNESCAPED_UNICODE);
		} elseif ($func == 'set'){
			if (!isset($arg[2])) $arg[2] = self::DEFAULT_EXT_TIME;
		}
		$info = $this->_link->$func(...$arg);
		if ($info) {
			if (in_array($func, ['hGetAll'])) {
				foreach ($info as $k => $v) {
					$info[$k] = isJson($v);
				}
			} else {
				$info = isJson($info);
			}
		}
		return $info;
	}
}