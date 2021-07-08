<?php

namespace app\middleware;

class VerifyToken
{
	private $except = [
		'admin' => [
			'login' => true,
		],
		'prettybag' => [
			'index' => true,
			'login' => true,
		],
	];

	public function handle($request)
	{
		if ($this->inExceptArray($request)) {
			return true;
		}
		$loginKey = env('APP_TEMPLATE_TYPE').'_mem_id';
		//检查登录状态
		if (empty(session()->get($loginKey))) {
			session()->set('callback_url', rtrim($_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']), '?');
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
		$class = strtolower($route['class']);
		if (empty($this->except[$class])) {
			return false;
		}
		$path = strtolower($route['path']);
		if (!empty($this->except[$class][$path])) {
			return true;
		}
		if (!empty($this->except[$class][$path.'/'.$route['func']])) {
			return true;
		}
		return false;
	}
}
