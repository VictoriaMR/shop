<?php

namespace app\controller;

class HomeBase extends Base
{
	public function __construct()
	{
		$class = 'template/'.APP_TEMPLATE_TYPE.'/controller/'.(IS_MOBILE?'mobile/':'computer/').\App::get('router', 'path');
		if (ROOT_PATH.$class.'.php') {
			call_user_func_array([make($class), \App::get('router', 'func')], []);
		}
	}
}