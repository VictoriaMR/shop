<?php

namespace frame;

final class Connection
{
    private $_connect;
    private $_selectdb;
    private $_configs = [];

    public function setDb($db=null)
    {
        $db === null && $db = 'default';
        if (!isset($this->_configs[$db])) {
            $this->_configs[$db] = config('database', $db);
        }
        $config = $this->_configs[$db];
        if (empty($config)) {
            throw new \Exception('Connect Error No Database Config', 1);
        }
        $host = $config['host'] ?? '127.0.0.1';
        if (!$this->_connect) {
            $this->_connect = new \mysqli($host, $config['username'], $config['password'], $config['database'], $config['port']);
            if ($this->_connect->connect_error) {
                throw new \Exception('Connect Error, '.$this->_connect->connect_error, 1);
            }
            if (!empty($config['charset'])) {
                $this->_connect->set_charset($config['charset']);
            }
            $this->_selectdb = $config['database'];
        }
        if ($this->_connect && $this->_selectdb != $config['database']) {
            $this->_connect->select_db($config['database']);
            $this->_selectdb = $config['database'];
        }
        return $this->_connect;
    }
}