<?php

namespace app\controller;

class Base
{
	protected function result($code, $data=[], $options=[])
	{
		$data = [
			'code' => $code,
			'data' => $data,
			'message' => '',
		];
		header('Content-Type:application/json; charset=utf-8');
		echo json_encode(array_merge($data, $options), JSON_UNESCAPED_UNICODE);
		\App::runOver();
		exit();
	}

	protected function success($data=[], $options=null)
	{
		if (!is_array($data) && !$options) {
			$options = $data;
		}
		$options = ['message' => $options];
		$this->result('200', $data, $options);
	}

	protected function error($message='', $code='10000')
	{
		if (!$message) {
			$message = 'error';
		}
		$this->result($code, [], ['message'=>$message]);
	}

	protected function assign($name, $value=null)
	{
		if ($name == '_title') $value .= ' - '.\App::get('base_info', 'name');
		return make('frame/View')->assign($name, $value);
	}

	protected function view($cache=false)
	{
		return make('frame/View')->display('', true, $cache);
	}
}