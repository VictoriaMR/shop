<?php
class App 
{
	private static $appData = [];

	public static function init() 
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
		self::make('frame/Error')->register();
	}

	public static function make($abstract, $params=null)
	{
		return self::autoload($abstract, $params);
	}

	public static function send()
	{
		//获取站点数据
		$info = config('domain.'.str_replace('www.', '', $_SERVER['HTTP_HOST'] ?? ''));
		if (empty($info)) redirect(config('env.DEFAULT_DOMAIN'));
		define('APP_CONTROLLER_TYPE', $info['path'] == 'admin' ? 'admin' : 'home');
		define('APP_TEMPLATE_TYPE', $info['path']);
		define('APP_SITE_ID', $info['site_id']);
		self::set('site_name', $info['name']);
		//路由解析
		self::make('frame/Router')->analyze();
		$info = self::get('router');
		//执行方法
		$class = 'app/controller/'.$info['class'].'/'.$info['path'];
		$callArr = [self::autoload($class), $info['func']];
		if (is_callable($callArr)) {
			if (!session()->get('cookie.setcookie')) {
				//Cookie初始化
				self::make('frame/Cookie')->init();
			}
			//中间件
			self::make('app/middleware/VerifyToken')->handle($info);
			call_user_func_array($callArr, []);
		} else {
			throw new \Exception($class.' '.$info['func'].' was not exist!', 1);
		}
		self::runOver();
	}

	private static function autoload($abstract, $params=null) 
	{
		$file = ROOT_PATH.str_replace('\\', DS, $abstract).'.php';
		if (is_file($file)) {
			return \frame\Container::instance()->autoload(str_replace(DS, '\\', $abstract), $file, $params);
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
		if (config('env.APP_DEBUG')) {
			make('frame/Debug')->runlog();
			if (!(IS_CLI || IS_AJAX)) make('frame/Debug')->init();
		}
		exit();
	}
}