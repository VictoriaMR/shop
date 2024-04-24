<?php

namespace app\controller;

class Base
{
	protected function success($msg='', $data=[], $code=200)
	{
		\App::jsonRespone($code, $data, $msg);
	}

	protected function error($msg='', $data=[], $code=400)
	{
		\App::jsonRespone($code, [], $msg);
	}

	protected function assign($name, $value=null)
	{
		return frame('View')->assign($name, $value);
	}

	protected function view($cache=false)
	{
		return frame('View')->display('', true, $cache);
	}
}