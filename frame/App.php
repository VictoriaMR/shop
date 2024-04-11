<?php

use frame\Container;

class App
{
	private static $data = [];
	public static $success = [];
	public static $error = [];

	public static function init()
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
		self::make('frame/Error')->register();
	}

	public static function make($abstract, $params=null)
	{
		$instance = self::get('autoload', $abstract);
		if (!$instance) {
			self::autoload($abstract);
			// 实例化对象
			$concrete = strtr($abstract, DS, '\\');
			if ($concrete instanceof Closure) {
				$instance = $concrete($this);
			} else {
				$reflector = new \ReflectionClass($concrete);
				!$reflector->isInstantiable() && throw new \Exception($concrete.' is not instantiable!');
				if (is_null($reflector->getConstructor())) {
					$instance = $reflector->newInstance();
				} else {
					$instance = $reflector->newInstance($params);
				}
			}
			self::set('autoload', $instance, $abstract);
		}
		return $instance;
	}

	public static function send()
	{
		$domain = $_SERVER['HTTP_HOST'] ?? '';
		$info = config('domain', $domain);
		!$info && throw new \Exception('domain: '.$domain.' was not exist!');
		$info['domain'] = $domain;
		self::set('base_info', $info);
		//路由解析
		$info = self::make('frame/Router')->analyze();
		$info['class'] = isAdmin()?'admin':'home';
		self::set('router', $info);
		//执行方法
		$call = self::make('app/controller/'.$info['class'].'/'.$info['path']);
		$callArr = [$call, $info['func']];
		self::make('app/middleware/VerifyToken')->handle($info);
		call_user_func_array($callArr, []);
		self::runOver();
	}

	private static function autoload($abstract, $params=null)
	{
		require(ROOT_PATH.strtr($abstract, '\\', DS).'.php');
	}

	public static function set($name, $value, $key=null)
	{
		if ($key) self::$data[$name][$key] = $value;
		else self::$data[$name] = $value;	
	}

	public static function get($name, $key=null)
	{
		if (is_null($key)) return self::$data[$name] ?? null;
		else return empty(self::$data[$name][$key]) ? null : self::$data[$name][$key];
	}

	public static function setVersion($version)
	{
		return redis(2)->set('frame:app:version', substr($version, 0, 5));
	}

	public static function getVersion()
	{
		defined('APP_VERSION') || define('APP_VERSION', redis(2)->get('frame:app:version')?:'1.0.0');
		return APP_VERSION;
	}

	public static function error($msg)
	{
		self::$error[] = $msg;
	}

	public static function runOver()
	{
		// debug开启
		if (isDebug()) {
			make('frame/Debug')->runlog();
			!isCli() && !isAjax() && !iget('iframe', false) && make('frame/Debug')->init();
		}
	}
}