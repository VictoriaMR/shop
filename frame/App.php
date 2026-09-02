<?php

class App
{
	private static $data = [];
	private static $instances = [];
	private static $domain = [];
	private static $_error = [];

	private static $classMap = [
		'frame\\App' => 'frame/App.php',
		'frame\\Connection' => 'frame/Connection.php',
		'frame\\Cookie' => 'frame/Cookie.php',
		'frame\\Debug' => 'frame/Debug.php',
		'frame\\Error' => 'frame/Error.php',
		'frame\\Helper' => 'frame/Helper.php',
		'frame\\Html' => 'frame/Html.php',
		'frame\\Http' => 'frame/Http.php',
		'frame\\IP' => 'frame/IP.php',
		'frame\\Locker' => 'frame/Locker.php',
		'frame\\Paginator' => 'frame/Paginator.php',
		'frame\\Query' => 'frame/Query.php',
		'frame\\Redis' => 'frame/Redis.php',
		'frame\\Request' => 'frame/Request.php',
		'frame\\Router' => 'frame/Router.php',
		'frame\\Session' => 'frame/Session.php',
		'frame\\Task' => 'frame/Task.php',
		'frame\\Time' => 'frame/Time.php',
		'frame\\View' => 'frame/View.php',
	];

	public static function init()
	{
		spl_autoload_register([__CLASS__, 'autoload'], true, true);
		frame('Error')->register();
	}

	public static function make($abstract, $params = null)
	{
		if ($params === null) {
			$concrete = is_string($abstract)
				? (strpos($abstract, '/') !== false ? strtr($abstract, '/', '\\') : $abstract)
				: null;

			$key = $concrete ?? $abstract;
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}

			if ($abstract instanceof \Closure) {
				$instance = $abstract();
			} else {
				$instance = new $concrete();
			}
			return self::$instances[$key] = $instance;
		}

		if ($abstract instanceof \Closure) {
			return $abstract($params);
		}
		$concrete = strpos($abstract, '/') !== false ? strtr($abstract, '/', '\\') : $abstract;
		return new $concrete($params);
	}

	public static function send()
	{
		$rawHost = $_SERVER['HTTP_HOST'] ?? '';
		if ($rawHost !== '') {
			$pos = strpos($rawHost, ':');
			$host = strtolower($pos !== false ? substr($rawHost, 0, $pos) : $rawHost);
		} else {
			$host = '';
		}

		$info = config('domain', $host);
		if (!$info) {
			$domains = config('domain');
			if (is_array($domains) && !empty($domains)) {
				$info = $domains[$host] ?? reset($domains);
			}
		}

		if (!$info) {
			throw new \Exception('Domain host: ' . ($rawHost ?: 'unknown') . ' does not exist!');
		}

		self::$domain = $info;
		self::$data['domain'] = $info;

		$router = frame('Router')->analyze($info['class']);
		self::$data['router'] = $router;

		if (self::middleware($router)) {
			$classPath = 'app\\controller\\' . $router['class'] . '\\' . $router['path'];
			$call = self::make($classPath);
			$func = $router['func'];
			if (method_exists($call, $func)) {
				$call->$func();
			} else {
				throw new \Exception('Action not found: ' . $classPath . '::' . $func);
			}
		}

		self::runOver();
	}

	private static function middleware($request)
	{
		$except = config('except', $request['class']);

		if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
			@session_start();
		}

		$session = frame('Session');
		$isHome = ($request['class'] === 'home');

		if (!$session->get('set_uuid', false)) {
			frame('Cookie')->setUuid($isHome);
		}

		// 语言/货币 Cookie 仅前端需要
		if ($isHome && $request['path'] !== 'Api' && $request['path'] !== 'Login' && !$session->get('set_cookie', false)) {
			frame('Cookie')->init(true);
		}

		if (isset($except[$request['path']]) || isset($except[$request['path'] . '/' . $request['func']])) {
			return true;
		}

		if (userId() < 1) {
			if (isAjax()) {
				self::jsonResponse(400, [], 'need login');
			} else {
				redirect(url('login'));
			}
			return false;
		}

		return true;
	}

	public static function autoload($abstract)
	{
		if (isset(self::$classMap[$abstract])) {
			require ROOT_PATH . self::$classMap[$abstract];
			return;
		}

		$file = ROOT_PATH . strtr($abstract, '\\', '/') . '.php';
		if (is_file($file)) {
			require $file;
		}
	}

	public static function set($name, $value, $key = null)
	{
		if ($key !== null) {
			self::$data[$name][$key] = $value;
		} else {
			self::$data[$name] = $value;
		}
	}

	public static function get($name, $key = null, $default = null)
	{
		if ($key === null) {
			return self::$data[$name] ?? $default;
		}
		return self::$data[$name][$key] ?? $default;
	}

	public static function append($name, $value)
	{
		self::$data[$name][] = $value;
	}

	public static function runOver($ajax = false)
	{
		if (isDebug() && !$ajax && self::shouldShowTrace()) {
			frame('Debug')->init();
		}

		// FastCGI 环境下立即将响应输出冲刷给客户端，后续耗时任务后台异步完成
		if (function_exists('fastcgi_finish_request')) {
			fastcgi_finish_request();
		}

		$logEnabled = self::$domain['log'] ?? self::get('domain', 'log', false);
		if ($logEnabled) {
			frame('Debug')->runlog(isCli() ? 'task' : '');
		}

		exit();
	}

	private static function shouldShowTrace()
	{
		$explicitTrace = self::get('page_trace', null);
		if ($explicitTrace !== null) {
			return (bool)$explicitTrace;
		}
		$router = self::get('router');
		if (!empty($router) && $router['path'] === 'Index' && $router['func'] === 'index') {
			return false;
		}
		return true;
	}

	public static function jsonResponse($code, $data = [], $msg = '')
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['code' => $code, 'data' => $data, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
		self::runOver(true);
	}

	public static function error($msg = '')
	{
		if ($msg) {
			self::$_error[] = $msg;
		}
		return self::$_error;
	}
}