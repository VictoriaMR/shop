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
				self::autoload($abstract);
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
		if ($info) {
			self::set('domain', $info);
			//路由解析
			$info = frame('Router')->analyze($info['class']);
			self::set('router', $info);
			// 中间组件方法
			if (self::middleware($info)) {
				//执行方法
				$call = self::make('app/controller/'.$info['class'].'/'.$info['path']);
				if (method_exists($call, $info['func'])) {
					$call->{$info['func']}();
				} else {
					throw new \Exception('class: '.$info['class'].'/'.$info['path'].'/'.$info['func'].' was not exist!');
				}
			}
			self::runOver();
		} else {
			throw new \Exception('domain: '.$_SERVER['HTTP_HOST'].' was not exist!');
		}
	}

	// 中间组件方法
	private static function middleware($request)
	{
		// 白名单优先判断
		$except = config('except', $request['class']);

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
		$session = frame('Session');
		$isHome = $request['class'] === 'home';
		if (!$session->get('set_uuid', false)) {
			frame('Cookie')->setUuid($isHome);
		}
		if ($request['path'] !== 'Api' && $request['path'] !== 'Login' && !$session->get('set_cookie', false)) {
			frame('Cookie')->init($isHome);
		}

		if (isset($except[$request['path']]) || isset($except[$request['path'].'/'.$request['func']])) return true;

		// 需要登录的要重定向
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

	private static function autoload($abstract)
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
		echo json_encode(array(
			'code' => $code,
			'data' => $data,
			'msg' => $msg,
		), JSON_UNESCAPED_UNICODE);
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