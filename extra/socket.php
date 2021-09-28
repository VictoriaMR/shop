<?php
use Workerman\Worker;
use PHPSocketIO\SocketIO;
use Workerman\Protocols\Http;

if(PHP_SAPI != 'cli') exit('must run in cli');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__).DS);
$autoload = ROOT_PATH.'extra'.DS.'socket'.DS.'autoload.php';
require $autoload;

function config($name) {
	global $config;
	if (!isset($config[$name])) {
		if (is_file($file = ROOT_PATH.'config'.DS.$name.'.php')) {
			$config[$name] = require $file;
		} else {
			$config[$name] = null;
		}
	}
	return $config[$name];
}
function param($name=null) {
	if (is_null($name)) return $_POST;
	return $_POST[$name] ?? null;
}
function result($data, $code=0, $msg='') {
	return json_encode(['code'=>$code, 'data'=>$data, 'msg'=>$msg], JSON_UNESCAPED_UNICODE);
}
function now() {
	return date('Y-m-d H:i:s');
}

// 全局数组保存
$config = config('socket');//socket配置
$token = config('token');//token
$uuid = [];//登录用户客户端
$autoUuid = [];//自动客户端

$sslOption = [];
if ($config['ssl']) {
	$sslOption['ssl'] = $config['ssl'];
}

//实例化SocketIO
$io = new SocketIO($config['socket_port'], $sslOption, $config['domain']);
//链接事件
$io->on('connection', function($socket) {
	// 心跳保持
	$socket->on('ioPing', function($e) use($socket) {
		$socket->emit('ioPong', 'Pong');
	});
	//普通注册事件
	$socket->on('login', function($param) use($socket) {
		if (isset($socket->uuid)) return true;
		if (strlen($param) != 32) {
			$socket->emit('ioClose', 'param error');
			return false;
		}
		global $uuid;
		$socket->join($param);
		$socket->uuid = $param;

		$uuid[$param] = [];
		$uuid[$param]['ip'] = $socket->conn->remoteAddress;
		$uuid[$param]['loginTime'] = time();

		$socket->emit('loginSuccess', now().' client socket login success with: '.$param);
	});
	//自动登录事件
	$socket->on('autoLogin', function($param) use($socket) {
		if (isset($socket->uuid)) return true;
		if (strlen($param) != 32) {
			$socket->emit('ioClose', 'param error');
			return false;
		}
		global $autoUuid;
		$socket->join($param);
		$socket->uuid = $param;

		$autoUuid[$param] = [];
		$autoUuid[$param]['ip'] = $socket->conn->remoteAddress;
		$autoUuid[$param]['loginTime'] = time();
		$autoUuid[$param]['is_free'] = 1;

		$socket->emit('loginSuccess', now().' client socket login success with: '.$param);
	});
	//是否在线
	$socket->on('isOnline', function($param) use($socket) {
		if (!isset($socket->uuid)) return false;
		if (empty($param['uuid'])) return false;
		global $uuid;
		$rst = [];
		foreach (explode(',', $param['uuid']) as $v){
			$rst[$v] = isset($uuid[$v]) ? true : false;
		}
		$socket->emit('online', $rst);
	});
	//断开连接
	$socket->on('disconnect', function($param) use($socket) {
		if (!isset($socket->uuid)) return false;
		global $uuid, $autoUuid;
		unset($uuid[$socket->uuid]);
		unset($autoUuid[$socket->uuid]);
	});
});
//推送端口
$io->on('workerStart', function() use($config, $sslOption) {
	$worker = new Worker('http://'.$config['domain'].':'.$config['worker_port'], $sslOption);
	if ($sslOption) {
		$worker->transport = 'ssl';
	}
	// 当http客户端发来数据时触发
	$worker->onMessage = function($httpWorker, $data) {
		Http::header('Content-Type: application/json');
		global $token;
		// 接口验证
		if (param('token') == $token) {
			switch (param('act')) {
				case 'push'://推送
					$message = param('message');
					$to = param('to');
					if (empty($message)) {
						return $httpWorker->send(result([], 10000, 'Invalid request parameter'));
					}
					$rst = [];
					if ($to == 'all') {
						$to = array_keys($uuid);
					} else {
						$to = explode(',', $to);
					}
					global $io, $uuid;
					foreach ($to as $key => $value) {
						if (isset($uuid[$value])) {
							$io->to($value)->emit('message', $message);
							$rst[$value] = true;
						}
					}
					return $httpWorker->send(result($rst, 200, 'push result'));
					break;
				case 'online': //登陆用户在线
					global $uuid;
					return $httpWorker->send(result(array_keys($uuid), 200, 'online client'));
					break;
				case 'autoOnline'://自动客户端空闲在线
					global $autoUuid;
					$temp = [];
					foreach ($autoUuid as $key => $value) {
						if (isset($value['is_free']) && $value['is_free']) {
							$temp[] = $key;
						}
					}
					return $httpWorker->send(result($temp, 200, 'online client'));
					break;
				case 'autoDeal'://发送自动任务
					$uuid = param('uuid');
					if (empty($uuid) || strlen($uuid) != 32) {
						return $httpWorker->send(result([], 10000, 'Invalid request parameter: uuid'));
					} else {
						global $io, $autoUuid;
						$autoUuid[$uuid]['is_free'] = false;
						$io->to($param['uuid'])->emit('autoDeal', param());
						return $httpWorker->send(result([], 200, 'send to '.$uuid.' success'));
					}
					break;
			}
		} else {
			$httpWorker->send(result([], 10000, 'Authentication failed'));
		}
	};
	// 执行监听
	$worker->listen();
});
// 运行所有服务
Worker::runAll();