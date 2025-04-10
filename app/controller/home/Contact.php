<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Contact extends HomeBase
{
	public function index()
	{	
		frame('Html')->addCss();
		frame('Html')->addJs();

		$this->assign('_title', appT('contact_us'));
		$this->view();
	}

	public function create()
	{
		$service = service('message/Message');
		$key = $service->createGroup(userId(), $service->getSystemId());
		if ($key) {
			$this->success($key, '');
		} else {
			$this->error('contact false');
		}
	}

	public function message()
	{
		$key = ipost('key');
		$page = ipost('page', 0);
		$size = ipost('size', 20);
		if (empty($key)) {
			$this->error('contact key empty.');
		}
		$list = service('message/Message')->getListData(['group_key'=>$key], '*', $page, $size, ['add_time'=>'desc']);
		if (!empty($list)) {
			$memId = userId();
			$userInfo = $value['avatar'] = session()->get(type().'_info');
			foreach ($list as $key => $value) {
				if ($memId == $value['mem_id']) {
					$value['is_self'] = true;
					$value['avatar'] = $userInfo['avatar'] ?? '';
					$value['name'] = $userInfo['name'] ?? '';
				} else {
					$value['is_self'] = false;
					$value['avatar'] = '';
					$value['name'] = site()->getName();
				}
				$list[$key] = $value;
			}
		}
		$this->success($list, '');
	}
}