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
		$this->_link->connect(env('REDIS_HOST', '127.0.0.1'), env('REDIS_PORT', '6379'), self::DEFAULT_CONNECT_TIME);
		if (!empty(env('REDIS_PASSWORD'))) {
			$this->_link->auth(env('REDIS_PASSWORD'));
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
		return $this->_link;
	}
	
	public function __call($func, $arg)
	{
		if (is_null($this->_link)) return false;
		$info = $this->_link->$func(...$arg);
		$temp = isJson($info);
		if ($temp === false) {
			return $info;
		} else {
			return $temp;
		}
	}
}