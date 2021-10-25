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
	}

	protected function success($data=[], $options=null)
	{
		if (is_array($data)) {
			if (is_null($options)) {
				$options = 'success';
			}
		} else {
			if (is_null($options)) {
				$options = $data;
			}
		}
		$options = ['message' => $options];
		$this->result('200', $data, $options);
	}

	protected function error($message='')
	{
		if (empty($message)) {
			$message = 'error';
		}
		$this->result('10000', [], ['message'=>$message]);
	}

	protected function assign($name, $value=null)
	{
		if ($name == '_title') {
			$value .= '-'.\App::get('site_name');
		}
		return make('frame/View')->assign($name, $value);
	}

	protected function view($name='')
	{
		return make('frame/View')->display($name);
	}
}