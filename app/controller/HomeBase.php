<?php

namespace app\controller;

class HomeBase extends Base
{
	public function __construct()
	{
		$class = 'template/'.APP_TEMPLATE_TYPE.'/controller/'.(IS_MOBILE?'mobile/':'computer/').\App::get('router', 'path');
		if (is_file(ROOT_PATH.$class.'.php')) {
			$callArr = [\App::make($class), \App::get('router', 'func')];
			if (is_callable($callArr)) {
				call_user_func_array($callArr, []);
			}
		}
	}
}