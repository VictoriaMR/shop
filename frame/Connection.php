<?php

namespace frame;

final class Connection
{
	private $_connect;
	private $_selectdb;
	private function __clone() {}

	private function connect($host, $username, $password, $port='3306', $database='', $charset='utf8')
	{
		$this->_connect = new \mysqli($host, $username, $password, $database, $port);
		if ($this->_connect->connect_error) {
			throw new \Exception('Connect Error ('.$this->_connect->connect_errno.') '.$this->_connect->connect_error, 1);
		}
		// $this->_connect->set_charset($charset);
	}

	public function setDb($db)
	{
		if (is_null($db)) $db = 'default';
		$config = config('database', $db);
		if (empty($config)) {
			throw new \Exception('Connect Error No Database Config', 1);
		}
		if (is_null($this->_connect)) {
			$this->connect(
				$config['db_host'], 
				$config['db_username'], 
				$config['db_password'], 
				$config['db_port'], 
				$config['db_database'], 
				$config['db_charset']
			);
			$this->_selectdb = $config['db_database'];
		}
		if ($this->_selectdb != $config['db_database']) {
			$this->_connect->select_db($config['db_database']);
			$this->_selectdb = $config['db_database'];
		}
		return $this->_connect;
	}
}