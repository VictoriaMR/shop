<?php
use Workerman\Worker;
use PHPSocketIO\SocketIO;
use Workerman\Protocols\Http;

if(PHP_SAPI != 'cli') exit('must run in cli');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__).DS);
require ROOT_PATH.'extra'.DS.'socket'.DS.'autoload.php';

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
function param($param, $name=null, $default=null) {
	return $param[$name] ?? $default;
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
$UuidArr = [];//登录用户客户端
$crawlerUuidArr = [];//自动客户端
$checkUuidArr = [];//维护客户端

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
	//登录事件
	$socket->on('login', function($param) use($socket) {
		if (isset($socket->uuid)) return true;
		$uuid = param($param, 'uuid');
		if (strlen($uuid) != 32) {
			$socket->emit('ioClose', 'param error');
			return false;
		}
		$type = param($param, 'type');
		$type = (explode('_', $type)[1] ?? '').'UuidArr';
		global $$type;
		$socket->join($uuid);
		$socket->uuid = $uuid;

		$$type[$uuid] = $param;
		$$type[$uuid]['ip'] = $socket->conn->remoteAddress;
		$$type[$uuid]['loginTime'] = time();

		$socket->emit('loginSuccess', 'client socket login success with: '.$uuid);
	});
	//登出事件
	$socket->on('logout', function($param) use($socket) {
		if (!isset($socket->uuid)) return false;
		$socket->disconnect();
	});
	//断开连接
	$socket->on('disconnect', function($param) use($socket) {
		if (!isset($socket->uuid)) return false;
		global $UuidArr, $autoUuidArr, $checkUuidArr;
		unset($UuidArr[$socket->uuid]);
		unset($autoUuidArr[$socket->uuid]);
		unset($checkUuidArr[$socket->uuid]);
	});
	//更新参数
	$socket->on('update', function($param) use($socket) {
		if (!isset($socket->uuid)) return false;
		$type = param($param, 'type');
		$type = (explode('_', $type)[1] ?? '').'UuidArr';
		global $$type;
		$uuid = $socket->uuid;
		$$type[$uuid] = array_merge($$type[$uuid], $param);
		$socket->emit('updateSuccess', 'client param updated success with: '.$uuid);
	});
});
//推送端口
$io->on('workerStart', function() use($config, $sslOption) {
	$worker = new Worker('http://'.$config['domain'].':'.$config['worker_port'], $sslOption);
	if ($sslOption) {
		$worker->transport = 'ssl';
	}
	// 当http客户端发来数据时触发
	$worker->onMessage = function($httpWorker, $request) {
		global $token;
		// 接口验证
		$param = $request->post();
		if (param($param, 'token') == $token) {
			switch (param($param, 'act')) {
				case 'online': //登陆用户在线
					$type = param($param, 'type', '').'UuidArr';
					global $$type;
					$temp = [];
					foreach ($$type as $key => $value) {
						if (isset($value['is_free'])) {
							if ($value['is_free']) {
								$temp[] = $key;
							}
						} else {
							$temp[] = $key;
						}
					}
					return $httpWorker->send(result($temp, 200, 'online client'));
					break;
				case 'push'://发送自动任务
					$uuid = param($param, 'uuid');
					if (empty($uuid)) {
						return $httpWorker->send(result([], 10000, 'Invalid request parameter: uuid'));
					} else {
						$type = param($param, 'type', '').'UuidArr';
						global $io, $$type;
						if (!is_array($uuid)) $uuid = [$uuid];
						foreach ($uuid as $value) {
							if (isset($$type[$value])) {
								$$type[$value]['is_free'] = false;
								$io->to($value)->emit(param($param, 'toType'), $param['data']);
							}
						}
						return $httpWorker->send(result([], 200, 'send success'));
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