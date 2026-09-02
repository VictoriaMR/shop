<?php

namespace frame;

final class Connection
{
    private $_connects = [];
    private $_configs = [];

    public function setDb($db = null)
    {
        $db = ($db === null || $db === '') ? 'default' : $db;
        if (isset($this->_connects[$db])) {
            return $this->_connects[$db];
        }

        if (!isset($this->_configs[$db])) {
            $this->_configs[$db] = config('database', $db);
        }
        $config = $this->_configs[$db];
        if (empty($config)) {
            throw new \Exception('Connect Error No Database Config: ' . $db, 1);
        }

        $host = $config['host'] ?? '127.0.0.1';
        $port = isset($config['port']) ? (int)$config['port'] : 3306;
        $mysqli = new \mysqli($host, $config['username'], $config['password'], $config['database'], $port);
        if ($mysqli->connect_errno) {
            throw new \Exception('Connect Error (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error, 1);
        }
        if (!empty($config['charset'])) {
            $mysqli->set_charset($config['charset']);
        }

        return $this->_connects[$db] = $mysqli;
    }
}