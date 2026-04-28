<?php
class App
{
	private static $data = [];
	private static $_error = [];

	public static function init()
	{
		spl_autoload_register([__CLASS__, 'autoload']);
		frame('Error')->register();
	}

	public static function make($abstract, $params=null)
	{
		$instance = self::$data['autoload'][$abstract] ?? null;
		if ($instance === null) {
			if ($abstract instanceof \Closure) {
				$instance = $abstract($params);
			} else {
				$concrete = strtr($abstract, '/', '\\');
				$instance = $params !== null ? new $concrete($params) : new $concrete();
			}
			self::$data['autoload'][$abstract] = $instance;
		}
		return $instance;
	}

	public static function send()
	{
		$info = config('domain', $_SERVER['HTTP_HOST']);
		if (!$info) {
			throw new \Exception('domain: '.$_SERVER['HTTP_HOST'].' was not exist!');
		}
		self::$data['domain'] = $info;
		self::$data['router'] = frame('Router')->analyze($info['class']);
		$router = self::$data['router'];
		if (self::middleware($router)) {
			$call = self::make('app/controller/'.$router['class'].'/'.$router['path']);
			if (method_exists($call, $router['func'])) {
				$call->{$router['func']}();
			} else {
				throw new \Exception('class: '.$router['class'].'/'.$router['path'].'/'.$router['func'].' was not exist!');
			}
		}
		self::runOver();
	}

	private static function middleware($request)
	{
		$except = config('except', $request['class']);

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$session = frame('Session');
		$isHome = $request['class'] === 'home';
		if (!$session->get('set_uuid', false)) {
			frame('Cookie')->setUuid($isHome);
		}
		// 语言/货币 Cookie 仅前端需要
		if ($isHome && $request['path'] !== 'Api' && $request['path'] !== 'Login' && !$session->get('set_cookie', false)) {
			frame('Cookie')->init(true);
		}

		if (isset($except[$request['path']]) || isset($except[$request['path'].'/'.$request['func']])) return true;

		if (userId() < 1) {
			if (isAjax()) {
				self::jsonResponse(400, 'need login');
			} else {
				redirect(url('login'));
			}
			return false;
		}
		return true;
	}

	public static function autoload($abstract)
	{
		if (is_file(ROOT_PATH.$abstract.'.php')) {
			require ROOT_PATH.$abstract.'.php';
		} else {
			throw new \Exception('file: '.$abstract.' was not exist!');
		}
	}

	public static function set($name, $value, $key=null)
	{
		if ($key) self::$data[$name][$key] = $value;
		else self::$data[$name] = $value;	
	}

	public static function get($name, $key=null, $default=null)
	{
		if (is_null($key)) return self::$data[$name] ?? $default;
		else return !isset(self::$data[$name][$key]) ? $default : self::$data[$name][$key];
	}

	public static function append($name, $value)
	{
		self::$data[$name][] = $value;
	}

	public static function runOver($ajax=false)
	{
		config('domain', 'log') && frame('Debug')->runlog(isCli()?'task':'');
		isDebug() && !$ajax && frame('Debug')->init();
		exit();
	}

	// 保留旧方法名兼容
	public static function jsonRespone($code, $data=[], $msg='')
	{
		return self::jsonResponse($code, $data, $msg);
	}

	public static function jsonResponse($code, $data=[], $msg='')
	{
		header('Content-Type:application/json;charset=utf-8');
		echo json_encode(['code' => $code, 'data' => $data, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
		self::runOver(true);
	}

	public static function error($msg='')
	{
		if ($msg) {
			self::$_error[] = $msg;
		}
		return self::$_error;
	}
}