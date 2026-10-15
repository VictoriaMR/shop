<?php

namespace frame;

final class Connection
{
    private $_connects = [];
    private $_configs = [];
    private $_lastActive = [];

    public function setDb($db = null, bool $forceReconnect = false)
    {
        $db = ($db === null || $db === '') ? 'default' : $db;
        $now = time();

        if ($forceReconnect && isset($this->_connects[$db])) {
            @$this->_connects[$db]->close();
            unset($this->_connects[$db], $this->_lastActive[$db]);
        }

        if (isset($this->_connects[$db])) {
            // 30秒内活跃连接直接复用(零 ping 开销); 超过30秒(如CLI长任务)检测连接可用性
            if (($now - ($this->_lastActive[$db] ?? 0)) >= 30) {
                $alive = false;
                try {
                    $alive = @$this->_connects[$db]->query('SELECT 1') !== false;
                } catch (\Throwable $e) {
                    $alive = false;
                }
                if (!$alive) {
                    @$this->_connects[$db]->close();
                    unset($this->_connects[$db], $this->_lastActive[$db]);
                    return $this->setDb($db);
                }
            }
            $this->_lastActive[$db] = $now;
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
        $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);
        $mysqli->real_connect($host, $config['username'], $config['password'], $config['database'], $port);

        if ($mysqli->connect_errno) {
            throw new \Exception('Connect Error (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error, 1);
        }
        if (!empty($config['charset'])) {
            $mysqli->set_charset($config['charset']);
        }

        $this->_lastActive[$db] = $now;
        return $this->_connects[$db] = $mysqli;
    }
}