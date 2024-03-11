<?php

namespace frame;

final class Connection
{
	private $_connect;
	private $_selectdb;

	private function connect($host, $username, $password, $port='3306', $database='')
	{
		$this->_connect = new \mysqli($host, $username, $password, $database, $port);
		if ($this->_connect->connect_error) {
			$this->_connect = false;
		}
	}

	public function setDb($db=null)
	{
		is_null($db) && $db = 'default';
		$config = config('database', $db);
		if (empty($config)) {
			throw new \Exception('Connect Error No Database Config', 1);
		}
		if (!$this->_connect) {
			$this->connect(
				$config['host'] ?? '127.0.0.1',
				$config['username'],
				$config['password'],
				$config['port'] ?? '3306',
				$config['database'],
			);
			$this->_selectdb = $config['database'];
		}
		if ($this->_connect && $this->_selectdb != $config['database']) {
			$this->_connect->select_db($config['database']);
			$this->_selectdb = $config['database'];
		}
		return $this->_connect;
	}
}