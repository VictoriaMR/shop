<?php
class App 
{
	public static function init() 
	{
		spl_autoload_register([__CLASS__ , 'autoload']);
		self::make('frame/Error')->register();
	}

	public static function run() 
	{
		self::init();
		self::send();
	}

	public static function make($abstract, $params=null)
	{
		return self::autoload($abstract, $params);
	}

	private static function send()
	{
		//路由解析
		$info = router()->analyze()->getRoute();
		//执行方法
		$class = 'app\\controller\\'.$info['class'].'\\'.$info['path'].'';

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

	public static function runOver()
	{
		if (env('APP_DEBUG')) {
			debug()->runlog();
			if (!IS_CLI && !IS_AJAX) {
				$router = router()->getRoute();
				if (!($router['path'] == 'index' && $router['func'] == 'index')) {
					debug()->init();
				}
			}
		}
		exit();
	}
}