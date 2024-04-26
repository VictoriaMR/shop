<?php

use frame\Container;

class App
{
	private static $data = [];
	public static $success = [];
	public static $error = [];

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
					throw new \Exception($concrete.' is not instantiable!');
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
		if ($info) {
			$info['domain'] = $domain;
			self::set('domain', $info);
			//路由解析
			$info = frame('Router')->analyze();
			$info['class'] = isAdmin()?'admin':'home';
			self::set('router', $info);
			// 中间组件方法
			if (self::middleware($info)) {
				//执行方法
				$call = self::make('app/controller/'.$info['class'].'/'.$info['path']);
				$callArr = [$call, $info['func']];
				call_user_func_array($callArr, []);
			}
			self::runOver();
		} else {
			throw new \Exception('domain: '.$domain.' was not exist!');
		}
	}

	// 中间组件方法
	private static function middleware($request)
	{
		if (!session()->get('set_uuid', false)) {
			frame('Cookie')->setUuid($request['class'] == 'home');
		}
		// 验证是否需要自动登录
		if (!in_array($request['path'], ['Api','Login']) && !session()->get('set_cookie', false)) {
			frame('Cookie')->init($request['class'] == 'home');
		}
		// 如果无需登录的, 初始化
		$except = config('except', $request['class']);
		if (isset($except[$request['path']])) return true;
		if (isset($except[$request['path'].'/'.$request['func']])) return true;
		// 需要登录的要重定向
		if (!userId()) {
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

	public static function error($msg)
	{
		self::$error[] = $msg;
	}

	public static function runOver()
	{
		// debug开启
		if (isDebug()) {
			frame('Debug')->runlog();
		}
		if (isAjax()) {
			exit();
		}
		if (isDebug() && !isCli() && !iget('iframe', false)) {
			frame('Debug')->init();
		}
	}

	public static function jsonRespone($code, $data=[], $msg='')
	{
		$data = [
			'code' => $code,
			'data' => $data,
			'msg' => $msg,
		];
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
		self::runOver();
	}
}