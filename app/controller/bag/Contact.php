<?php

namespace app\controller\bag;
use app\controller\;

class Contact extends 
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$this->assign('_title', site()->getName().' - Contact Us');
		$this->view();
	}

	public function create()
	{
		$service = make('app/service/message/Message');
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
		$list = make('app/service/message/Message')->getListData(['group_key'=>$key], '*', $page, $size, ['add_time'=>'desc']);
		if (!empty($list)) {
			$memId = userId();
			$userInfo = $value['avatar'] = session()->get(APP_TEMPLATE_TYPE.'_info');
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