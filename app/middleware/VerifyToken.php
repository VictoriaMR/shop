<?php

namespace app\middleware;

class VerifyToken
{
	public function handle($request)
	{
		if ($this->inExceptArray($request)) return true;
		if (empty(userId())) redirect(url('login'));
		return true;
	}

	private function inExceptArray($route)
	{
		$except = config('except', $route['class']);
		if (!empty($except[$route['path']])) return true;
		if (!empty($except[$route['path'].'/'.$route['func']])) return true;
		return false;
	}
}