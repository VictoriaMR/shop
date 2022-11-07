<?php

namespace app\controller;

class AdminBase extends Base
{
	protected $_nav = [];
	protected $_ignore = [];
	protected $_arr = [];
	protected $_default;

	protected function _init()
	{
		$router = \App::get('router');
		if ($router['class'] == 'admin') {
			if ($router['path'] !== 'Index') {
				if (empty($this->_default)) {
					$this->_nav = $this->_arr;
				} else {
					$this->_nav = ['default' => $this->_default]+$this->_arr;
				}
				$this->assign('_ignore', $this->_ignore);
				$this->assign('_tag', $this->_arr);
				$this->assign('_nav', $this->_nav);
			}
		}
		$this->assign('_path', $router['path']);
		$this->assign('_func', $router['func']);
		$this->assign('_title', $this->_arr[$router['func']] ?? '');
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
		$fromCode = ipost('from_code', 'zh');
		$name = ipost('name');
		if (empty($trCode) || empty($name)) {
			$this->error('参数错误');
		}
		$rst = make('app/service/Translate')->getText($name, $trCode, $fromCode);
		$this->success('', $rst);
	}
}