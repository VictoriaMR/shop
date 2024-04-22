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
		http_response_code($code);
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode(array_merge($data, $options), JSON_UNESCAPED_UNICODE);
		\App::runOver(false);
	}

	protected function success($msg='', $data=[])
	{
		if (!is_string($msg)) {
			$data = $msg;
			$msg = '';
		}
		$this->result(200, $data, ['msg' => $msg]);
	}

	protected function error($msg='', $code=400)
	{
		$this->result($code, [], ['msg'=>$msg]);
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