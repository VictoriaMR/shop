<?php

namespace app\controller;

class Base
{
	protected $_nav;
	protected $_tag;
	protected $_arr;
	protected $_default;
	protected $_tagShow = true;

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
		$this->result('10000', [], ['message' => $message]);
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

	protected function _init()
	{
		$this->_tag = $this->_arr;
		$router = \App::get('router');
		if ($router['class'] == 'admin') {
			if ($router['path'] !== 'index') {
				if (empty($this->_default)) {
					$this->_nav = $this->_arr;
				} else {
					$this->_nav = array_merge(['default' => $this->_default], $this->_arr);
				}
				$this->assign('_tag', $this->_tag);
				$this->assign('_nav', $this->_nav);
				$this->assign('_tagShow', $this->_tagShow);
			}
		}
		$this->assign('_path', $router['path']);
		$this->assign('_func', $router['func']);
		$this->assign('_title', $this->_tag[$router['func']] ?? '');
	}

	protected function addLog($msg)
	{
		$data = [
			'remark' => $msg,
			'type' => 3,
		];
		make('app/service/admin/Logger')->addLog($data);
	}

	protected function transfer()
	{
		$trCode = ipost('tr_code');
		$name = ipost('name');
		if (empty($trCode) || empty($name)) {
			$this->error('参数错误');
		}
		$rst = make('app/service/Translate')->getTranslate($name, $trCode);
		$this->success($rst);
	}
}
