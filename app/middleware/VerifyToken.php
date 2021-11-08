<?php

namespace app\middleware;

class VerifyToken
{
	public function handle($request)
	{
		if ($this->inExceptArray($request)) {
			return true;
		}
		if (empty(userId())) {
			if (!request()->isPost()) {
				session()->set('callback_url', str_replace('login.html', '', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
			}
			if (IS_AJAX) {
				header('Content-Type:application/json;charset=utf-8');
				echo json_encode(['code'=>'10001', 'data'=>'', 'message' => 'need login'], JSON_UNESCAPED_UNICODE);
				exit();
			} else {
				redirect(url('login'));
			}
		}
		return true;
	}

	private function inExceptArray($route)
	{
		//没有在排除的都要求登录
		$except = config('except.'.strtolower($route['class']));
		$path = strtolower($route['path']);
		if (!empty($except[$path])) {
			return true;
		}
		if (!empty($except[$path.'/'.$route['func']])) {
			return true;
		}
		return false;
	}
}