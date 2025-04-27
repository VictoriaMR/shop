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
		$instance = self::get('autoload', $abstract);
		if (!$instance) {
			self::autoload($abstract);
			// 实例化对象
			$concrete = strtr($abstract, '/', '\\');
			if ($concrete instanceof Closure) {
				$instance = $concrete($this);
			} else {
				$reflector = new \ReflectionClass($concrete);
				if ($reflector->getConstructor()) {
					$instance = $reflector->newInstance($params);
				} else {
					$instance = $reflector->newInstance();
				}
			}
			self::set('autoload', $instance, $abstract);
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
				$callArr = [$call, $info['func']];
				if (is_callable($callArr)) {
					call_user_func_array($callArr, []);
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
		if (!frame('Session')->get('set_uuid', false)) {
			frame('Cookie')->setUuid($request['class'] == 'home');
		}
		// 验证是否需要自动登录
		if (!in_array($request['path'], ['Api','Login']) && !frame('Session')->get('set_cookie', false)) {
			frame('Cookie')->init($request['class'] == 'home');
		}
		// 如果无需登录的, 初始化
		$except = config('except', $request['class']);
		if (isset($except[$request['path']])) return true;
		if (isset($except[$request['path'].'/'.$request['func']])) return true;
		// 需要登录的要重定向
		if (userId() < 1) {
			if (isAjax()) {
				self::jsonRespone(400, 'need login');
			} else {
				redirect(url('login'));
			}
			return false;
		}
		return true;
	}

	private static function autoload($abstract, $params=null)
	{
		if (is_file(ROOT_PATH.$abstract.'.php')) {
			require(ROOT_PATH.$abstract.'.php');
		} else {
			throw new \Exception('file: '.$abstract.'was not exist!');
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
		else return empty(self::$data[$name][$key]) ? $default : self::$data[$name][$key];
	}

	public static function runOver($ajax=false)
	{
		config('domain', 'log') && frame('Debug')->runlog();
		isDebug() && !$ajax && frame('Debug')->init();
		exit();
	}

	public static function jsonRespone($code, $data=[], $msg='')
	{
		header('Content-Type:application/json;charset=utf-8');
		echo json_encode(array(
			'code' => $code,
			'data' => $data,
			'msg' => $msg,
		), JSON_UNESCAPED_UNICODE);
		self::runOver(true);
	}

	public static function error($msg)
	{
		if ($msg) {
			self::$_error[] = $msg;
		}
		return self::$_error;
	}
}