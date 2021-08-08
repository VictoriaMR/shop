<?php

namespace app\controller\bag;
use app\controller\Controller;

class UserInfoController extends Controller
{
	public function index()
	{	
		$this->view();
	}

	public function collect()
	{
		$spuId = ipost('spu_id', 0);
		if (empty($spuId)) {
			$this->error('param error');
		}
		$rst = make('app/service/member/CollectService')->collectProduct($spuId);
		if ($rst) {
			$this->success($rst, 'success');
		} else {
			$this->error('collect failed');
		}
	}
}