<?php

namespace app\controller;

class AdminBase extends Base
{
	protected $_nav;
	protected $_tag;
	protected $_arr;
	protected $_default;
	protected $_tagShow = true;

	protected function _init()
	{
		$this->_tag = $this->_arr;
		$router = \App::get('router');
		if ($router['class'] == 'admin') {
			if ($router['path'] !== 'Index') {
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
		$rst = make('app/service/Translate')->getText($name, $trCode);
		$this->success($rst);
	}
}