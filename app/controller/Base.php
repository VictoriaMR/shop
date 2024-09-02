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

	protected function view($data=array())
	{
		return frame('View')->display('', true, $data);
	}
}