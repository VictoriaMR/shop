<?php

namespace app\controller\admin;
use app\controller\Base;

class Payment extends Base
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '支付账号管理',
		];
		$this->_default = '支付管理';
	}

	public function index()
	{	
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}
		html()->addJs();

		$type = iget('type');
		$isSandbox = iget('is_sandbox', -1);
		$name = iget('name');
		$page = iget('page', 1);
		$size = iget('size', 20);

		$paymentService = make('app/service/payment/Payment');
		$where = [];
		if (!empty($type)) {
			$where['type'] = (int) $type;
		}
		if ($isSandbox >= 0) {
			$where['is_sandbox'] = (int) $isSandbox;
		}
		if (!empty($name)) {
			$where['is_sandbox'] = ['like', '%'.trim($name).'%'];
		}

		$total = $paymentService->getCountData($where);

		if ($total > 0) {
			$list = $paymentService->getList($where, $page, $size);
		}

		$typeList = $paymentService->getTypeList();
		$sandBoxList = $paymentService->getSandBoxList();

		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->assign('type', $type);
		$this->assign('isSandbox', $isSandbox);
		$this->assign('name', $name);
		$this->assign('typeList', $typeList);
		$this->assign('sandBoxList', $sandBoxList);
		$this->_init();
		$this->view();
	}

	protected function getInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = make('app/service/payment/Payment')->loadData($id, 'app_key,secret_key,webhook_key');
		if (empty($info)) {
			$this->error('找不到数据');
		}
		$this->success($info, '');
	}
}