<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Order extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '订单列表',
		];
		$this->_default = '订单管理';
		parent::_init();
	}

	public function index()
	{
		$page = iget('page', 1);
		$size = iget('size', 20);
		$siteId = iget('site_id', 0);
		$status = iget('status', -1);
		$paymentId = iget('payment_id', 0);
		$orderId = iget('order_id', '');
		$email = iget('email', '');
		$stime = iget('stime', '');
		$etime = iget('etime', '');

		$where = [];
		if ($siteId) {
			$where['site_id'] = $siteId;
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}
		if ($paymentId) {
			$where['payment_id'] = $paymentId;
		}
		if ($orderId) {
			$where['order_id'] = $orderId;
		}
		if ($email) {
			$memList = service('member/Member')->getListData(['email'=>$email], 'mem_id');
			if (empty($memList)) {
				$where = ['order_id' => 0];
			} else {
				$where['mem_id'] = ['in', array_column($memList, 'mem_id')];
			}
		}
		if ($stime) {
			$where['add_time'] = ['>=', $stime];
		}
		if ($etime) {
			$where['add_time'] = ['<=', $etime];
		}
		$siteList = service('site/Site')->getListData([], 'site_id,name');
		$siteList = array_column($siteList, 'name', 'site_id');
		//状态列表
		$orderService = service('order/Order');
		$statusList = $orderService->orderStatus();
		//支付账号
		$paymentAccount = service('payment/Payment')->getList([], 0);
		$paymentAccount = array_map(function($value){
			$value['name'] = $value['type_name'].' - '.$value['name'];
			return $value;
		}, $paymentAccount);
		$paymentAccount = array_column($paymentAccount, 'name', 'payment_id');

		$total = $orderService->getCountData($where);
		if ($total > 0) {
			$list = $orderService->getListData($where, '*', $page, $size);
		}

		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->assign('site_id', $siteId);
		$this->assign('status', $status);
		$this->assign('payment_id', $paymentId);
		$this->assign('order_id', $orderId);
		$this->assign('email', $email);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);
		$this->assign('statusList', $statusList);
		$this->assign('paymentAccount', $paymentAccount);
		$this->view();
	}
}