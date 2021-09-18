<?php

namespace app\controller\bag;
use app\controller\Base;

class Order extends Base
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		
		$list = $this->getOrderList();

		$this->assign('list', $list);
		$this->assign('status', input('status'));
		$this->assign('page', input('page', 1));
		$this->assign('size', input('size', 10));
		$this->assign('_title', appT('my_order'));
		$this->view();
	}

	protected function getOrderList()
	{
		$status = (int)input('status');
		$page = input('page', 1);
		$size = input('size', 10);

		$where = ['mem_id'=>userId(), 'is_delete'=>0];
		if ($status) {
			$where['status'] = $status;
		}
		return make('app/service/order/Order')->getList($where, $page, $size);
	}

	public function getOrderListAjax()
	{
		$this->success($this->getOrderList(), '');
	}

	public function repurchase()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		if (!make('app/service/order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$id])) {
			$this->error(appT('order_error'));
		}
		$list = make('app/service/order/Product')->getListData(['order_id'=>$id], 'sku_id,quantity');
		$cartService = make('app/service/Cart');
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
		$orderService = make('app/service/order/Order');
		if (!$orderService->getCountData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>1])) {
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
		$orderService = make('app/service/order/Order');
		$info = $orderService->loadData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>2]);

		if (empty($info)) {
			$this->error(appT('order_error'));
		}
		$rst = $orderService->updateData($id, ['status'=>7]);
		if ($rst) {
			make('app/service/order/StatusHistory')->addLog($id, 7, $info['lan_id']);
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
		if (!make('app/service/order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$orderId])) {
			$this->error('Sorry, we can\' find any order info here, Please try agin.');
		}
		$info = make('app/service/order/Address')->loadData(['order_id'=>$orderId, 'type'=>1]);
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
		if (!make('app/service/order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$orderId])) {
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
		$countryInfo = make('app/service/address/Country')->loadData($country_code2, 'dialing_code');
		$data['phone'] = '+'.$countryInfo['dialing_code'].' '.$phone;

		$rst = make('app/service/order/Address')->updateData(['order_id'=>$orderId, 'type'=>1], $data);
		if ($rst) {
			$this->success('Update your billing address success.');
		} else {
			$this->error('Update your billing address failed.');
		}
	}

	public function search()
	{
		$this->assign('_title', appT('order_search'));
		$this->view();
	}

	public function trash()
	{
		$this->assign('_title', appT('order_trash'));
		$this->view();
	}
}