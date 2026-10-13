<?php

class App
{
	private static $data = [];
	private static $instances = [];
	private static $domain = [];
	private static $_error = [];

	public static function init()
	{
		spl_autoload_register([__CLASS__, 'autoload'], true, true);
		frame('Error')->register();
	}

	public static function make($abstract)
	{
		if (isset(self::$instances[$abstract])) {
			return self::$instances[$abstract];
		}
		$concrete = strtr($abstract, '/', '\\');
		return self::$instances[$abstract] = new $concrete();
	}

	public static function send()
	{
		$host = $_SERVER['HTTP_HOST'] ?? '';
		if ($host) {
			$host = strtolower(strstr($host, ':', true) ?: $host);
		}
		self::$data['domain'] = config('domain', $host, false);
		if (!self::$data['domain']) {
			throw new \Exception('Domain host: [' . $host . '] does not exist!');
		}
		// 分析路由
		$router = frame('Router')->analyze(self::$data['domain']['class']);
		self::$data['router'] = $router;
		//中间件校验
		if (self::middleware($router)) {
			self::make('app/controller/' . $router['class'] . '/' . $router['path'])->{$router['func']}();
		}
		//结束输出
		self::runOver();
	}

	private static function middleware($request)
	{
		$except = config('except', $request['class']);
		if (isset($except[$request['path']]) || isset($except[$request['path'] . '/' . $request['func']])) {
			return true;
		}
		$rst = frame('Cookie')->init($request['class'] === 'home');
		if ($rst) {
			return true;
		}
		if (isAjax()) {
			self::jsonResponse(400, [], 'need login');
		} else {
			redirect(url('login'));
		}
		return false;
	}

	public static function autoload($abstract)
	{
		require ROOT_PATH . strtr($abstract, '\\', '/') . '.php';
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