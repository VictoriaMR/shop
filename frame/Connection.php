<?php

namespace frame;

final class Connection
{
    private $_connects = [];
    private $_configs = [];
    private $_verified = [];

    public function setDb($db = null)
    {
        $db = ($db === null || $db === '') ? 'default' : $db;
        if (isset($this->_connects[$db])) {
            // 每次请求仅首次 ping 检测死连接，后续直接返回
            if (!isset($this->_verified[$db])) {
                if (!$this->_connects[$db]->ping()) {
                    unset($this->_connects[$db]);
                    return $this->setDb($db);
                }
                $this->_verified[$db] = true;
            }
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
        $timeout = isset($config['timeout']) ? (int)$config['timeout'] : 5;

        // 支持持久连接: 在 database 配置中设置 'persistent' => true
        if (!empty($config['persistent'])) {
            $host = 'p:' . $host;
        }

        $mysqli = mysqli_init();
        // 设置连接超时（秒），防止连接卡死
        $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);
        $mysqli->real_connect($host, $config['username'], $config['password'], $config['database'], $port);

        if ($mysqli->connect_errno) {
            throw new \Exception('Connect Error (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error, 1);
        }
        if (!empty($config['charset'])) {
            $mysqli->set_charset($config['charset']);
        }

        $this->_verified[$db] = true;
        return $this->_connects[$db] = $mysqli;
    }
}