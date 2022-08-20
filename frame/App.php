<?php
class App 
{
	private static $appData = [];

	public static function init() 
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
		if (!IS_CLI) self::set('base_info', self::getDomain());
		self::make('frame/Error')->register();
	}

	public static function make($abstract, $params=null)
	{
		if (!self::get('autoload', $abstract)) {
			self::autoload($abstract);
			self::set('autoload', \frame\Container::instance()->autoload(strtr($abstract, DS, '\\'), '', $params), $abstract);
		}
		return self::get('autoload', $abstract);
	}

	public static function send()
	{
		$baseInfo = self::get('base_info');
		if (empty($baseInfo)) throw new \Exception('domain: '.$_SERVER['HTTP_HOST'].' was not exist!', 1);
		//路由解析
		define('IS_ADMIN', $baseInfo['site_id'] == 10);
		$router = self::make('frame/Router')->analyze();
		$router['class'] = $baseInfo['type'];
		define('APP_TEMPLATE_TYPE', $baseInfo['type']);
		define('APP_TEMPLATE_PATH', $baseInfo['path']);
		define('APP_DOMAIN', 'https://'.$baseInfo['domain'].'/');
		self::set('router', $router);
		//执行方法
		$call = self::make('app/controller/'.$router['class'].'/'.$router['path']);
		$callArr = [$call, $router['func']];
		if (is_callable($callArr)) {
			if (!session()->get('setcookie', false)) {
				self::make('frame/Cookie')->init();
			}
			self::make('app/middleware/VerifyToken')->handle($router);
			call_user_func_array($callArr, []);
		} else {
			throw new \Exception('type '.$router['class'].', class '.$router['path'].', function '.$router['func'].' was not exist!', 1);
		}
		self::runOver();
	}

	private static function getDomain()
	{
		return self::make('app/service/site/Site')->getInfoCache(str_replace('www.', '', $_SERVER['HTTP_HOST']), 'domain');
	}

	private static function autoload($abstract, $params=null) 
	{
		$file = ROOT_PATH.strtr($abstract, '\\', DS).'.php';
		if (is_file($file)) {
			require $file;
		} else {
			throw new \Exception($file.' to autoload '.$abstract.' was failed!', 1);
		}
	}

	public static function set($name, $value, $key=null)
	{
		if (is_null($key)) self::$appData[$name] = $value;
		else self::$appData[$name][$key] = $value;
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
			if (self::get('base_info', 'debug') && !IS_CLI && !IS_AJAX) make('frame/Debug')->init();
		}
	}

	public static function setVersion($version)
	{
		return redis()->set('frame-app:version', substr($version, 0, 5));
	}

	public static function getVersion()
	{
		$version = self::get('version');
		if (!$version) {
			$version = redis()->get('frame-app:version');
			self::set('version', $version);
		}
		return $version ?: '1.0.0';
	}
}