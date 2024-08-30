<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Order extends HomeBase
{
	public function index()
	{	
		$status = (int)iget('status');
		$page = iget('page', 1);
		$size = iget('size', 10);

		frame('Html')->addCss();
		frame('Html')->addJs();

		if (userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>0];
			if ($status) {
				$where['status'] = $status;
			}
			$list = service('order/Order')->getList($where, $page, $size);
		}

		$this->assign('list', $list ?? []);
		$this->assign('status', $status);
		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('_title', appT('my_order'));
		$this->view();
	}

	public function getOrderListAjax()
	{
		$status = (int)ipost('status');
		$page = (int)ipost('page', 1);
		$size = (int)ipost('size', 10);
		$isDelete = (int)ipost('is_delete', 0);
		if (userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>$isDelete];
			if ($status) {
				$where['status'] = $status;
			}
			$list = service('order/Order')->getList($where, $page, $size);
		}
		$this->success($list ?? [], '');
	}

	public function repurchase()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		if (!service('order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$id])) {
			$this->error(appT('order_error'));
		}
		$list = service('order/Product')->getListData(['order_id'=>$id], 'sku_id,quantity');
		$cartService = service('Cart');
		foreach ($list as $value) {
			$cartService->addToCart($value['sku_id'], $value['quantity']);
		}
		$this->success(['url'=>url('cart')], '');
	}

	public function delete()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		if (!$orderService->getCountData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>$orderService->getConst('STATUS_WAIT_PAY')])) {
			$this->error(appT('order_error'));
		}
		$rst = $orderService->updateData($id, ['is_delete'=>1]);
		if ($rst) {
			$this->success();
		} else {
			$this->error();
		}
	}

	public function refund()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		$info = $orderService->loadData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>$orderService->getConst('STATUS_PAIED')]);
		if (empty($info)) {
			$this->error(appT('order_error'));
		}
		$status = $orderService->getConst('STATUS_REFUNDING');
		$rst = $orderService->updateData($id, ['status'=>$status]);
		if ($rst) {
			service('order/StatusHistory')->addLog($id, $status, $info['lan_id']);
			$this->success();
		} else {
			$this->error();
		}
	}

	public function complete()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		$info = $orderService->loadData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>$orderService->getConst('STATUS_SHIPPED')]);
		if (empty($info)) {
			$this->error(appT('order_error'));
		}
		$status = $orderService->getConst('STATUS_FINISHED');
		$rst = $orderService->updateData($id, ['status'=>$status]);
		if ($rst) {
			service('order/StatusHistory')->addLog($id, $status, $info['lan_id']);
			$this->success();
		} else {
			$this->error();
		}
	}

	public function cancel()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		$info = $orderService->loadData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>$orderService->getConst('STATUS_WAIT_PAY')]);
		if (empty($info)) {
			$this->error(appT('order_error'));
		}
		$status = $orderService->getConst('STATUS_CANCEL');
		$rst = $orderService->updateData($id, ['status'=>$status]);
		if ($rst) {
			service('order/StatusHistory')->addLog($id, $status, $info['lan_id']);
			$this->success();
		} else {
			$this->error();
		}
	}

	public function getOrderAddress()
	{
		$orderId = (int)ipost('order_id');
		if (empty($orderId)) {
			$this->error('Sorry, we can\' find any order param here, Please try agin.');
		}
		if (!service('order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$orderId])) {
			$this->error('Sorry, we can\' find any order info here, Please try agin.');
		}
		$info = service('order/Address')->loadData(['order_id'=>$orderId, 'type'=>1]);
		$this->success($info, 'success');
	}

	public function editOrderAddress()
	{
		$orderId = (int)ipost('order_id');
		$country_code2 = ipost('country_code2');
		$tax_number = ipost('tax_number');
		$first_name = ipost('first_name');
		$last_name = ipost('last_name');
		$phone = ipost('phone');
		$postcode = ipost('postcode');
		$city = ipost('city');
		$zone_id = (int)ipost('zone_id');
		$state = ipost('state');
		$address1 = ipost('address1');
		$address2 = ipost('address2');
		if (empty($orderId)) {
			$this->error('Sorry, we can\' find any order param here, Please try agin.');
		}
		if (empty($country_code2)) {
			$this->error('Country is required');
		}
		if (empty($first_name)) {
			$this->error('First Name is required');
		}
		if (empty($phone)) {
			$this->error('Phone Number is required');
		}
		if (empty($postcode)) {
			$this->error('ZIP Code is required');
		}
		if (empty($city)) {
			$this->error('City is required');
		}
		if (empty($state)) {
			$this->error('State / Region is required');
		}
		if (empty($address1)) {
			$this->error('Address is required');
		}
		if (!service('order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$orderId])) {
			$this->error('Sorry, we can\' find any order info here, Please try agin.');
		}
		$data = [
			'country_code2' => substr($country_code2, 0, 2),
			'tax_number' => substr($tax_number, 0, 32),
			'first_name' => substr($first_name, 0, 32),
			'last_name' => substr($last_name, 0, 32),
			'postcode' => substr($postcode, 0, 10),
			'city' => substr($city, 0, 32),
			'zone_id' => $zone_id,
			'state' => substr($state, 0, 32),
			'address1' => substr($address1, 0, 64),
			'address2' => substr($address2, 0, 64),
		];
		$countryInfo = service('address/Country')->loadData($country_code2, 'dialing_code');
		$data['phone'] = '+'.$countryInfo['dialing_code'].' '.$phone;

		$rst = service('order/Address')->updateData(['order_id'=>$orderId, 'type'=>1], $data);
		if ($rst) {
			$this->success('Update your billing address success.');
		} else {
			$this->error('Update your billing address failed.');
		}
	}

	public function search()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$page = iget('page', 1);
		$size = iget('size', 10);
		$keyword = trim(iget('keyword'));

		if (userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>0];
			$list = service('order/Order')->getListByKeyword($where, $keyword, $page, $size);
		}

		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('keyword', $keyword);
		$this->assign('list', $list ?? []);
		$this->assign('_title', appT('order_search'));
		$this->view();
	}

	public function getSearchOrderListAjax()
	{
		$page = iget('page', 1);
		$size = iget('size', 10);
		$keyword = trim(iget('keyword'));

		if (!empty($keyword)) {
			$where = ['mem_id'=>userId(), 'is_delete'=>0];
			$list = service('order/Order')->getListByKeyword($where, $keyword, $page, $size);
		}
		$this->success($list ?? [], '');
	}

	public function trash()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$page = iget('page', 1);
		$size = iget('size', 10);

		if (userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>1];
			$list = service('order/Order')->getList($where, $page, $size);
		}

		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->assign('_title', appT('order_trash'));
		$this->view();
	}

	public function detail()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$id = (int)iget('id');
		if (empty($id)) {
			$error = appT('order_error');
		} else {
			$orderService = service('order/Order');
			$orderInfo = $orderService->getInfo($id);
			if (empty($orderInfo)) {
				$error = appT('order_error');
			} else {
				//检查订单状态
				if ($orderInfo['base']['status'] == $orderService->getConst('STATUS_WAIT_PAY')) {
					if ($orderInfo['base']['add_time'] < now(time()-$orderService->getConst('ORDER_WAIT_PAY_TIME'))) {
						//取消订单
						$status = $orderService->getConst('STATUS_CANCEL');
						$rst = $orderService->updateData($id, ['status'=>$status]);
						if ($rst) {
							service('order/StatusHistory')->addLog($id, $status, $orderInfo['base']['lan_id']);
						}
						$orderInfo = $orderService->getInfo($id);
					}
					$orderInfo['base']['last_pay_time'] = (int)((strtotime($orderInfo['base']['add_time']) + $orderService->getConst('ORDER_WAIT_PAY_TIME') - time()) / 86400);
				}
			}
		}
		$this->assign('orderInfo', $orderInfo ?? []);
		$this->assign('_title', appT('order_detail'));
		$this->view();
	}
}