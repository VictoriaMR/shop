<?php

namespace app\controller;

class AdminBase extends Base
{
	protected $_init = true;
	protected $_nav = [];
	protected $_ignore = [];
	protected $_arr = [];
	protected $_default = '';

	protected function _init(): void
	{
		$router = \App::get('router');
		$path = (string)($router['path'] ?? 'Index');
		$func = (string)($router['func'] ?? 'index');

		$permService = service('controller/Permission');
		if (!$permService->hasAccess($path, $func)) {
			if (isAjax() || isPost()) {
				$this->error('抱歉，您没有该功能的访问权限');
			}
			$this->renderPermissionDenied($path, $func);
		}

		$allowedTabs = $permService->filterControllerTabs($path, $this->_arr);
		$data = [
			'_path' => $path,
			'_func' => $func,
		];
		if (isset($this->_arr[$func])) {
			$data['_title'] = $this->_arr[$func];
		}

		if ($path !== 'Index') {
			$this->_nav = $this->_default !== ''
				? ['default' => $this->_default] + $this->_arr
				: $this->_arr;
			$data['_ignore'] = $this->_ignore;
			$data['_tag']    = $allowedTabs;
			$data['_nav']    = $this->_nav;
		}
		frame('View')->setData($data);
	}

	protected function renderPermissionDenied(string $path, string $func): void
	{
		$title = $this->_arr[$func] ?? ($path . '/' . $func);
		frame('View')->setData([
			'_path'  => $path,
			'_func'  => $func,
			'_title' => '无访问权限',
			'_nav'   => $this->_default !== '' ? ['default' => $this->_default, $func => $title] : [],
		]);
		frame('View')->load('common/header');
		echo '<div class="container-fluid" style="margin-top: 30px;">'
			. '<div class="alert alert-danger" style="padding: 20px; border-radius: 6px;">'
			. '<h4 style="margin-top: 0; font-weight: bold;"><i class="fa fa-lock"></i> 抱歉，您没有该功能的访问权限</h4>'
			. '<p style="margin-bottom: 0; color: #7f1d1d;">当前请求模块：<code>' . htmlspecialchars($path . '/' . $func, ENT_QUOTES, 'UTF-8') . '</code>，如需开通请联系管理员授权。</p>'
			. '</div></div>';
		frame('View')->load('common/footer');
		\App::runOver();
	}

	public function addLog($info = '', int $type = 0)
	{
		return service('log/Member')->addLog($info, $type);
	}

	protected function avatar($info)
	{
		if (empty($info)) {
			return '';
		}
		$img  = service('member/Member')->getAvatar($info['avatar'] ?? '', $info['sex'] ?? 0);
		$nick = htmlspecialchars($info['nick_name'] ?? '', ENT_QUOTES, 'UTF-8');
		$mid  = htmlspecialchars((string)($info['mem_id'] ?? ''), ENT_QUOTES, 'UTF-8');
		$avatarHtml = ($img !== '' && (!isMobile() || strpos($img, 'image/common/') === false))
			? '<img src="' . $img . '">'
			: '<i class="fa fa-user"></i>';
		return '<div class="userAvatarInfo">'
			. '<span class="avatarStyle">' . $avatarHtml . '</span>'
			. '<span class="two"><p>' . $nick . '</p><p>' . $mid . '</p></span>'
			. '</div>';
	}
}