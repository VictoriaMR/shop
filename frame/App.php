<?php

use frame\Container;

class App
{
	private static $data = [];
	private static $error = [];

	public static function init()
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
		self::make('frame/Error')->register();
	}

	public static function make($abstract, $params=null, $static=true)
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
				if ($reflector->isInstantiable()) {
					if (is_null($reflector->getConstructor())) {
						$instance = $reflector->newInstance();
					} else {
						$instance = $reflector->newInstance($params);
					}
				} else {
					throw new \Exception($concrete.' is not instantiable!', 1);
				}
			}
			$static && self::set('autoload', $instance, $abstract);
		}
		return $instance;
	}

	public static function send()
	{
		$domain = $_SERVER['HTTP_HOST'] ?? '';
		$info = config('domain', $domain);
		if (empty($info)) throw new \Exception('domain: '.$domain.' was not exist!', 1);
		$info['domain'] = $domain;
		self::set('base_info', $info);
		//路由解析
		$info = self::make('frame/Router')->analyze();
		$info['class'] = isAdmin()?'admin':'home';
		self::set('router', $info);
		//执行方法
		$call = self::make('app/controller/'.$info['class'].'/'.$info['path']);
		$callArr = [$call, $info['func']];
		if (is_callable($callArr)) {
			self::make('app/middleware/VerifyToken')->handle($info);
			call_user_func_array($callArr, []);
		} else {
			throw new \Exception('type '.$info['class'].', class '.$info['path'].', function '.$info['func'].' was not exist!', 1);
		}
		self::runOver();
	}

	private static function autoload($abstract, $params=null)
	{
		$file = ROOT_PATH.strtr($abstract, '\\', DS).'.php';
		if (is_file($file)) require $file;
		else throw new \Exception($file.' to autoload '.$abstract.' was failed!', 1);
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

	public static function runOver()
	{
		if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
		if (isDebug()) {
			make('frame/Debug')->runlog();
			!isCli() && !isAjax() && make('frame/Debug')->init();
		}
		exit();
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
}