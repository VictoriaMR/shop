<?php

namespace app\controller;

class AdminBase extends Base
{
	protected $_nav = [];
	protected $_ignore = [];
	protected $_arr = [];
	protected $_default = '';

	protected function _init(): void
	{
		$router = \App::get('router');
		$func = $router['func'];
		$data = [
			'_path'  => $router['path'],
			'_func'  => $func,
		];
		if (isset($this->_arr[$func])) {
			$data['_title'] = $this->_arr[$func];
		}

		if ($router['class'] === 'admin' && $router['path'] !== 'Index') {
			$this->_nav = $this->_default !== ''
				? ['default' => $this->_default] + $this->_arr
				: $this->_arr;
			$data['_ignore'] = $this->_ignore;
			$data['_tag']    = $this->_arr;
			$data['_nav']    = $this->_nav;
		}

		frame('View')->setData($data);
	}

	public function addLog($info = '')
	{
		return service('log/Login')->addLog($info);
	}

	protected function avatar($info)
	{
		if (empty($info)) {
			return '';
		}
		$img  = service('member/Member')->getAvatar($info['avatar'], $info['sex']);
		$nick = htmlspecialchars($info['nick_name'], ENT_QUOTES, 'UTF-8');
		$mid  = htmlspecialchars($info['mem_id'], ENT_QUOTES, 'UTF-8');
		return '<div class="userAvatarInfo">'
			. '<span class="avatarStyle"><img src="' . $img . '"></span>'
			. '<span class="two"><p>' . $nick . '</p><p>' . $mid . '</p></span>'
			. '</div>';
	}
}