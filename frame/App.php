<?php

use frame\Container;

class App
{
	private static $appData = [];

	public static function init()
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
		self::make('frame/Error')->register();
	}

	public static function make($abstract, $params=null, $static=true)
	{
		if ($static) {
			if (!self::get('autoload', $abstract)) {
				self::autoload($abstract);
				self::set('autoload', Container::instance()->autoload(strtr($abstract, DS, '\\'), $params), $abstract);
			}
			return self::get('autoload', $abstract);
		} else {
			self::autoload($abstract);
			return Container::instance()->autoload(strtr($abstract, DS, '\\'), $params);
		}
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
		if (isDebug()) {
			make('frame/Debug')->runlog();
			if (self::get('base_info', 'debug') && !isCli() && !isAjax()) make('frame/Debug')->init();
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
}