<?php

namespace app\controller;

class Base
{
	/**
	 * 在控制器/方法中显式开启或关闭 PageTrace 引入
	 *
	 * @param bool $enable
	 * @return $this
	 */
	protected function trace($enable = true)
	{
		\App::set('page_trace', (bool)$enable);
		return $this;
	}

	protected function success($msg='', $data=[], $code=1)
	{
		\App::jsonResponse($code, $data, $msg);
	}

	protected function error($msg='', $data=[], $code=0)
	{
		\App::jsonResponse($code, [], $msg);
	}

	protected function view($data=array())
	{
		if (isset($data['_title'])) {
			$data['_title'] .= ' - '.\App::get('domain', 'name');
		}
		return frame('View')->display('', true, $data);
	}

	protected function assign($name, $value) 
	{
		return frame('View')->setData([$name=>$value]);
	}
}