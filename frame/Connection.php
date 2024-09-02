<?php

namespace frame;

final class Connection
{
    private $_connect;
    private $_selectdb;

    public function setDb($db=null)
    {
        is_null($db) && $db = 'default';
        $config = config('database', $db);
        if (empty($config)) {
            throw new \Exception('Connect Error No Database Config', 1);
        }
        if (!$this->_connect) {
            $this->_connect = new \mysqli($config['host']??'127.0.0.1', $config['username'], $config['password'], $config['database'], $config['port']??'3306');
            if ($this->_connect->connect_error) {
                throw new \Exception('Connect Error, '.$this->_connect->connect_error, 1);
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