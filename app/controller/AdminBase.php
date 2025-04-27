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
		$data = [];
		if ($router['class'] == 'admin') {
			if ($router['path'] != 'Index') {
				if (empty($this->_default)) {
					$this->_nav = $this->_arr;
				} else {
					$this->_nav = ['default'=>$this->_default]+$this->_arr;
				}
				$data['_ignore'] = $this->_ignore;
				$data['_tag'] = $this->_arr;
				$data['_nav'] = $this->_nav;
			}
		}
		$data['_path'] = $router['path'];
		$data['_func'] = $router['func'];
		$data['_title'] = $this->_arr[$router['func']] ?? '';
		frame('View')->setData($data);
	}

	protected function transfer()
	{
		$trCode = ipost('tr_code');
		$fromCode = ipost('from_code', 'zh');
		$name = ipost('name');
		if (empty($trCode) || empty($name)) {
			$this->error('参数错误');
		}
		$rst = service('Translate')->getText($name, $trCode, $fromCode);
		$this->success('', $rst);
	}

	public function addLog($info='')
	{
		return service('log/Login')->addLog($info);
	}

	protected function avatar($info)
	{
		if (empty($info)) return '';
		$img = service('member/Member')->getAvatar($info['avatar'], $info['sex']);
		return '<div class="userAvatarInfo"><span class="avatarStyle"><img src="'.$img.'"></span><span class="two"><p>'.$info['nick_name'].'</p><p>'.$info['mem_id'].'</p></span></div>';
	}
}