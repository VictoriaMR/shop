<?php

namespace app\controller;

class Base
{
	protected function result($code, $data=[], $options=[])
	{
		$data = [
			'code' => $code,
			'data' => $data,
			'msg' => '',
		];
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode(array_merge($data, $options), JSON_UNESCAPED_UNICODE);
		\App::runOver();
		exit();
	}

	protected function success($msg='', $data=[])
	{
		if (!is_string($msg)) {
			$data = $msg;
			$msg = '';
		}
		$this->result('200', $data, ['msg' => $msg]);
	}

	protected function error($msg='', $code='10000')
	{
		$this->result($code, [], ['msg'=>$msg]);
	}

	protected function assign($name, $value=null)
	{
		return make('frame/View')->assign($name, $value);
	}

	protected function view($cache=false)
	{
		return make('frame/View')->display('', true, $cache);
	}
}