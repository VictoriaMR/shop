<?php
class App 
{
	private static $appData = [];

	public static function init() 
	{
		if (!IS_CLI) self::set('base_info', self::getConfig());
		spl_autoload_register([__CLASS__ , 'autoload']);
		self::make('frame/Error')->register();
	}

	public static function make($abstract, $params=null)
	{
		return self::autoload($abstract, $params);
	}

	public static function send()
	{
		$baseInfo = self::get('base_info');
		if (empty($baseInfo)) throw new \Exception($_SERVER['HTTP_HOST'].' was not exist!', 1);
		//路由解析
		define('IS_ADMIN', $baseInfo['type'] == 'admin');
		$router = self::make('frame/Router')->analyze();
		$router['class'] = $baseInfo['type'];
		$router['view_suffix'] = $baseInfo['view_suffix'];
		define('APP_TEMPLATE_TYPE', $baseInfo['type']);
		define('APP_TEMPLATE_PATH', $baseInfo['path']);
		define('APP_DOMAIN', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/');
		self::set('router', $router);
		//执行方法
		$controller = 'app/controller/'.$router['class'].'/'.$router['path'];
		$callArr = [self::autoload($controller), $router['func']];
		if (is_callable($callArr)) {
			if (!session()->get('setcookie', false)) {
				self::make('frame/Cookie')->init();
			}
			self::make('app/middleware/VerifyToken')->handle($router);
			call_user_func_array($callArr, []);
		} else {
			throw new \Exception($router['path'].' '.$router['func'].' was not exist!', 1);
		}
		self::runOver();
	}

	private static function getConfig()
	{
		$config = config('domain', str_replace('www.', '', $_SERVER['HTTP_HOST']));
		if (empty($config)) {
			$config = config('domain');
			return array_shift($config);
		}
		return $config;
	}

	private static function autoload($abstract, $params=null) 
	{
		$file = ROOT_PATH.strtr($abstract, '\\', DS).'.php';
		if (is_file($file)) {
			return \frame\Container::instance()->autoload(strtr($abstract, DS, '\\'), $file, $params);
		}
		throw new \Exception($file.' to autoload '.$abstract.' was failed!', 1);
	}

	public static function set($name, $value)
	{
		if (is_null($value)) unset(self::$appData[$name]);
		else self::$appData[$name] = $value;
		return true;
	}

	public static function get($name, $key=null)
	{
		if (is_null($key)) return self::$appData[$name] ?? null;
		else return empty(self::$appData[$name][$key]) ? null : self::$appData[$name][$key];
	}

	public static function runOver()
	{
		if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
		if (config('env', 'APP_DEBUG')) {
			make('frame/Debug')->runlog();
			if (!(IS_CLI || IS_AJAX)) make('frame/Debug')->init();
		}
	}
}