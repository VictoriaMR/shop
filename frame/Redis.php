<?php

namespace frame;

class Redis
{
	private $_link;
	private $_db;
	const DEFAULT_EXT_TIME = 60;
	const DEFAULT_CONNECT_TIME = 5;

	private function connect() 
	{
		$this->_link = new \Redis();
		$this->_link->connect(config('env.REDIS_HOST', '127.0.0.1'), config('env.REDIS_PORT', '6379'), self::DEFAULT_CONNECT_TIME);
		if (!empty(config('env.REDIS_PASSWORD'))) {
			$this->_link->auth(config('env.REDIS_PASSWORD'));
		}
		return true;
	}

	public function setDb($db=0)
	{
		if (is_null($this->_link)) {
			$this->connect();
		}
		if ($db != $this->_db) {
			$this->_link->select($db);
			$this->_db = $db;
		}
		return $this;
	}
	
	public function __call($func, $arg)
	{
		if (is_null($this->_link)) return false;
		if ($func != 'hMset' && !empty($arg[1]) && is_array($arg[1])) {
			$arg[1] = json_encode($arg[1], JSON_UNESCAPED_UNICODE);
		} elseif ($func == 'hSet' && !empty($arg[2]) && is_array($arg[2])) {
			$arg[2] = json_encode($arg[2], JSON_UNESCAPED_UNICODE);
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