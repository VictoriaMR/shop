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
		return self::autoload($abstract, $params);
		if (!self::get('autoload', $abstract)) {
			self::set('autoload', self::autoload($abstract, $params), $abstract);
		}
		return self::get('autoload', $abstract);
	}

	public static function send()
	{
		$baseInfo = self::get('base_info');
		if (empty($baseInfo)) throw new \Exception($_SERVER['HTTP_HOST'].' was not exist!', 1);
		//路由解析
		define('IS_ADMIN', $baseInfo['site_id'] == 10);
		$router = self::make('frame/Router')->analyze();
		$router['class'] = $baseInfo['type'];
		$router['view_suffix'] = $baseInfo['view_suffix'];
		define('APP_TEMPLATE_TYPE', $baseInfo['type']);
		define('APP_TEMPLATE_PATH', $baseInfo['path']);
		define('APP_DOMAIN', 'https://'.$baseInfo['domain'].'/');
		self::set('router', $router);
		//执行方法
		$callArr = [self::autoload('app/controller/'.$router['class'].'/'.$router['path']), $router['func']];
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

	private static function getDomain()
	{
		return self::make('app/service/site/Site')->getInfoCache(str_replace('www.', '', $_SERVER['HTTP_HOST']), 'domain');
	}

	private static function autoload($abstract, $params=null) 
	{
		$abstract = strtr($abstract, '\\', DS);
		if (!self::get('autoload', $abstract)) {
			$file = ROOT_PATH.$abstract.'.php';
			if (is_file($file)) {
				self::set('autoload', \frame\Container::instance()->autoload(strtr($abstract, DS, '\\'), $file, $params), $abstract);
			} else {
				throw new \Exception($file.' to autoload '.$abstract.' was failed!', 1);
			}
		}
		return self::get('autoload', $abstract);

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
			if (!IS_CLI && !IS_AJAX) make('frame/Debug')->init();
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