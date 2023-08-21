<?php

namespace app\middleware;

class VerifyToken
{
	public function handle($request)
	{
		session()->get('setcookie', false) || make('frame/Cookie')->init();
		if ($this->inExceptArray($request)) return true;
		userId() || redirect(url('login'));
		return true;
	}

	private function inExceptArray($route)
	{
		$except = config('except', $route['class']);
		if (isset($except[$route['path']])) return true;
		if (isset($except[$route['path'].'/'.$route['func']])) return true;
		return false;
	}
}